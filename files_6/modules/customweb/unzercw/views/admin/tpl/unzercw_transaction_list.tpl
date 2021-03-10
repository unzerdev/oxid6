[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]
}
//-->
</script>

[{if $noresult}]
    <span class="listitem">
        <b>[{oxmultilang default=true ident="SHOWLIST_NORESULTS"}]</b><br><br>
    </span>
[{/if}]

<div id="liste">

<form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
	[{include file="_formparams.tpl" cl="unzercw_transaction_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td class="listfilter first">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[unzercw_transaction][createdOn]" value="[{$where.unzercw_transaction.createdOn|oxformdate}]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[unzercw_transaction][transactionId]" value="[{$where.unzercw_transaction.transactionId}]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[unzercw_transaction][orderNumber]" value="[{$where.unzercw_transaction.orderNumber }]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="15" maxlength="128" name="where[oxpayments][oxdesc]" value="[{$where.oxpayments.oxdesc}]">
            </div></div>
        </td>
        <td class="listfilter">
            <div class="r1">
              <div class="b1">

                <input class="listedit" type="text" size="15" maxlength="128" name="where[unzercw_transaction][paymentId]" value="[{$where.unzercw_transaction.paymentId}]">

              <div class="find">
              <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang default=true ident="GENERAL_SEARCH" }]">
            </div>
            </div>
          </div>
        </td>
    </tr>
    <tr>
        <td class="listheader first"><a href="javascript:top.oxid.admin.setSorting(document.forms.showlist, '', 'createdOn', 'desc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="Date" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting(document.forms.showlist, '', 'transactionId', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="Transaction Id" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting(document.forms.showlist, '', 'orderNumber', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang default=true ident="GENERAL_ORDERNUM" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting(document.forms.showlist, 'oxpayments', 'oxdesc', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="Payment Method" }]</a></td>
        <td class="listheader"><a href="javascript:top.oxid.admin.setSorting(document.forms.showlist, '', 'paymentId', 'asc');document.forms.showlist.submit();" class="listheader">[{ oxmultilang ident="Payment Id" }]</a></td>
    </tr>

[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    
    [{if $listitem->oxorder__oxstorno->value == 1}]
		[{assign var="listclass" value=listitem3}]
	[{else}]
		[{ if $listitem->blacklist == 1}]
			[{assign var="listclass" value=listitem3}]
		[{ else}]
			[{assign var="listclass" value=listitem$blWhite}]
		[{ /if}]
	[{/if}]
	[{ if $listitem->getTransactionId() == $oxid}]
		[{assign var="listclass" value=listitem4}]
	[{ /if}]
    <tr id="row.[{$_cnt}]">
        <td class="[{$listclass}]"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->getTransactionId()}]');" class="[{$listclass}]">[{$listitem->unzercw_transaction__createdon|oxformdate}]</a></td>
        <td class="[{$listclass}]"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->getTransactionId()}]');" class="[{$listclass}]">[{$listitem->unzercw_transaction__transactionid->value}]</a></td>
        <td class="[{$listclass}]"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->getTransactionId()}]');" class="[{$listclass}]">[{$listitem->unzercw_transaction__ordernumber->value}]</a></td>
        <td class="[{$listclass}]"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->getTransactionId()}]');" class="[{$listclass}]">[{$listitem->unzercw_transaction__paymentmethodname->value}]</a></td>
        <td class="[{$listclass}]"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->getTransactionId()}]');" class="[{$listclass}]">[{$listitem->unzercw_transaction__paymentid->value}]</a></td>
    </tr>
[{if $blWhite == "2"}]
    [{assign var="blWhite" value=""}]
[{else}]
    [{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="8"}]

</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{oxmultilang default=true ident="ORDER_LIST_MENUITEM"}]";
    parent.parent.sMenuSubItem = "[{oxmultilang ident="mxunzercw_transactions"}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
