{$,Read the catalogue tutorial for information on custom catalogue layouts}

{+START,IF,{GIVE_CONTEXT}}
<div class="box"><div class="box_inner">
{+END}
{+START,IF,{$NOT,{GIVE_CONTEXT}}}
<div class="catalogue_fieldmap_entry_wrap">
{+END}
	{+START,IF,{GIVE_CONTEXT}}
		<h3>{!CATALOGUE_GENERIC,{CATALOGUE_TITLE*}}</h3>
	{+END}

	<div class="wide_table_wrap"><table class="map_table wide_table results_table spaced_table">
		{+START,IF,{$NOT,{$MOBILE}}}
			<colgroup>
				<col class="catalogue_fieldmap_field_name_column" />
				<col class="catalogue_fieldmap_field_value_column" />
			</colgroup>
		{+END}

		<tbody>
			{FIELDS}
		</tbody>
	</table></div>

	{+START,IF_PASSED,BREADCRUMBS}
		{+START,IF_NON_EMPTY,{BREADCRUMBS}}
			<nav class="breadcrumbs" itemprop="breadcrumb" role="navigation"><p>{!LOCATED_IN,{BREADCRUMBS}}</p></nav>
		{+END}
	{+END}

	{+START,IF_NON_PASSED_OR_FALSE,ENTRY_SCREEN}
		<div class="float_surrounder">
			{+START,IF,{$NOT,{GIVE_CONTEXT}}}
				<p class="left">
					<a target="_self" href="#"><img class="top_vertical_alignment" title="{!BACK_TO_TOP}" alt="{!BACK_TO_TOP}" src="{$IMG*,icons/24x24/tool_buttons/top}" srcset="{$IMG*,icons/48x48/tool_buttons/top} 2x" /></a>
				</p>
			{+END}

			{+START,IF_NON_EMPTY,{VIEW_URL}}
				<p class="{$?,{GIVE_CONTEXT},shunted_button,right}">
					<a class="buttons__more button_screen_item" title="{!VIEW}: {$STRIP_TAGS*,{FIELD_0}} {+START,IF_PASSED_AND_TRUE,COMMENT_COUNT} ({$STRIP_TAGS,{$COMMENT_COUNT,catalogues,{ID}}}){+END}" href="{VIEW_URL*}"><span>{!VIEW}</span></a>
				</p>
			{+END}
			{+START,IF_EMPTY,{VIEW_URL}}{+START,IF_NON_EMPTY,{EDIT_URL}}
				<p class="{$?,{GIVE_CONTEXT},shunted_button,right}">
					<a class="buttons__edit button_screen_item" href="{EDIT_URL*}" title="{!EDIT}: {$STRIP_TAGS*,{FIELD_0}}"><span>{!EDIT}</span></a>
				</p>
			{+END}{+END}
		</div>
	{+END}
{+START,IF,{$NOT,{GIVE_CONTEXT}}}
</div>
{+END}
{+START,IF,{GIVE_CONTEXT}}
</div></div>
{+END}
