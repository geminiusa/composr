<tr class="form_table_field_spacer">
	{+START,SET,posting_field}
		{+START,IF_PASSED,POST_COMMENT}
			{+START,IF_NON_EMPTY,{POST_COMMENT}}
				<p class="faux_h2"><label for="{NAME*}">{POST_COMMENT*}</label></p>

				<input type="hidden" name="label_for__{NAME*}" value="{$STRIP_TAGS,{POST_COMMENT*}}" />
			{+END}
		{+END}
		{+START,IF_NON_PASSED,POST_COMMENT}
			<span class="field_name">
				<label class="accessibility_hidden" for="{NAME*}">{!_POST}</label>
			</span>

			<span id="required_readable_marker__{$?,{$IS_EMPTY,{NAME*}},{$RAND},{NAME*}}" style="display: {$?,{REQUIRED*},inline,none}"><span class="required_star">*</span> <span class="accessibility_hidden">{!REQUIRED}</span></span>
		{+END}

		{+START,INCLUDE,FORM_SCREEN_FIELD_DESCRIPTION}{+END}

		<input type="hidden" name="comcode__{NAME*}" value="1" />
		{HIDDEN_FIELDS}

		{+START,IF,{$OR,{$AND,{$IN_STR,{CLASS},wysiwyg},{$JS_ON}},{$AND,{$MATCH_KEY_MATCH,_WILD:cms_comcode_pages},{$SHOW_DOCS}}}}
			<div class="comcode_supported posting_form_main_comcode_button">
				<ul class="horizontal_links horiz_field_sep associated_links_block_group">
					{+START,IF,{$SHOW_DOCS}}{+START,IF_PASSED,COMCODE_URL}
						{$,<li><a class="link_exempt" title="\{!COMCODE_MESSAGE,Comcode\}: \{!LINK_NEW_WINDOW\}" target="_blank" href="\{COMCODE_URL*\}"><img alt="\{!COMCODE_MESSAGE,Comcode\}" src="\{$IMG*,comcode\}" title="\{!COMCODE_MESSAGE,Comcode\}" /></a> \{!COMCODE_MESSAGE,<a class="link_exempt" title="Comcode: \{!LINK_NEW_WINDOW\}" target="_blank" href="\{COMCODE_URL*\}">Comcode</a>\}</li>}
						{+START,IF,{$MATCH_KEY_MATCH,_WILD:cms_comcode_pages}}
							<li><a class="link_exempt" title="{!FULL_COMCODE_TUTORIAL} {!LINK_NEW_WINDOW}" target="_blank" href="{$TUTORIAL_URL*,tut_comcode}">{!FULL_COMCODE_TUTORIAL}</a></li>
							<li><a class="link_exempt" title="{!FULL_BLOCK_TUTORIAL} {!LINK_NEW_WINDOW}" target="_blank" href="{$TUTORIAL_URL*,tut_adv_comcode_pages}">{!FULL_BLOCK_TUTORIAL}</a></li>
						{+END}
					{+END}{+END}
					{+START,IF,{$IN_STR,{CLASS},wysiwyg}}
						{+START,IF,{$JS_ON}}
							<li><a id="toggle_wysiwyg_{NAME*}" href="#" onclick="return toggle_wysiwyg('{NAME;*}');"><abbr title="{!TOGGLE_WYSIWYG_2}"><img src="{$IMG*,icons/16x16/editor/wysiwyg_on}" srcset="{$IMG*,icons/32x32/editor/wysiwyg_on} 2x" alt="{!comcode:ENABLE_WYSIWYG}" title="{!comcode:ENABLE_WYSIWYG}" /></abbr></a></li>
						{+END}
					{+END}
				</ul>
			</div>
		{+END}
	{+END}
	{+START,IF_NON_EMPTY,{$TRIM,{$GET,posting_field}}}
		<th{+START,IF,{$NOT,{$MOBILE}}} colspan="2"{+END} class="table_heading_cell{+START,IF,{REQUIRED}} required{+END}">
			{$GET,posting_field}
		</th>
	{+END}
