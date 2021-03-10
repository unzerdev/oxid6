[{capture append="oxidBlock_content"}]
	[{include file="page/checkout/inc/steps.tpl" active=4 }]

	<div id="thankyouPage">
		<h3 class="blockHead">[{$pendingTitle}]</h3>
		<div class="status error">
			<p>[{$pendingText}]</p>
			<p>[{$checkAgainText}]</p>
		</div>
		
		<div class="lineBox clear">
			<a href="[{$checkoutUrl}]" class="prevStep submitButton largeButton">[{$checkoutButtonLabel}]</a>
			<a href="" class="submitButton nextStep largeButton">[{$checkAgainButtonLabel}]</a>
		</div>
	</div>
[{/capture}]
[{include file="layout/page.tpl"}]