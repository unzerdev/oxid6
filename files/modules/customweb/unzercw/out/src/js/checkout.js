(function($){
	window['unzercw_checkout_processor'] = {
		init: function(processingLabel, selfUrl, visibleFieldsUrl, paymentMethod, transactionId) {
			this.processingLabel = processingLabel;
			this.selfUrl = selfUrl;
			this.visibleFieldsUrl = visibleFieldsUrl;
			this.paymentMethod = paymentMethod;
			this.transactionId = transactionId;
			
			if (typeof window['unzercw_backup_object'] !== 'undefined') {
				var backup = window['unzercw_backup_object'];
				this.paymentMethod = backup.paymentMethod;
				this.skipValidation = backup.skipValidation;
				this.dynamicFormFields = backup.dynamicFormFields;
				this.aliasId = backup.aliasId;
			}
			
			this.attachListeners();
			
			return this;
		},
		
		/**
		 * The method name of the currently selected method.
		 * 
		 * @return boolean|string
		 */
		paymentMethod: false,
		
		/**
		 * The content of the form fields.
		 * 
		 * @return boolean|array
		 */
		dynamicFormFields: false,
		
		/**
		 * Enforce skipping the form validation.
		 * 
		 * @return boolean
		 */
		skipValidation: false,
		
		/**
		 * Removes on the payment list selection page all form field names to prevent the sending of the directly to the server.
		 * 
		 * @return void
		 */
		removeFormFieldNames: function() {
			var submittableTypes = ['select', 'input', 'button', 'textarea'];
			for(var i = 0; i < submittableTypes.length; i++) {
				$('.unzercw-payment-form ' + submittableTypes[i] + '[name]').each(function (element) {
					$(this).attr('data-field-name', $(this).attr('name'));
					$(this).removeAttr('name');
				});
			}
		},
		
		/**
		 * This method returns the form fields loaded by JavaScript. These fields should not be
		 * send to the shopping cart.
		 * 
		 * @return List<Object>
		 */
		getDynamicFormValues: function(removeHidden) {
			if (this.dynamicFormFields !== false) {
				return this.dynamicFormFields;
			}
			var output = {};
			$('.unzercw-payment-form').find('*[data-field-name]').each($.proxy(function (key, element) {
				var name = $(element).attr('data-field-name');
				if (removeHidden && $(element).attr('type') == 'hidden' && !$(element).attr('originalelement')) {
					return;
				}
				if (name.indexOf(this.paymentMethod) === 0) {
					var fieldName = name.substring(this.paymentMethod.length + 1, name.length).replace(']', '');
					output[fieldName] = $(element).val();
				} else {
					output[name] = $(element).val();
				}
			}, this));
			return output;
		},
		
		getDynamicFormValuesPaymentList: function() {
			var output = {};
			if (this.paymentMethod !== false) {
				$('.' + this.paymentMethod + '-form').find('*[data-field-name]').each($.proxy(function (key, element) {
					var name = $(element).attr('data-field-name');
					if (name.indexOf(this.paymentMethod) === 0) {
						var fieldName = name.substring(this.paymentMethod.length + 1, name.length).replace(']', '');
						output[fieldName] = $(element).val();
					}
				}, this));
			}
			return output;
		},
		
		renderDataAsHiddenFields: function(data) {
			var me = this,
				output = '';
			$.each(data, function(key, value) {
				if ($.isArray(value)) {
					for (var i = 0; i < value.length; i++) {
						output += me.renderHiddenField(key + '[]', value[i]);
					}
				} else {
					output += me.renderHiddenField(key, value);
				}
			});
			return output;
		},
		
		renderHiddenField: function(key, value) {
			if (typeof value == 'string') {
				value = value.replace(/"/g, "&quot;");
			}
			return '<input type="hidden" name="' + key + '" value="' + value + '" />';
		},
		
		/**
		 *  This method validates the confirmation form.  
		 * 
		 * @param successCallback This function is called when the validation was successful
		 * @param failureCallback This function is called when the validation fails
		 */
		validatePaymentForm: function(successCallback, failureCallback) {
			if (this.skipValidation) {
				successCallback(new Array());
				return;
			}

			if (this.paymentMethod !== false) {
				var validationCallback = window['cwValidateFields'+this.paymentMethod];
				if (typeof validationCallback !== 'undefined') {
					validationCallback(successCallback, failureCallback);
					return
				}
				else {
					successCallback(new Array());
					return;
				}
			}
			else {
				if (typeof cwValidateFields !== 'undefined') {
					cwValidateFields(successCallback, failureCallback);
					return;
				}
				else {
					successCallback(new Array());
					return;
				}
			}
			
		},
		
		attachListeners: function() {
			$('*').unbind('.unzercw');
			this.attachConfirmationSubmitHandler();
			this.attachSeparateSubmitHandler();
			this.attachAliasSelectHandler();
			this.attachPaymentMethodSelectHandler();
			this.attachPaymentFormSubmitHandler();
			this.removeFormFieldNames();
			
			$(document).on('customweb.ready', function(){
				$('.unzercw-payment-form input[type="hidden"]').on('validation.validation', function(){
					return [];
				});
			});
		},
		
		attachConfirmationSubmitHandler: function() {
			this.getConfirmationFormElement().bind('submit.unzercw', $.proxy(function(event) {
				var rs = this.handleConfirmationFormSubmitEvent();
				if (rs === false) {
					event.preventBubble = true;
				}
				return rs;
			}, this));
		},
		
		attachSeparateSubmitHandler: function() {
			this.getSeparateFormElement().bind('submit.unzercw', $.proxy(function(event) {
				var rs = this.handleSeparateFormSubmitEvent();
				if (rs === false) {
					event.preventBubble = true;
				}
				return rs;
			}, this));
		},
		
		attachAliasSelectHandler: function() {
			this.getAliasSelectElement().attr('name', '').attr('data-field-name', '');
			this.getAliasSelectElement().bind('change.unzercw', $.proxy(function(){
				this.loadAliasData();
			}, this));
		},
		
		attachPaymentMethodSelectHandler: function() {
			$('#payment input[name="paymentid"]').bind('click.unzercw', $.proxy(function(){
				this.paymentMethod = $('#payment input[name="paymentid"]:checked').val();
				this.aliasId = null;
			}, this));
			$('#payment input[name="paymentid"]:checked').trigger('click');
		},
		
		attachPaymentFormSubmitHandler: function() {
			this.getPaymentFormElement().bind('submit.unzercw', $.proxy(function(event) {
				var rs = this.handlePaymentFormSubmitEvent();
				if (rs === false) {
					event.preventBubble = true;
				}
				return rs;
			}, this));
		},
		
		handleConfirmationFormSubmitEvent: function() {
			if (!this.isUnzerCwPaymentMethod()) {
				return true;
			}
			
			this.setLoadingIndicator(true);
			this.blockUI();
			this.cleanUpErrorMessages();
			var _this = this;
			this.validatePaymentForm(
					function(valid){_this.handleConfirmationFormSubmitEventValidationSuccess();},
					function(errors, valid){_this.handleConfirmationFormSubmitEventValidationFailure(errors, valid);}
			);
				
			return false;
		},
		
		handleConfirmationFormSubmitEventValidationSuccess: function() {
			$.ajax({
				type: 'POST',
				url: this.selfUrl,
				data: this.createDataForConfirmationAjaxCall(),
				success: $.proxy(function(response){
					this.handleConfirmationAjaxResponse(response);
				}, this)
			});
		},
		
		handleConfirmationFormSubmitEventValidationFailure: function(errors, valid) {
			alert(errors[Object.keys(errors)[0]]);
			this.unblockUI();
			this.setLoadingIndicator(false);
		},
		
		handlePaymentFormSubmitEvent: function() {
			if (!this.isUnzerCwPaymentMethod() || $('.' + this.paymentMethod + '-form').length == 0) {
				return true;
			}
			
			this.setLoadingIndicator(true);
			this.blockUI();
			this.cleanUpErrorMessages();
			var _this = this;
			this.validatePaymentForm(
					function(valid){_this.handlePaymenFormSubmitEventValidationSuccess();},
					function(errors, valid){_this.handlePaymenFormSubmitEventValidationFailure(errors, valid);}
			);
			
			return false;

		},
		
		handlePaymenFormSubmitEventValidationSuccess: function() {
			
			this.dynamicFormFields = this.getDynamicFormValuesPaymentList();
			
			$.ajax({
				type: 'POST',
				url: this.getPaymentFormElement().attr('action'),
				data: this.createDataForPaymentAjaxCall(),
				success: $.proxy(function(response){
					this.handlePaymentAjaxResponse(response);
				}, this)
			});
			
		},
		
		handlePaymenFormSubmitEventValidationFailure: function(errors, valid) {
			alert(errors[Object.keys(errors)[0]]);
			this.unblockUI();
			this.setLoadingIndicator(false);
		},
		
		handleSeparateFormSubmitEvent: function() {
			this.setLoadingIndicator(true);
			this.blockUI();
			this.cleanUpErrorMessages();
			var _this = this;
			this.validatePaymentForm(
					function(valid){_this.handleSeparateFormSubmitEventValidationSuccess();},
					function(errors, valid){_this.handleSeparateFormSubmitEventValidationFailure(errors, valid);}
			);
			
			return false;
		},
		
		handleSeparateFormSubmitEventValidationSuccess: function() {
			
			$.ajax({
				type: 'POST',
				url: this.selfUrl,
				data: this.createDataForSeparateAjaxCall(),
				success: $.proxy(function(response){
					this.handleSeparateAjaxResponse(response);
				}, this)
			});

		},
		handleSeparateFormSubmitEventValidationFailure: function(errors, valid) {
			alert(errors[Object.keys(errors)[0]]);
			this.unblockUI();
			this.setLoadingIndicator(false);
		},
		
		createDataForConfirmationAjaxCall: function() {
			var parameters = {};
			$.each(this.getConfirmationFormElement().serializeArray(), function(index, item){
				parameters[item.name] = item.value;
			});
			if (this.aliasId) {
				parameters['unzercw_alias_id'] = this.aliasId;
			}
			parameters['cl'] = 'order';
			parameters['fnc'] = 'execute';
			parameters['unzercw_create_order'] = 'active';
			return parameters;
		},
		
		createDataForPaymentAjaxCall: function() {
			var parameters = {};
			if ($('.' + this.paymentMethod + '-form').data('authorization-method') != 'HiddenAuthorization'
				&& $('.' + this.paymentMethod + '-form').data('authorization-method') != 'AjaxAuthorization'
				&& $('.' + this.paymentMethod + '-form').data('authorization-method') != 'ServerAuthorization') {
				parameters = this.getDynamicFormValuesPaymentList();
			}
			$.each(this.getPaymentFormElement().serializeArray(), function(index, item){
				parameters[item.name] = item.value;
			});
			if (this.aliasId) {
				parameters['unzercw_alias_id'] = this.aliasId;
			}
			return parameters;
		},
		
		createDataForSeparateAjaxCall: function() {
			var parameters = {};
			$.each(this.getSeparateFormElement().serializeArray(), function(index, item){
				parameters[item.name] = item.value;
			});
			if (this.aliasId) {
				parameters['unzercw_alias_id'] = this.aliasId;
			}
			parameters['cl'] = 'unzercw_payment';
			parameters['fnc'] = 'pay';
			parameters['unzercw_transaction_id'] = this.transactionId;
			return parameters;
		},
		
		handleConfirmationAjaxResponse: function(response) {
			try {
				var objects = $.parseJSON(response);
			}
			catch(e) {
				this.handleConfirmationAjaxFailure(response);
				this.unblockUI();
				this.setLoadingIndicator(false);
				return false;
			}
			
			if (objects.success) {
				this.startPayment(objects);
			} else {
				document.location = this.selfUrl + "cl=" + objects.controller;
			}
		},
		
		handlePaymentAjaxResponse: function (response) {
			var content = $(response);
			if (content.find('#payment').length > 0) {
				this.handleAjaxFailure(response, '#payment');
				this.unblockUI();
				this.setLoadingIndicator(false);
				return;
			}
			
			this.skipValidation = true;
			backup = this;
			
			var backupWindow = new Object();
			if(this.hasIssueWithDocumentOpen()){
				for (var key in window) {
					backupWindow[key] = window[key];
				}
			}
			document.open('text/html');
			document.write(response);
			window['unzercw_backup_object'] = backup;
			if(this.hasIssueWithDocumentOpen()){
				for (var key in backupWindow) {
					if(!(key in window)){
						window[key] = backupWindow[key];
					}
				}
			}
			document.close();
		},
		
		hasIssueWithDocumentOpen: function() {
			var uaString = navigator.userAgent;
			var match = uaString.match(/Firefox\/([0-9]+)\./);
			var ver = match ? parseInt(match[1]) : 0;
			if(ver == 66) {
				return true;
			}
			return false;
		},
		
		handleSeparateAjaxResponse: function(response) {
			try {
				var objects = $.parseJSON(response);
			}
			catch(e) {
				this.handleAjaxFailure(response, '#unzercwPaymentForm');
				this.unblockUI();
				this.setLoadingIndicator(false);
				return false;
			}
			
			this.startPayment(objects);
		},
		
		handleAjaxFailure: function(response, container) {
			var content = $(response);
			var errors = content.find('.alert.alert-danger');
			if (errors.length > 0) {
				var html = '<div class="alert alert-danger unzercw-error-list">' + errors.html() + "</div>";
				$(container).before(html);
				$(window).scrollTop(0);
			}
			else {
				alert(response);
			}
		},
		
		handleConfirmationAjaxFailure: function(response) {
			var content = $(response);
			var errors = content.find('.alert.alert-danger');
			var alerts = content.find('.alert.alert-error');
			if (errors.length > 0) {
				$('.unzercw-error-list').remove();
				var html = '<div class="alert alert-danger unzercw-error-list">' + errors.html() + "</div>";
				$('.checkoutSteps').before(html);
				$(window).scrollTop(0);
			}
			else if(alerts.length > 0){
				$('.unzercw-error-list').remove();
				var html = '<div class="alert alert-error unzercw-error-list">' + alerts.html() + "</div>";
				$('.steps').before(html);
				$(window).scrollTop(0);
			}
			else {
				alert(response);
			}
		},
		
		startPayment: function(response) {
			if (response.authorizationMethod == 'AjaxAuthorization') {
				$.getScript(response.ajaxScriptUrl, $.proxy(function(){
					eval("var callbackFunction = " + response.ajaxSubmitCallback);
					callbackFunction(this.getDynamicFormValues());
				}, this));
			} else if (response.authorizationMethod == 'HiddenAuthorization') {
				this.sendDataAsForm(response.formActionUrl, $.extend(this.getDynamicFormValues(true), response.hiddenFields));
			} else {
				this.sendDataAsForm(response.formActionUrl, this.getDynamicFormValues());
			}
		},
		
		sendDataAsForm: function(url, values) {
			var newForm = '<form id="unzercw_redirect_form" action="' + url + '" method="POST">';
			newForm += this.renderDataAsHiddenFields(values);
			newForm += '</form>';
			$('body').append(newForm);
			$('#unzercw_redirect_form').submit();
		},
		
		loadAliasData: function() {
			this.setLoadingIndicator(true);
			this.blockUI();
			$.getJSON(this.visibleFieldsUrl, {
				unzercw_alias_id: this.getAliasSelectElement().val(),
				unzercw_payment_id: this.paymentMethod
			}, $.proxy(function(response){
				this.setLoadingIndicator(false);
				this.unblockUI();
				
				if (response.formActionUrl) {
					this.getConfirmationFormElement().attr('action', response.formActionUrl);
				}
				if($('.'+this.paymentMethod+'-form').length){
				    $('.'+this.paymentMethod+'-form .unzercw-visible-form-fields').html(response.visibleFormFields);
				    if (response.hiddenFormFields) {
					$('.'+this.paymentMethod+'-form .unzercw-hidden-form-fields').html(response.hiddenFormFields);
				    }
				}
				else{
				    $('.unzercw-visible-form-fields').html(response.visibleFormFields);
				    if (response.hiddenFormFields) {
					$('.unzercw-hidden-form-fields').html(response.hiddenFormFields);
				    }    
				}
				
				this.removeFormFieldNames();
				this.aliasId = this.getAliasSelectElement().val();
			}, this));
		},

		setLoadingIndicator: function(flag) {
			if (flag) {
				this.getSubmitElement().hide();
				this.getSubmitElement().after('<div class="ajax_loader loadingicon unzercw-loader">' + this.processingLabel + '</div>');
			} else {
				this.getSubmitElement().show();
				$('.unzercw-loader').remove();
			}
		},
		
		/**
		 * This method returns an element which should be blocked during AJAX calls.
		 * 
		 * @return Object
		 */
		getBlockingElement: function() {
			/*if (this.currentSelectedPaymentMethod !== false) {
				var paymentBlock = $('.unzercw-payment-block[data-module-code="' + this.currentSelectedPaymentMethod + '"]');
				if (paymentBlock.length) {
					return paymentBlock;
				}
			}*/
			
			var blockingElement = this.getConfirmationFormElement() || this.getSeparateFormElement();
			if (typeof blockingElement === 'undefined' || blockingElement.length <= 0) {
				return $('body');
			}
			else {
				return blockingElement;
			}
		},
		
		/**
		 * This method blocks the user from entering other data. This method should be called
		 * before any AJAX call is executed.
		 * 
		 * @return void
		 */
		blockUI: function() {
			var element = this.getBlockingElement();
			var height = element.outerHeight();
			var width = element.outerWidth();
			var offset = element.position();
			element.append('<div class="unzercw-ajax-overlay"></div>');
			element.find('.unzercw-ajax-overlay').css('border-radius', element.css('border-radius')).height(height).width(width).css({top: offset.top, left: offset.left, position:'absolute'}).fadeTo(100, 0.3);
		},
		
		/**
		 * This method unblocks the user interface and allows the user do do any user action.
		 * 
		 * @return void
		 */
		unblockUI: function() {
			this.getBlockingElement().find('.unzercw-ajax-overlay').remove();
		},
		
		cleanUpErrorMessages: function() {
			$('.alert.alert-danger').remove();
		},
		
		isUnzerCwPaymentMethod: function() {
			return /^unzercw_/.test(this.paymentMethod);
		},
		
		getSubmitElement: function() {
			return $('#orderConfirmAgbTop .submitButton.nextStep, #orderConfirmAgbBottom .submitButton.nextStep, #paymentNextStepBottom, #unzercwPaymentForm .submitButton.nextStep');
		},
		
		getConfirmationFormElement: function() {
			return $('#orderConfirmAgbTop, #orderConfirmAgbBottom');
		},
		
		getPaymentFormElement: function() {
			return $('#payment');
		},
		
		getSeparateFormElement: function() {
			return $('#unzercwPaymentForm');
		},
		
		getAliasSelectElement: function() {
			return $('.unzercw-alias-form-fields select');
		}
	};
})(jQuery);