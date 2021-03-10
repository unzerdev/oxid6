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

<table cellspacing="0" cellpadding="0" border="0" width="98%">
	<tr>
	    <td valign="top" width="50%" style="padding: 10px;">
	    	<h3>[{oxmultilang ident='Refund' noerror=true}] [{$refundId}]</h3>
	    	<table class="data" style="border: 1px #A9A9A9 solid; padding: 5px; width: 100%;">
				<tbody>
	        		[{foreach from=$refundLabels item=label}]
						<tr>
							<td><b>[{$label.label}]</b> [{oxinputhelp ident=$label.description}]</td>
							<td>[{$label.value}]</td>
						</tr>
	     		   [{/foreach}]
	     		</tbody>
     		</table>
     	</td>
     	<td valign="top" align="left" width="50%" style="padding: 10px;">
			<h3>[{oxmultilang ident='Refunded Items' noerror=true}]</h3>
			<table cellspacing="0" class="data" style="width:100%; margin-bottom: 20px;">
				<thead>
					<tr class="headings">
						<td class="listheader first">[{oxmultilang ident='Name' noerror=true}]</td>
						<td class="listheader">[{oxmultilang ident='SKU' noerror=true}]</td>
						<td class="listheader">[{oxmultilang ident='Quantity' noerror=true}]</td>
						<td class="listheader">[{oxmultilang ident='Tax Rate' noerror=true}]</td>
						<td class="listheader">[{oxmultilang ident='Total Amount (excl. Tax)' noerror=true}]</td>
						<td class="listheader">[{oxmultilang ident='Total Amount (incl. Tax)' noerror=true}]</td>
					</tr>
				</thead>
				<tbody>
					[{foreach from=$refundItems item=item}]
						<tr class="border">
							<td class="listitem" valign="top">[{$item.name}]</td>
							<td class="listitem" valign="top">[{$item.sku}]</td>
							<td class="listitem" valign="top">[{$item.qty}]</td>
							<td class="listitem" valign="top">[{$item.tax_rate}]</td>
							<td class="listitem" valign="top">[{$item.amount_excl}]</td>
							<td class="listitem" valign="top">[{$item.amount_incl}]</td>
						</tr>
					[{/foreach}]
				</tbody>
			</table>
     	</td>
     </tr>
    </table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]