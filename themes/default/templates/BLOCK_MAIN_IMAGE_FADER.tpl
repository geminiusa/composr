{$SET,RAND,{$RAND}}

{+START,IF,{$GET,simple_image_fader}}
	<div class="box box___block_main_image_fader"><div class="box_inner">
		<h2>{!MEDIA}</h2>

		<div class="img_thumb_wrap">
			<a href="{GALLERY_URL*}"><img class="img_thumb" id="image_fader_{$GET,RAND}" src="{FIRST_URL*}" alt="" /></a>
		</div>
	</div></div>
{+END}
{+START,IF,{$NOT,{$GET,simple_image_fader}}}
	<div class="gallery_tease_pic_wrap"><div class="gallery_tease_pic">
		<div class="box box___gallery_tease_pic"><div class="box_inner">
			<div class="float_surrounder">
				<div class="gallery_tease_pic_pic">
					<div class="img_thumb_wrap">
						<a href="{GALLERY_URL*}"><img class="img_thumb" id="image_fader_{$GET,RAND}" src="{FIRST_URL*}" alt="" /></a>
					</div>
				</div>

				<h2 id="image_fader_title_{$GET,RAND}">{!MEDIA}</h2>

				<div class="gallery_tease_pic_teaser" id="image_fader_scrolling_text_{$GET,RAND}">
					<span aria-busy="true"><img id="loading_image" alt="" src="{$IMG*,loading}" /></span>
				</div>
			</div>
		</div></div>
	</div></div>
{+END}

<noscript>
	{+START,LOOP,HTML}
		{_loop_var}
	{+END}
</noscript>

<script>// <![CDATA[
	add_event_listener_abstract(window,'load',function() {
		var data={};
		initialise_image_fader(data,'{$GET%,RAND}');

		{+START,LOOP,TITLES}
			initialise_image_fader_title(data,'{_loop_var;^/}',{_loop_key%});
		{+END}
		{+START,LOOP,HTML}
			initialise_image_fader_html(data,'{_loop_var;^/}',{_loop_key%});
		{+END}
		{+START,LOOP,IMAGES}
			initialise_image_fader_image(data,'{_loop_var;^/}',{_loop_key%},{MILL%},{IMAGES%});
		{+END}
	});
//]]></script>
