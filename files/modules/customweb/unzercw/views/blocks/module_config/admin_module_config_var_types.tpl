[{$smarty.block.parent}]

[{if $module_var|strpos:'unzercw_' === 0}]
	[{if $var_type == 'cwpassword'}]
		<input type="password" class="txt" style="width: 250px;" name="confpasswords[[{$module_var}]]" value="[{$confpasswords.$module_var}]" [{$readonly}] />
	[{elseif $var_type == 'textarea'}]
		<textarea style="width: 250px;" name="conftextareas[[{$module_var}]]" [{$readonly}]>[{$conftextareas.$module_var}]</textarea>
	[{elseif $var_type == 'file'}]
		<select style="width: auto;" class="select" name="conffiles[[{$module_var}]]" [{$readonly}]>
			<option value="">[{ oxmultilang ident="Default" noerror=true }]</option>
			[{foreach from=$var_constraints.$module_var item='_field'}]
				<option value="[{$_field}]" [{ if $_field == $conffiles.$module_var}]selected="selected"[{/if}]>[{$_field}]</option>
			[{/foreach}]
		</select>
	[{elseif $var_type == 'multilang'}]
		<ul>
			[{foreach from=$languages key=langKey item=langLabel}]
				<li>[{$langLabel}]: <input type="text" class="txt" style="width: 250px;" name="confmultilangs[[{$module_var}]][[{$langKey}]]" value="[{$confmultilangs.$module_var.$langKey}]" [{$readonly}] /></li>
			[{/foreach}]
		</ul>
	[{elseif $var_type == 'multiselect'}]
		<input type="hidden" name="confmultiselects[[{$module_var}]][dummy]" value="dummy" />
		<select style="width: auto;" class="select" name="confmultiselects[[{$module_var}]][]" size="[{$var_constraints.$module_var|@count}]" multiple="multiple" [{$readonly}]>
			[{foreach from=$var_constraints.$module_var item='_field'}]
				<option value="[{$_field|escape}]" [{ if in_array($_field, $confmultiselects.$module_var) || in_array($_field|oxmultilangassign, $confmultiselects.$module_var)}]selected="selected"[{/if}]>[{ oxmultilang default=true ident="SHOP_MODULE_`$module_var`_`$_field`" noerror=true }]</option>
			[{/foreach}]
		</select>
	[{elseif $var_type == 'cwselect'}]
	    <select  style="width: auto;" class="select" name="confselects[[{$module_var}]]" [{ $readonly }]>
	        [{foreach from=$var_constraints.$module_var item='_field'}]
	            <option value="[{$_field|escape}]"  [{if ($confselects.$module_var==$_field)}]selected[{/if}]>[{ oxmultilang default=true ident="SHOP_MODULE_`$module_var`_`$_field`" }]</option>
	        [{/foreach}]
	    </select>
	[{/if}]
[{/if}]