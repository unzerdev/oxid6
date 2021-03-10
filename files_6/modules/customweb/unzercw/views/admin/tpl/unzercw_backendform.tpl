[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="none"}]

<div class="liste" style="height: 41px; position: relative; margin: 0 -20px;">
	<div class="tabs" style="position: absolute; margin: 0;">
	<table cellspacing="0" cellpadding="0" border="0">
	<tr>
	[{assign var="_cnt" value="0"}]
	[{foreach from=$tabs item=tab}]
	  [{if $tab.active }]
	    [{assign var="_act" value=$tab.active}]
	    [{assign var="_state" value="active"}]
	  [{else}]
	    [{assign var="_state" value="inactive"}]
	  [{/if}]
	
	  [{if $_cnt == 0}]
	    [{assign var="_state" value=$_state|cat:" first"}]
	  [{/if}]
	
	  [{if $_cnt == $tabLength-1 }]
	    [{assign var="_state" value=$_state|cat:" last"}]
	  [{/if}]
	
	  <td class="tab [{$_state}]">
	      <div class="r1"><div class="b1">
	      	<a href="[{$oViewConf->getSelfLink()}]&cl=unzercw_backendform&formName=[{$tab.machineName}]&oxid=[{$currentadminshop}]">[{oxmultilang ident=$tab.label noerror=true}]</a>
	      </div></div>
	  </td>
	
	  [{assign var="_cnt" value=$_cnt+1 }]
	[{/foreach}]
	  <td class="line"></td>
	</tr>
	</table>
	</div>
</div>

<div style="padding: 20px 0;">
	[{$formHtml}]
</div>

<link rel="stylesheet" type="text/css" href="[{$oViewConf->getModuleUrl('unzercw', 'out/src/css/admin.css')}]">
</body>
</html>