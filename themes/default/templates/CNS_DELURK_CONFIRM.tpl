{TITLE}

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}">
	{$INSERT_SPAMMER_BLACKHOLE}

	<p class="lonely_label">
		{!DELURK_CONFIRM}
	</p>
	<ul>
		{+START,LOOP,LURKERS}
			<li>
				<label for="lurker_{ID*}"><input type="checkbox" name="lurker_{ID*}" id="lurker_{ID*}" value="1" checked="checked" /> <a title="{USERNAME*} {!LINK_NEW_WINDOW}" target="_blank" href="{PROFILE_URL*}">{USERNAME*}</a></label>
			</li>
		{+END}
	</ul>

	<p class="proceed_button">
		{+START,IF,{$JS_ON}}
			<input class="buttons__back button_screen" type="button" onclick="history.back(); return false;" value="{!GO_BACK}" />
		{+END}

		<input accesskey="u" onclick="disable_button_just_clicked(this);" class="buttons__proceed button_screen" type="submit" value="{!PROCEED}" />
	</p>
</form>
