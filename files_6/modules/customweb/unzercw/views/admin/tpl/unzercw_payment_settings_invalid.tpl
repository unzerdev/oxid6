[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="payment_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<div class="messagebox">
	<p class="message">[{oxmultilang ident='This is no Unzer payment method.' noerror=true}]</p>
</div>
    
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]