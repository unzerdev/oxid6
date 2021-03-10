[{oxscript include=$oViewConf->getModuleUrl('unzercw','out/src/js/checkout.js')}]
[{oxscript add="$(document).ready(function() { var defaultTemplateCheckoutHandler = window['unzercw_checkout_processor']; defaultTemplateCheckoutHandler.init('$processingLabel', '$selfUrl', '$unzercwAliasUrl'); });"}]

[{$smarty.block.parent}]