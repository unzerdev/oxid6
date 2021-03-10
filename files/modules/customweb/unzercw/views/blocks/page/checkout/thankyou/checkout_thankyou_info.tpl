[{$smarty.block.parent}]

[{if $paymentInformation}]
	<h3 style="margin-top: 20px;">[{oxmultilang ident='Your Payment Information'}]</h3>
	<div style="margin-bottom: 30px;">[{$paymentInformation}]</div>
[{/if}]