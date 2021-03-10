[{oxstyle include=$oViewConf->getModuleUrl('unzercw', 'out/src/css/payment_form.css')}]

[{oxscript include=$oViewConf->getModuleUrl('unzercw','out/src/js/checkout.js')}]
[{oxscript add="$(document).ready(function() { var defaultTemplateCheckoutHandler = window['unzercw_checkout_processor']; defaultTemplateCheckoutHandler.init('$processingLabel', '$selfUrl', '$aliasUrl', '$paymentMethodId', '$transactionId'); });"}]

[{capture append="oxidBlock_content"}]
	<form action="[{$formActionUrl}]" method="post" id="unzercwPaymentForm" accept-charset="UTF-8" class="form-horizontal">
		<div class="unzercw-payment-form">
			[{if !empty($hiddenFormFields)}]
				<div class="unzercw-hiddenFormFields">
					[{$hiddenFormFields}]
				</div>
			[{/if}]
			
			[{if !empty($visibleFormFields) || !empty($aliasFormFields)}]
				<div class="panel panel-default">
					<div class="panel-heading">[{oxmultilang ident="Your Payment Information"}]</div>
					<div class="panel-body">
						<div class="unzercw-alias-form-fields">
							[{$aliasFormFields}]
						</div>
						
						<div class="unzercw-visible-form-fields">
							[{$visibleFormFields}]
						</div>
					</div>
				</div>
			[{/if}]
		</div>
		
		<div class="well well-sm">
            <a href="[{$previousUrl}]" class="btn btn-default pull-left prevStep submitButton largeButton"><i class="fa fa-caret-left"></i> [{oxmultilang ident="PREVIOUS_STEP"}]</a>
            <button type="submit" class="btn btn-primary pull-right submitButton nextStep largeButton" id="submitButton">[{oxmultilang ident="SUBMIT_ORDER"}] <i class="fa fa-caret-right"></i></button>
            <div class="clearfix"></div>
        </div>
	</form>
[{/capture}]
[{include file="layout/page.tpl"}]