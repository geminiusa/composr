{+START,IF_PASSED,QUICK_VERSION}{+START,IF_PASSED,QUICK_FILESIZE}{+START,IF_PASSED,QUICK_URL}
	<div class="dlHolder">
		<div class="dlHead Grn">
			Automatic extractor <span>Recommended</span>
		</div>

		<div class="dlBody">
			<p>This package ("quick installer") will self-extract on your server and automatically set all permissions.</p>

			<p>Works on most servers (needs PHP FTP support or suEXEC on your server).</p>

			<div class="sept"></div>

			<p><a class="alLft nice_link" href="{QUICK_URL*}">Download &dtrif;</a> <a class="alRht" href="#">v{QUICK_VERSION*} | {QUICK_FILESIZE*}</a></p>
		</div>
	</div>
{+END}

{+START,IF_PASSED,MANUAL_VERSION}{+START,IF_PASSED,MANUAL_FILESIZE}{+START,IF_PASSED,MANUAL_URL}
	<div class="dlHolder">
		<div class="dlHead Blu">
			Manual extractor <span>Slower; requires chmodding</span>
		</div>

		<div class="dlBody">
			<p>This is a zip containing all Composr files (several thousand). It is much slower, and only recommended if you cannot use the quick installer. Some chmodding is required.</p>

			<p><strong>Do not use this for upgrading.</strong></p>

			<div class="sept"></div>

			<p><a class="alLft nice_link" href="{MANUAL_URL*}">Download &dtrif;</a> <a class="alRht" href="{MANUAL_URL*}">v{MANUAL_VERSION*} | {MANUAL_FILESIZE*}</a></p>
		</div>
	</div>
{+END}

{+START,IF_PASSED,BLEEDINGMANUAL_VERSION}{+START,IF_PASSED,BLEEDINGMANUAL_FILESIZE}{+START,IF_PASSED,BLEEDINGMANUAL_URL}
{+START,IF_PASSED,BLEEDINGQUICK_VERSION}{+START,IF_PASSED,BLEEDINGQUICK_FILESIZE}{+START,IF_PASSED,BLEEDINGQUICK_URL}
	<div class="dlHolder">
		<div class="dlHead">
			Bleeding edge <span>Unstable</span>
		</div>

		<div class="dlBody">
			<p>Are you able to {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},alpha,beta}-test the new version: v{BLEEDINGQUICK_VERSION*}?<br />
			It {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},<strong>will not be stable</strong> like,<strong>may not be as stable</strong> as} our main version{+START,IF_PASSED,QUICK_VERSION} (v{QUICK_VERSION*}){+END}.</p>

			<div class="sept"></div>

			<p><a class="alLft nice_link" href="{BLEEDINGQUICK_URL*}">Download automatic extractor &dtrif;</a> <a class="alRht nice_link" href="{BLEEDINGMANUAL_URL*}">Download manual extractor &dtrif;</a></p>
		</div>
	</div>
{+END}{+END}{+END}
{+END}{+END}{+END}
