{TITLE}

<p>
	{!CONFIRM_REMOVE_PERIODIC}
</p>

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
	{$INSERT_SPAMMER_BLACKHOLE}

	{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}

	<div>
		<div class="proceed_button">
			<input class="buttons__proceed button_screen" onclick="this.disabled=true; this.form.submit();" accesskey="u" type="submit" value="{!PROCEED}" />
		</div>
	</div>
</form>

{+START,IF,{$JS_ON}}
<a href="#" onclick="history.back(); return false;"><img title="{!_NEXT_ITEM_BACK}" alt="{!_NEXT_ITEM_BACK}" src="{$IMG*,icons/48x48/menu/_generic_admin/back}" /></a>
{+END}

