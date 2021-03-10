[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="payment_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
	[{$oViewConf->getHiddenSid()}]
	<input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
	<input type="hidden" name="fnc" value="save">
	<input type="hidden" name="oxid" value="[{$oxid}]">
	<input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">
	
	<table cellspacing="0" cellpadding="0" border="0" style="width:100%;height:100%;">
	[{foreach from=$paymentSettings item=paymentSetting}]
		<tr>
			<td valign="top" class="edittext" width="250" nowrap="">
				<label for="[{$paymentSetting.key}]">[{oxmultilang ident=$paymentSetting.title noerror=true}]</label>
			</td>
			<td valign="top" class="edittext">
				[{if $paymentSetting.description}]
					<input type="button" id="helpBtn_[{$paymentSetting.name}]" class="btnShowHelpPanel" onClick="YAHOO.oxid.help.showPanel('[{$paymentSetting.name}]');">
				    <div id="helpText_[{$paymentSetting.name}]" class="helpPanelText">
				        [{oxmultilang ident=$paymentSetting.description noerror=true}]
				    </div>
				[{/if}]
			
				[{if $paymentSetting.type eq 'str'}]
					<input class="txt" style="width: 250px;" type="text" name="confstrs[[{$paymentSetting.key}]]" value="[{$paymentSetting.value}]" />
				[{elseif $paymentSetting.type eq 'password'}]
					<input class="txt" style="width: 250px;" type="password" name="confpasswords[[{$paymentSetting.key}]]" value="[{$paymentSetting.value}]" />
				[{elseif $paymentSetting.type eq 'textarea'}]
					<textarea style="width: 250px;" name="conftextareas[[{$paymentSetting.key}]]">[{$paymentSetting.value}]</textarea>
				[{elseif $paymentSetting.type eq 'select'}]
					<select class="select" name="confselects[[{$paymentSetting.key}]]">
						[{foreach from=$paymentSetting.options key=value item=label}]
	                    	<option value="[{$value}]" [{ if $paymentSetting.value == $value || ($value|oxmultilangassign == $paymentSetting.value)}]selected="selected"[{/if}]>
	                    		[{if strstr($paymentSetting.key, 'authorizationMethod')}]
	                    			[{$label}]
	                    		[{elseif ctype_digit($label)}]
	                    			[{$label}]
	                    		[{else}]
	                    			[{oxmultilang ident=$label noerror=true}]
	                    		[{/if}]
	                    	</option>
	                    [{/foreach}]
	                </select>
				[{elseif $paymentSetting.type eq 'multiselect'}]
					<input type="hidden" name="confmultiselects[[{$paymentSetting.key}]][dummy]" value="dummy" />
					<select class="select" name="confmultiselects[[{$paymentSetting.key}]][]" size="[{$paymentSetting.options|@count}]" multiple="multiple">
						[{foreach from=$paymentSetting.options key=value item=label}]
	                   		<option value="[{$value}]" [{ if in_array($value, $paymentSetting.value) || in_array($value|oxmultilangassign, $paymentSetting.value)}]selected="selected"[{/if}]>[{oxmultilang ident=$label noerror=true}]</option>
	                    [{/foreach}]
					</select>
				[{elseif $paymentSetting.type eq 'multilang'}]
					<ul>
						[{foreach from=$languages key=langKey item=langLabel}]
							<li>[{$langLabel}]: <input class="txt" style="width: 250px;" type="text" name="confmultilangs[[{$paymentSetting.key}]][[{$langKey}]]" value="[{$paymentSetting.value.$langKey}]" /></li>
						[{/foreach}]
					</ul>
				[{elseif $paymentSetting.type == 'file'}]
					<select class="select" name="conffiles[[{$paymentSetting.key}]]">
						<option value="">[{ oxmultilang ident="Default" noerror=true }]</option>
						[{foreach from=$paymentSetting.options item='_field'}]
							<option value="[{$_field}]" [{ if $_field == $paymentSetting.value}]selected="selected"[{/if}]>[{$_field}]</option>
						[{/foreach}]
					</select>
				[{/if}]
			</td>
		</tr>
	[{/foreach}]
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="save" value="[{oxmultilang ident="Save" noerror=true}]" style="width:150px;" /></td>
		</tr>
	</table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]