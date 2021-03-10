[{capture append="oxidBlock_content"}]
	<div class="lineBox clear">
		<h1 style="font-size: 21px; font-weight: bold; margin: 10px 15px; float: left;">[{oxmultilang ident='Redirection'}]: [{$paymentMethodName}]</h1>
	
		<form action="[{$formTargetUrl}]" method="POST" name="unzercw-payment-redirection-form">
			[{$hiddenFormFields}]
		
			<div class="actions" style="float: right; width: 230px;">
				<button type="submit" class="submitButton nextStep largeButton">[{oxmultilang ident='Continue'}]</button>
			</div>
		</form>
		
		[{oxscript add="jQuery(document).ready(function() {document['unzercw-payment-redirection-form'].submit();});"}]
	</div>
[{/capture}]
[{include file="layout/page.tpl"}]