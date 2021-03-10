[{if $isUnzercwModule}]
	[{oxstyle include=$oViewConf->getModuleUrl('unzercw', 'out/src/css/payment_form.css')}]
	
	[{oxscript include=$oViewConf->getModuleUrl('unzercw','out/src/js/checkout.js')}]
	[{oxscript add="$(document).ready(function() { var defaultTemplateCheckoutHandler = window['unzercw_checkout_processor']; defaultTemplateCheckoutHandler.init('$processingLabel', '$selfUrl', '$aliasUrl', '$paymentMethodId'); });"}]
	
	<form action="[{$formActionUrl}]" method="post" id="orderConfirmAgbBottom" accept-charset="UTF-8" class="form-horizontal">
		[{$oViewConf->getHiddenSid()}]
		[{$oViewConf->getNavFormParams()}]
		<input type="hidden" name="challenge" value="[{$challenge}]">
		<input type="hidden" name="sDeliveryAddressMD5" value="[{$oView->getDeliveryAddressMD5()}]">
		
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
		
		[{if !$preventDefault}]
			<input type="hidden" name="cl" value="order">
			<input type="hidden" name="fnc" value="[{$oView->getExecuteFnc()}]">
		[{/if}]
		
		[{if !$mobileActive}]
			[{if !$includeAGBTemplate}]
				<div class="agb">
					[{if $oView->isActive('PsLogin') || !$oView->isConfirmAGBActive()}]
			            <input type="hidden" name="ord_agb" value="1">
			        [{else}]
			            <input type="hidden" name="ord_agb" value="0">
			        [{/if}]
				</div>
			[{/if}]
		
			<div class="well well-sm">
	            <button type="submit" class="btn btn-lg btn-primary pull-right submitButton nextStep largeButton">
	                <i class="fa fa-check"></i> [{oxmultilang ident="SUBMIT_ORDER"}]
	            </button>
	            <div class="clearfix"></div>
	        </div>		
		[{else}]
			[{if !$includeAGBTemplate}]
			<div class="agb">
				[{if $oView->isActive('PsLogin') }]
					<input type="hidden" name="ord_agb" value="1" id="ord_agb">
				[{else}]
					[{if $oView->isConfirmAGBActive()}]
							<input type="hidden" name="ord_agb" value="0" id="ord_agb">
					[{/if}]
				[{/if}]
			</div>
			[{else}]
				[{if !$oView->showOrderButtonOnTop()}]
					[{include file="page/checkout/inc/agb.tpl"}]
					<hr/>
				[{else}]
					[{include file="page/checkout/inc/agb.tpl" hideButtons=true}]
				[{/if}]
			[{/if}]
			<ul class="form">
	  	     	<li><button type="submit" class="btn">[{oxmultilang default=true ident="SUBMIT_ORDER"}]</button></li>
				<li><input type="button" class="btn previous" value="[{oxmultilang default=true ident="PREVIOUS_STEP"}]" onclick="window.open('[{oxgetseourl ident=$oViewConf->getPaymentLink()}]', '_self');"></li>
	       	</ul>
       	[{/if}]
	</form>
[{else}]
	[{$smarty.block.parent}]
[{/if}]