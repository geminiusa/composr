<form title="{!DELETE_SELECTION}" id="mass_select_form__{$GET%,support_mass_select}" style="display: none" onsubmit="return confirm_delete(this,true);" class="mass_delete_form" action="{$PAGE_LINK*,_SEARCH:{$GET,support_mass_select}:mass_delete:redirect={$SELF_URL&}}" method="post">
	{$INSERT_SPAMMER_BLACKHOLE}

	<p class="proceed_button">
		<input class="menu___generic_admin__delete button_screen_item" type="submit" value="{!DELETE_SELECTION}" />
	</p>
</form>
