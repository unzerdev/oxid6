[{if $unzercw_widgets}]
	<div class="lineBox">
		[{foreach from=$unzercw_widgets item=widget}]
			<div class="unzercw-external-checkout-widget">
				[{$widget.html}]
			</div>
		[{/foreach}]
	</div>

	<style type="text/css">
	.unzercw-external-checkout-widget {
		display: inline-block;
  		margin-right: 15px;
  		margin-bottom: 15px;
	}
	</style>
[{/if}]

[{if $external_checkout}]
	<div class="status success corners">
		<p>[{oxmultilang ident='Dear customer we have received your billing and shipping information. In order to finish your order please create an account below or checkout as guest.'}]</p>
	</div>
[{/if}]

[{$smarty.block.parent}]

[{if $lgn_usr}]
	[{oxscript add="$('input[name=lgn_usr]').val('$lgn_usr');"}]
[{/if}]