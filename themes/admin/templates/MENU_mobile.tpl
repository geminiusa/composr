{$REQUIRE_CSS,menu__mobile}
{$REQUIRE_JAVASCRIPT,menu_mobile}

<a href="{$PAGE_LINK*,:sitemap}" class="mobile_menu_button" onclick="return mobile_menu_button('{MENU|;*}');"><img src="{$IMG*,mobile_menu}" alt="{!MENU}" />	<span>{!MENU}</span></a>

{+START,IF_NON_EMPTY,{CONTENT}}
	<nav class="menu_type__mobile" role="navigation" style="display: none" aria-expanded="false">
		<div class="mobile_search">
			{+START,INCLUDE,ADMIN_ZONE_SEARCH}{+END}
		</div>

		<ul class="nl" id="r_{MENU|*}_d">
			{CONTENT}
		</ul>
	</nav>
{+END}
