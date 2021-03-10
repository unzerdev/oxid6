[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_main">
</form>

<form name="back-link" id="back-link" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    [{if $referer == 'transaction'}]
    <input type="hidden" name="oxid" value="[{ $refererId }]">
    <input type="hidden" name="cl" value="unzercw_transaction_view">
    [{else}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="unzercw_order_transactions">
    [{/if}]
    <input type="submit" name="refund" value="[{oxmultilang ident='Back to Transaction' noerror=true}]" style="width:150px;" />
</form>

[{if $isPartialRefundPossible}]
	<table cellspacing="0" cellpadding="0" border="0" width="98%">
		<tr>
		    <td valign="top" width="50%" style="padding: 10px;">
				<h3>[{oxmultilang ident='Partial Refund' noerror=true}]</h3>
				<p>[{oxmultilang ident='With the following form you can perform a partial refund.' noerror=true}]</p>
				
				<form action="[{ $oViewConf->getSelfLink() }]" method="POST" class="unzercw-line-item-grid" id="refund-form">
					[{ $oViewConf->getHiddenSid() }]
				    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
				    <input type="hidden" name="oxid" value="[{ $oxid }]">
				    <input type="hidden" name="cl" value="unzercw_transaction_refund_form">
				    <input type="hidden" name="fnc" value="refund">
				    
				    <input type="hidden" name="referer" value="[{ $referer }]" />
				    <input type="hidden" name="refererId" value="[{ $refererId }]" />
				    
				    <input type="hidden" name="transactionId" value="[{$transactionId}]">
					<input type="hidden" id="unzercw-decimal-places" value="[{$decimalPlaces}]" />
					<input type="hidden" id="unzercw-currency-code" value="[{$currencyCode}]" />
					<table cellspacing="0" class="data" style="width:100%">
						<thead>
							<tr class="headings">
								<td class="listheader first">[{oxmultilang ident='Name' noerror=true}]</td>
								<td class="listheader">[{oxmultilang ident='SKU' noerror=true}]</td>
								<td class="listheader">[{oxmultilang ident='Type' noerror=true}]</td>
								<td class="listheader">[{oxmultilang ident='Tax Rate' noerror=true}]</td>
								<td class="listheader" align="right">[{oxmultilang ident='Quantity' noerror=true}]</td>
								<td class="listheader" align="right">[{oxmultilang ident='Total Amount (excl. Tax)' noerror=true}]</td>
								<td class="listheader" align="right">[{oxmultilang ident='Total Amount (incl. Tax)' noerror=true}]</td>
								</tr>
						</thead>
					
						<tbody>
						[{foreach from=$nonRefundedLineItems item=item name=refundedLineItems}]
							<tr id="line-item-row-[{$smarty.foreach.refundedLineItems.index}]" class="line-item-row border" data-line-item-index="[{$smarty.foreach.refundedLineItems.index}]" >
								<td class="listitem" valign="top">[{$item.name}]</td>
								<td class="listitem" valign="top">[{$item.sku}]</td>
								<td class="listitem" valign="top">[{$item.type}]</td>
								<td class="listitem" valign="top">[{$item.tax_rate}] %<input type="hidden" class="tax-rate" value="[{$item.tax_rate}]" /></td>
								<td class="listitem" align="right" valign="top"><input type="text" style="text-align: right;" class="line-item-quantity" name="quantity[[{$smarty.foreach.refundedLineItems.index}]]" value="[{$item.qty}]" /></td>
								<td class="listitem" align="right" valign="top"><input type="text" style="text-align: right;" class="line-item-price-excluding" name="price_excluding[[{$smarty.foreach.refundedLineItems.index}]]" value="[{$item.amount_excl}]" /></td>
								<td class="listitem" align="right" valign="top"><input type="text" style="text-align: right;" class="line-item-price-including" name="price_including[[{$smarty.foreach.refundedLineItems.index}]]" value="[{$item.amount_incl}]" /></td>
							</tr>
						[{/foreach}]
						</tbody>
						<tfoot>
							<tr>
								<td colspan="7">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="6" align="right">[{oxmultilang ident='Total Refund Amount' noerror=true}]:</td>
								<td id="line-item-total" align="right">
									[{$refundableAmount}]
									[{$currencyCode|upper}]
								</td>
							</tr>
						</tfoot>
					</table>
					[{if $isRefundClosable}]
						<div class="closable-box" style="text-align: right; margin-top: 1em;">
							<label for="close-transaction">[{oxmultilang ident='Close transaction for further refunds' noerror=true}]</label>
							<input id="close-transaction" type="checkbox" name="close" value="on" />
						</div>
					[{/if}]
					
					<div style="text-align: right; margin-top: 1em;">
						<input type="submit" name="save" value="[{oxmultilang ident="Refund" noerror=true}]" style="width:150px;" /><br/>
					</div>
				</form>
			</td>
		</tr>
	</table>
[{/if}]

[{oxscript include="js/libs/jquery.min.js" priority=1}]
[{oxscript include=$oViewConf->getModuleUrl('unzercw','out/src/js/line_item_grid.js')}]
[{oxscript}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]