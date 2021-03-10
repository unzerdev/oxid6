[{oxstyle include=$oViewConf->getModuleUrl('unzercw', 'out/src/css/payment_form.css')}]

[{if $paymentmethod->isUnzercwPaymentMethod()}]
	<div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
		<input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
	    <ul class="form">
	        [{if $paymentmethod->getPrice()}]
	            <li>
	                <div class="payment-charge">
	                    [{if $oxcmp_basket->getPayCostNet()}]
	                        ([{$paymentmethod->getFNettoPrice()}] [{$currency->sign}] [{oxmultilang ident="PLUS_VAT"}] [{$paymentmethod->getFPriceVat()}] )
	                    [{else}]
	                        ([{$paymentmethod->getFBruttoPrice()}] [{$currency->sign}])
	                    [{/if}]
	                </div>
	            </li>
	        [{/if}]

			[{block name="checkout_payment_longdesc"}]
	            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
	                <li>
	                    <div class="payment-desc">
	                        [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
	                    </div>
	                </li>
	            [{/if}]
	        [{/block}]
	        
	        [{if $paymentmethod->isUnzercwPaymentFormOnPaymentPage()}]
	        	<li>
		        	<div class="unzercw-payment-form [{$sPaymentID}]-form" data-authorization-method="[{$paymentmethod->getUnzercwAuthorizationMethod()}]">
						<ul class="form">
							<div class="unzercw-alias-form-fields">
								[{$paymentmethod->getUnzercwAliasFormFields()}]
							</div>
							
							<div class="unzercw-visible-form-fields">
								[{$paymentmethod->getUnzercwVisibleFormFields()}]
							</div>
						</ul>
					</div>
				</li>
			[{/if}]
	    </ul>
	</div>
[{else}]
    [{$smarty.block.parent}]
[{/if}]