{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{TEXT}

{+START,IF,{$NOT,{NEW}}}
	{$SET,extra_buttons,<a class="menu___generic_admin__delete button_screen" href="{DELETE_URL*}"><span>{!DELETE}</span></a>}
{+END}

{POSTING_FORM}

{REVISION_HISTORY}

