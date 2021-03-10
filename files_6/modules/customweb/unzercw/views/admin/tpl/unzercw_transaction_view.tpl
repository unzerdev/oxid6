[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function gotoOrder( sID )
{
	[{assign var="shMen" value=1}]

	[{foreach from=$menustructure item=menuholder }]
	  [{if $shMen && $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length }]

		[{assign var="shMen" value=0}]
		[{assign var="mn" value=1}]

		[{foreach from=$menuholder->childNodes item=menuitem }]
		  [{if $menuitem->nodeType == XML_ELEMENT_NODE && $menuitem->childNodes->length }]
			[{ if $menuitem->getAttribute('id') == 'mxorders' }]

			  [{foreach from=$menuitem->childNodes item=submenuitem }]
				[{if $submenuitem->nodeType == XML_ELEMENT_NODE && $submenuitem->getAttribute('cl') == 'admin_order' }]

					if ( top && top.navigation && top.navigation.adminnav ) {
						var _sbli = top.navigation.adminnav.document.getElementById( 'nav-1-[{$mn}]-1' );
						var _sba = _sbli.getElementsByTagName( 'a' );
						top.navigation.adminnav._navAct( _sba[0] );
					}

				[{/if}]
			  [{/foreach}]

			[{ /if }]
			[{assign var="mn" value=$mn+1}]

		  [{/if}]
		[{/foreach}]
	  [{/if}]
	[{/foreach}]

	var oTransfer = document.getElementById("transfer");
	oTransfer.oxid.value=sID;
	oTransfer.cl.value='admin_order';
	oTransfer.target='basefrm';
	oTransfer.submit();
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="unzercwtransaction_view">
</form>

<table cellspacing="0" cellpadding="0" border="0" width="98%">
	<tr>
	    <td valign="top" width="50%" style="padding: 10px;">
	    	<table class="data" style="border: 1px #A9A9A9 solid; padding: 5px; width: 100%;">
				<tbody>
					<tr>
						<td><b>[{oxmultilang ident='Date' noerror=true}]</b></td>
						<td>[{$transaction->getCreatedOn()|oxformdate}]</td>
					</tr>
					<tr>
						<td><b>[{oxmultilang ident='Transaction Id' noerror=true}]</b></td>
						<td>[{$transactionId}]</td>
					</tr>
					[{if $orderId}]
						<tr>
							<td><b>[{oxmultilang ident='Order' noerror=true}]</b></td>
							<td>[{$orderNumber}] <button onclick="gotoOrder('[{$orderId}]')">[{oxmultilang ident='View Order' noerror=true}]</button></td>
						</tr>
					[{/if}]
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
	        		[{foreach from=$labels item=label}]
						<tr>
							<td><b>[{$label.label}]</b> [{oxinputhelp ident=$label.description}]</td>
							<td>[{$label.value|escape:'htmlall'}]</td>
						</tr>
	     		   [{/foreach}]
	     		</tbody>
     		</table>

     		[{if $paymentInformation}]
     			<h3 style="margin-top: 20px;">[{oxmultilang ident='Payment Information' noerror=true}]</h3>
				<div>[{$paymentInformation}]</div>
			[{/if}]
	    </td>
	    <!-- Anfang rechte Seite -->
	    <td valign="top" align="left" width="50%" style="padding: 10px;">
	    	<h3>[{oxmultilang ident='Transaction History' noerror=true}]</h3>
			<table cellspacing="0" class="data" style="width:100%; margin-bottom: 20px;">
				<thead>
					<tr class="headings">
						<td class="listheader first">[{oxmultilang ident='Date' noerror=true}]</th>
						<td class="listheader">[{oxmultilang ident='Action' noerror=true}]</th>
						<td class="listheader">[{oxmultilang ident='Message' noerror=true}]</th>
					</tr>
				</thead>
				<tbody>
					[{foreach from=$history item=item}]
						<tr class="border">
							<td class="listitem" valign="top">[{$item.creationdate}]</td>
							<td class="listitem" valign="top">[{$item.action}]</td>
							<td class="listitem" valign="top">[{$item.message}]</td>
						</tr>
					[{/foreach}]
				</tbody>
			</table>
			
	    	[{if $captures || $isCapturePossible}]
	    		<h3>[{oxmultilang ident='Captures' noerror=true}]</h3>
	    	[{/if}]
	    	[{if $captures}]
		   		<table cellspacing="0" class="data" style="width:100%; margin-bottom: 20px;">
					<thead>
						<tr class="headings">
							<td class="listheader first">[{oxmultilang ident='Date' noerror=true}]</td>
							<td class="listheader">[{oxmultilang ident='Amount' noerror=true}]</td>
							<td class="listheader">[{oxmultilang ident='Status' noerror=true}]</td>
							<td class="listheader"></td>
						</tr>
					</thead>
					<tbody>
						[{foreach from=$captures item=capture}]
							<tr class="border">
								<td class="listitem" valign="top">[{$capture.date}]</td>
								<td class="listitem" valign="top">[{$capture.amount}]</td>
								<td class="listitem" valign="top">[{$capture.status}]</td>
								<td><a href="[{ $oViewConf->getSelfLink() }]cl=unzercw_transaction_capture&oxid=[{$oxid}]&capture=[{$capture.id}]&referer=transaction&refererId=[{$oxid}]">View</a></td>
							</tr>
						[{/foreach}]
					</tbody>
				</table>
			[{/if}]
			[{if $isCapturePossible}]
				<form name="capture" id="capture" action="[{ $oViewConf->getSelfLink() }]" method="post">
				    [{ $oViewConf->getHiddenSid() }]
				    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
				    <input type="hidden" name="oxid" value="[{ $orderId }]">
				    <input type="hidden" name="referer" value="transaction" />
				    <input type="hidden" name="refererId" value="[{ $oxid }]" />
				    <input type="hidden" name="cl" value="unzercw_transaction_capture_form">
				    [{if !$isPartialCapturePossible}]
				    	<input type="hidden" name="fnc" value="capture">
				    [{/if}]
				    <input type="hidden" name="transactionId" value="[{$transactionId}]">

					<input type="submit" name="capture" value="[{oxmultilang ident="Capture" noerror=true}]" style="width:150px;" />
				</form>
			[{/if}]
	   		
	   		[{if $refunds || $isRefundPossible}]
		   		<h3>[{oxmultilang ident='Refunds' noerror=true}]</h3>
		   	[{/if}]
	   		[{if $refunds}]
				<table cellspacing="0" class="data" style="width:100%; margin-bottom: 20px;">
					<thead>
						<tr class="headings">
							<td class="listheader first">[{oxmultilang ident='Date' noerror=true}]</td>
							<td class="listheader">[{oxmultilang ident='Amount' noerror=true}]</td>
							<td class="listheader">[{oxmultilang ident='Status' noerror=true}]</td>
							<td class="listheader"></td>
						</tr>
					</thead>
					<tbody>
						[{foreach from=$refunds item=refund}]
							<tr class="border">
								<td class="listitem" valign="top">[{$refund.date}]</td>
								<td class="listitem" valign="top">[{$refund.amount}]</td>
								<td class="listitem" valign="top">[{$refund.status}]</td>
								<td><a href="[{ $oViewConf->getSelfLink() }]cl=unzercw_transaction_refund&oxid=[{$oxid}]&refund=[{$refund.id}]&referer=transaction&refererId=[{$oxid}]">View</a></td>
							</tr>
						[{/foreach}]
					</tbody>
				</table>
			[{/if}]
			[{if $isRefundPossible}]
				<form name="refund" id="refund" action="[{ $oViewConf->getSelfLink() }]" method="post">
				    [{ $oViewConf->getHiddenSid() }]
				    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
				    <input type="hidden" name="oxid" value="[{ $orderId }]">
				    <input type="hidden" name="referer" value="transaction" />
				    <input type="hidden" name="refererId" value="[{ $oxid }]" />
				    <input type="hidden" name="cl" value="unzercw_transaction_refund_form">
				    [{if !$isPartialRefundPossible}]
				    	<input type="hidden" name="fnc" value="refund">
				    [{/if}]
				    <input type="hidden" name="transactionId" value="[{$transactionId}]">

					<input type="submit" name="refund" value="[{oxmultilang ident="Refund" noerror=true}]" style="width:150px;" />
				</form>
			[{/if}]
			
			[{if $cancels || $isCancelPossible}]
				<h3>[{oxmultilang ident='Cancelation' noerror=true}]</h3>
			[{/if}]
			[{if $cancels}]
		   		<table cellspacing="0" class="data" style="width:100%; margin-bottom: 20px;">
					<thead>
						<tr class="headings">
							[{foreach from=$cancelLabels item=label}]
								<td class="listheader">[{oxmultilang ident=$label noerror=true}]</th>
							[{/foreach}]
						</tr>
					</thead>
					<tbody>
						[{foreach from=$cancels item=cancel}]
							<tr class="border">
								[{foreach from=$cancel item=label}]
									<td class="listitem" valign="top">[{$label.value|escape:'htmlall'}] [{oxinputhelp ident=$label.description}]</td>
								[{/foreach}]
							</tr>
						[{/foreach}]
					</tbody>
				</table>
			[{/if}]
			[{if $isCancelPossible}]
				<form name="cancel" id="cancel" action="[{ $oViewConf->getSelfLink() }]" method="post">
				    [{ $oViewConf->getHiddenSid() }]
				    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
				    <input type="hidden" name="oxid" value="[{ $orderId }]">
				    <input type="hidden" name="referer" value="transaction" />
				    <input type="hidden" name="refererId" value="[{ $oxid }]" />
				    <input type="hidden" name="cl" value="unzercw_order_transactions">
				    <input type="hidden" name="fnc" value="cancel">
				    <input type="hidden" name="transactionId" value="[{$transactionId}]">
				    
				    <input type="submit" name="save" value="[{oxmultilang ident="Cancel" noerror=true}]" style="width:150px;" />
				</form>
			[{/if}]
			
			[{if $isUpdatable}]
				<h3>[{oxmultilang ident='Update' noerror=true}]</h3>
				<form name="update" id="update" action="[{ $oViewConf->getSelfLink() }]" method="post">
				    [{ $oViewConf->getHiddenSid() }]
				    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
				    <input type="hidden" name="oxid" value="[{ $orderId }]">
				    <input type="hidden" name="referer" value="transaction" />
				    <input type="hidden" name="refererId" value="[{ $oxid }]" />
				    <input type="hidden" name="cl" value="unzercw_order_transactions">
				    <input type="hidden" name="fnc" value="update">
				    <input type="hidden" name="transactionId" value="[{$transactionId}]">
				    
				    <input type="submit" name="save" value="[{oxmultilang ident="Update" noerror=true}]" style="width:150px;" />
				</form>
			[{/if}]
	    </td>
	    <!-- Ende rechte Seite -->
	
	</tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]