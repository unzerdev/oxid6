[{foreach from=$cssFiles item=cssFile}]
	[{oxstyle include=$cssFile}]
[{/foreach}]
[{oxstyle include=$oViewConf->getModuleUrl('unzercw', 'out/src/css/payment_form.css')}]

[{foreach from=$javascriptFiles item=javascriptFile}]
	[{oxscript include=$javascriptFile}]
[{/foreach}]

[{capture append="oxidBlock_content"}]
	[{$mainContent}]
[{/capture}]
[{include file="layout/page.tpl" title=$template_title location=$template_title}]