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

[{$smarty.block.parent}]