</tr>
<tr class="field_input">
	<td class="{+START,IF,{REQUIRED}} required{+END} form_table_huge_field"{+START,IF,{$NOT,{$MOBILE}}} colspan="2"{+END}>
		{+START,IF_PASSED,DEFAULT_PARSED}
			<textarea cols="1" rows="1" style="display: none" readonly="readonly" disabled="disabled" name="{NAME*}_parsed">{DEFAULT_PARSED*}</textarea>
		{+END}

		<div class="float_surrounder">
			{+START,IF,{$NOT,{$MOBILE}}}
				{+START,IF,{$JS_ON}}
					<div class="float_surrounder" role="toolbar">
						<div id="post_special_options2" style="display: none">
							{COMCODE_EDITOR_SMALL}
						</div>
						<div id="post_special_options">
							{COMCODE_EDITOR}
						</div>
					</div>
				{+END}
			{+END}

			<div id="container_for_{NAME*}" class="constrain_field container_for_wysiwyg">
				<textarea{+START,IF,{$NOT,{$MOBILE}}} onchange="manage_scroll_height(this);" onkeyup="manage_scroll_height(this);"{+END} accesskey="x" class="{CLASS*}{+START,IF,{REQUIRED}} posting_required{+END} wide_field posting_field_textarea" tabindex="{TABINDEX_PF*}" id="{NAME*}" name="{NAME*}" cols="70" rows="17">{POST*}</textarea>

				{+START,IF_PASSED,WORD_COUNTER}
					{$SET,word_count_id,{$RAND}}
					<div class="word_count" id="word_count_{$GET*,word_count_id}"></div>
				{+END}

				{+START,IF,{$IN_STR,{CLASS},wysiwyg}}
					<script>// <![CDATA[
						if ((window.wysiwyg_on) && (wysiwyg_on()))
						{
							document.getElementById('{NAME;*}').readOnly=true; // Stop typing while it loads
							window.setTimeout(function() {
								if (document.getElementById('{NAME;*}').value==document.getElementById('{NAME;*}').defaultValue)
									document.getElementById('{NAME;*}').readOnly=false; // Too slow, maybe WYSIWYG failed due to some network issue
							},3000);
						}

						{+START,IF_PASSED,WORD_COUNTER}
							add_event_listener_abstract(window,'load',function() {
								setup_word_counter(document.getElementById('post'),document.getElementById('word_count_{$GET;,word_count_id}'));
							});
						{+END}
					//]]></script>
				{+END}
			</div>
		</div>

		{+START,IF_NON_EMPTY,{$TRIM,{EMOTICON_CHOOSER}}}
			{+START,IF,{$NOT,{$MOBILE}}}{+START,IF,{$OR,{$CONFIG_OPTION,is_on_emoticon_choosers},{$AND,{$CNS},{$JS_ON}}}}
				<div{+START,IF,{$CONFIG_OPTION,is_on_emoticon_choosers}} class="emoticon_chooser box"{+END}>
					{+START,IF,{$AND,{$CNS},{$JS_ON}}}
						<span class="right horiz_field_sep associated_link"><a rel="nofollow" target="_blank" href="{$FIND_SCRIPT*,emoticons}?field_name={NAME;*}{$KEEP;*,0,1}" onclick="window.faux_open(maintain_theme_in_link('{$FIND_SCRIPT;*,emoticons}?field_name={NAME;*}{$KEEP;*,0,1}'),'site_emoticon_chooser','width=300,height=320,status=no,resizable=yes,scrollbars=no'); return false;" title="{!EMOTICONS_POPUP} {!LINK_NEW_WINDOW}">{$?,{$CONFIG_OPTION,is_on_emoticon_choosers},{!VIEW_ARCHIVE},{!EMOTICONS_POPUP}}</a></span>
					{+END}

					{+START,IF,{$CONFIG_OPTION,is_on_emoticon_choosers}}
						{EMOTICON_CHOOSER}
					{+END}
				</div>
			{+END}{+END}

			{+START,IF_PASSED,POST_COMMENT}
				<p class="posting_rules">{!USE_WEBSITE_RULES,{$PAGE_LINK*,:rules},{$PAGE_LINK*,:privacy}}</p>
			{+END}
		{+END}

		<script>// <![CDATA[
			manage_scroll_height(document.getElementById('{NAME;/}'));
			{+START,INCLUDE,AUTOCOMPLETE_LOAD,.js,javascript}{+END}
		//]]></script>
	</td>
</tr>

{+START,IF_NON_EMPTY,{ATTACHMENTS}}
	<tr class="form_table_field_spacer">
		<th{+START,IF,{$NOT,{$MOBILE}}} colspan="2"{+END} class="table_heading_cell">
			{+START,IF,{$JS_ON}}
				<a class="toggleable_tray_button" id="fes_attachments" onclick="toggle_subordinate_fields(this.getElementsByTagName('img')[0]); return false;" href="#"><img alt="{!EXPAND}: {!ATTACHMENTS}" title="{!EXPAND}" src="{$IMG*,1x/trays/expand}" srcset="{$IMG*,2x/trays/expand} 2x" /></a>
			{+END}

			<span class="faux_h2{+START,IF,{$JS_ON}} toggleable_tray_button{+END}"{+START,IF,{$JS_ON}} onclick="/*Access-note: code has other activation*/ toggle_subordinate_fields(this.parentNode.getElementsByTagName('img')[0],'fes_attachments_help'); return false;"{+END}>
				{!ATTACHMENTS}

				{+START,IF,{$NOT,{$MOBILE}}}
					<img class="activate_rich_semantic_tooltip help_icon" onclick="this.onmouseover(event);" title="{!ATTACHMENT_HELP=}" onmouseover="activate_rich_semantic_tooltip(this,event);" alt="{!HELP}" src="{$IMG*,icons/16x16/help}" srcset="{$IMG*,icons/32x32/help} 2x" />
				{+END}
			</span>

			{+START,IF_PASSED,HELP}
				<p style="display: none" id="fes_attachments_help">
					{HELP*}
				</p>
			{+END}
		</th>
	</tr>
	<tr style="display: none" class="field_input">
		<td class="form_table_huge_field"{+START,IF,{$NOT,{$MOBILE}}} colspan="2"{+END}>
			{ATTACHMENTS}

			<input type="hidden" name="posting_ref_id" value="{$RAND,1,2147483646}" />

			<script>// <![CDATA[
				add_event_listener_abstract(window,'load',function() {
					initialise_html5_dragdrop_upload('container_for_{NAME;/}','{NAME;/}');
				});
			//]]></script>
		</td>
	</tr>
{+END}
