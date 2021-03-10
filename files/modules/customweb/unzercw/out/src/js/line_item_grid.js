(function($) {
	
	var UnzerCwLineItemGrid = {
		decimalPlaces: 2,
		currencyCode: 'EUR',
		
		init: function() {
			this.decimalPlaces = parseFloat($("#unzercw-decimal-places").val());
			this.currencyCode = $("#unzercw-currency-code").val();
			this.attachListeners();
		},
		
		attachListeners: function() {
			$(".unzercw-line-item-grid input.line-item-quantity").each(function() {
				UnzerCwLineItemGrid.attachListener(this);
			});
			$(".unzercw-line-item-grid input.line-item-price-excluding").each(function() {
				UnzerCwLineItemGrid.attachListener(this);
			});
			$(".unzercw-line-item-grid input.line-item-price-including").each(function() {
				UnzerCwLineItemGrid.attachListener(this);
			});
		},
		
		attachListener: function(element) {
			$(element).change(function() {
				UnzerCwLineItemGrid.recalculate(this);
			});
			
			$(element).attr('data-before-change', $(element).val());
			$(element).attr('data-original', $(element).val());
		},
		
		recalculate: function(eventElement) {
			var lineItemIndex = $(eventElement).parents('tr').attr('data-line-item-index');
			var row = $('.unzercw-line-item-grid tr[data-line-item-index="' + lineItemIndex + '"]');
			var taxRate = parseFloat(row.find('input.tax-rate').val());

			var quantityBefore = parseFloat(row.find('input.line-item-quantity').attr('data-before-change'));
			var quantityValue = row.find('input.line-item-quantity').val();
			if (quantityValue == '' || isNaN(quantityValue)) {
				var quantity = quantityBefore;
			} else {
				var quantity = parseFloat(quantityValue);
			}
			
			var priceExcludingBefore = parseFloat(row.find('input.line-item-price-excluding').attr('data-before-change'));
			var priceExcludingValue = row.find('input.line-item-price-excluding').val();
			if (priceExcludingValue == '' || isNaN(priceExcludingValue)) {
				var priceExcluding = priceExcludingBefore;
			} else {
				var priceExcluding = parseFloat(priceExcludingValue);
			}

			var priceIncludingBefore = parseFloat(row.find('input.line-item-price-including').attr('data-before-change'));
			var priceIncludingValue = row.find('input.line-item-price-including').val();
			if (priceIncludingValue == '' || isNaN(priceIncludingValue)) {
				var priceIncluding = priceIncludingBefore;
			} else {
				var priceIncluding = parseFloat(priceIncludingValue);
			}
			
			if ($(eventElement).hasClass('line-item-quantity')) {
				if (quantityBefore == 0) {
					quantityBefore = parseFloat(row.find('input.line-item-quantity').attr('data-original'));
					priceExcludingBefore = parseFloat(row.find('input.line-item-price-excluding').attr('data-original'));
				}
				var pricePerItemIncluding = parseFloat(priceIncludingBefore / quantityBefore);
				priceIncluding = quantity * pricePerItemIncluding;
				priceExcluding = priceIncluding / (taxRate / 100 + 1);
			}
			else if ($(eventElement).hasClass('line-item-price-excluding')) {
				priceIncluding = (taxRate / 100 + 1) * priceExcluding;
			}
			else if ($(eventElement).hasClass('line-item-price-including')) {
				priceExcluding = priceIncluding / (taxRate / 100 + 1);
			}
			
			quantity = quantity.toFixed(0);
			priceExcluding = priceExcluding.toFixed(this.decimalPlaces);
			priceIncluding = priceIncluding.toFixed(this.decimalPlaces);
			
				
			row.find('input.line-item-quantity').val(quantity);
			row.find('input.line-item-price-excluding').val(priceExcluding);
			row.find('input.line-item-price-including').val(priceIncluding);
			
			row.find('input.line-item-quantity').attr('data-before-change', quantity);
			row.find('input.line-item-price-excluding').attr('data-before-change', priceExcluding);
			row.find('input.line-item-price-including').attr('data-before-change', priceIncluding);
			
			// Update total
			var totalAmount = 0;
			$(".unzercw-line-item-grid input.line-item-price-including").each(function() {
				totalAmount += parseFloat($(this).val());
			});
			
			$('#line-item-total').html(totalAmount.toFixed(this.decimalPlaces));
			$('#line-item-total').append(" " + this.currencyCode);
		},
		
	};
	
	$(document).ready(function() {
		UnzerCwLineItemGrid.init();
	});

})(jQuery);