[{include file="payment_list.tpl"}]

[{oxscript include="js/libs/jquery.min.js" priority=1}]
[{oxscript}]

<script type="text/javascript">
$('a[href$="_payment_settings"]').parents('td').hide();
[{if $enabledModule}]
	$('a[href="#[{$enabledModule}]_payment_settings"]').parents('td').show();
[{/if}]
[{if $noSelection}]
	$('.tabs .tab:gt(2)').hide();
[{/if}]
$('td.tab').removeClass('last');
$('td.tab:visible:last').addClass('last');
</script>