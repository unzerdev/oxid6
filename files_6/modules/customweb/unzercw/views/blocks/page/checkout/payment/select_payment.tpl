[{oxstyle include=$oViewConf->getModuleUrl('unzercw', 'out/src/css/payment_form.css')}]

[{if $paymentmethod->isUnzercwPaymentMethod()}]
    <dl>
        <dt>
            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
            <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{ if $paymentmethod->fAddPaymentSum }]([{ $paymentmethod->fAddPaymentSum }] [{ $currency->sign}])[{/if}]</b></label>
        </dt>
        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
        	[{if $paymentmethod->oxpayments__oxlongdesc->value}]
                <div class="desc">
                    [{ $paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                </div>
            [{/if}]
        	[{if $paymentmethod->isUnzercwPaymentFormOnPaymentPage()}]
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
			[{/if}]
        </dd>
    </dl>
[{else}]
    [{$smarty.block.parent}]
[{/if}]