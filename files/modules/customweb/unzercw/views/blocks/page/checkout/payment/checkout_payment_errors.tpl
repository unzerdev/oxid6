[{assign var="iPayError" value=$oView->getPaymentError() }]
[{if $iPayError == 'unzercw'}]
	<div class="status error">[{ $oView->getPaymentErrorText() }]</div>
[{else}]
    [{$smarty.block.parent}]
[{/if}]