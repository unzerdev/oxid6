[{include file="order_list.tpl"}]

[{oxscript include="js/libs/jquery.min.js" priority=1}]
[{oxscript}]

<script type="text/javascript">
$('a[href$="_order_transactions"]').parents('td').hide();
[{if $enabledModule}]
	$('a[href="#[{$enabledModule}]_order_transactions"]').parents('td').show();
[{/if}]
[{if $noSelection}]
	$('.tabs .tab:gt(5)').hide();
[{/if}]
$('td.tab').removeClass('last');
$('td.tab:visible:last').addClass('last');
</script>