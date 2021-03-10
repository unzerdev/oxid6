[{$smarty.block.parent}]

[{if $paymentInformation}]
	[{oxmultilang ident='Your Payment Information' suffix='COLON' }]
	[{$paymentInformationPlain}]
[{/if}]