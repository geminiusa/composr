<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Edit a zone.
 *
 * @param  ID_TEXT $zone The current name of the zone
 * @param  SHORT_TEXT $title The zone title
 * @param  ID_TEXT $default_page The zones default page
 * @param  SHORT_TEXT $header_text The header text
 * @param  ID_TEXT $theme The theme
 * @param  BINARY $require_session Whether the zone requires a session for pages to be used
 * @param  ID_TEXT $new_zone The new name of the zone
 * @param  boolean $uniqify Whether to force the name as unique, if there's a conflict
 * @param  boolean $skip_afm Whether to skip the AFM because we know it's not needed (or can't be loaded)
 * @param  string $base_url The base URL (blank: natural)
 * @return ID_TEXT The name
 */
function actual_edit_zone($zone, $title, $default_page, $header_text, $theme, $require_session, $new_zone, $uniqify = false, $skip_afm = false, $base_url = '')
{
    if ($zone != $new_zone) {
        require_code('type_sanitisation');
        if (!is_alphanumeric($new_zone, true)) {
            warn_exit(do_lang_tempcode('BAD_CODENAME'));
        }

        if (!is_null($GLOBALS['CURRENT_SHARE_USER'])) {
            warn_exit(do_lang_tempcode('SHARED_INSTALL_PROHIBIT'));
        }

        // Check doesn't already exist
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('zones', 'zone_header_text', array('zone_name' => $new_zone));
        if (!is_null($test)) {
            if ($uniqify) {
                $new_zone .= '_' . uniqid('', true);
            } else {
                warn_exit(do_lang_tempcode('ALREADY_EXISTS', escape_html($new_zone)));
            }
        }

        require_code('abstract_file_manager');
        if (!$skip_afm) {
            force_have_afm_details();
        }
        afm_move($zone, $new_zone);
    }

    $_header_text = $GLOBALS['SITE_DB']->query_select_value('zones', 'zone_header_text', array('zone_name' => $zone));
    $_title = $GLOBALS['SITE_DB']->query_select_value('zones', 'zone_title', array('zone_name' => $zone));

    $map = array(
        'zone_name' => $new_zone,
        'zone_default_page' => $default_page,
        'zone_theme' => $theme,
        'zone_require_session' => $require_session,
    );
    $map += lang_remap('zone_title', $_title, $title);
    $map += lang_remap('zone_header_text', $_header_text, $header_text);
    $GLOBALS['SITE_DB']->query_update('zones', $map, array('zone_name' => $zone), '', 1);

    if ($new_zone != $zone) {
        actual_rename_zone_lite($zone, $new_zone, true);

        $GLOBALS['SITE_DB']->query_update('menu_items', array('i_url' => $new_zone), array('i_url' => $zone), '', 1);
    }

    // If we're in this zone, update the theme
    global $ZONE;
    if ($ZONE['zone_name'] == $zone) {
        $ZONE['theme'] = $theme;
    }

    decache('menu');
    persistent_cache_delete(array('ZONE', $zone));
    persistent_cache_delete('ALL_ZONES');

    require_code('zones2');
    save_zone_base_url($zone, $base_url);

    log_it('EDIT_ZONE', $zone);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        generate_resourcefs_moniker('zone', $zone);
    }

    return $zone;
}

/**
 * Rename a zone in the database.
 *
 * @param  ID_TEXT $zone The old name of the zone
 * @param  ID_TEXT $new_zone The new name of the zone
 * @param  boolean $dont_bother_with_main_row Whether to assume the main zone row has already been renamed as part of a wider editing operation
 */
function actual_rename_zone_lite($zone, $new_zone, $dont_bother_with_main_row = false)
{
    if (!$dont_bother_with_main_row) {
        $GLOBALS['SITE_DB']->query_update('zones', array('zone_name' => $new_zone), array('zone_name' => $zone), '', 1);
        $GLOBALS['SITE_DB']->query_update('group_zone_access', array('zone_name' => $new_zone), array('zone_name' => $zone));
        $GLOBALS['SITE_DB']->query_update('member_zone_access', array('zone_name' => $new_zone), array('zone_name' => $zone));
    } else {
        $GLOBALS['SITE_DB']->query_delete('zones', array('zone_name' => $zone), '', 1);
        $GLOBALS['SITE_DB']->query_delete('group_zone_access', array('zone_name' => $zone));
        $GLOBALS['SITE_DB']->query_delete('member_zone_access', array('zone_name' => $zone));
    }
    $GLOBALS['SITE_DB']->query_update('group_page_access', array('zone_name' => $new_zone), array('zone_name' => $zone));
    $GLOBALS['SITE_DB']->query_update('member_page_access', array('zone_name' => $new_zone), array('zone_name' => $zone));
    $GLOBALS['SITE_DB']->query_update('comcode_pages', array('the_zone' => $new_zone), array('the_zone' => $zone), '', null, null, false, true); // May fail because the table might not exist when this is called
    if (addon_installed('redirects_editor')) {
        $GLOBALS['SITE_DB']->query_update('redirects', array('r_from_zone' => $new_zone), array('r_from_zone' => $zone));
        $GLOBALS['SITE_DB']->query_update('redirects', array('r_to_zone' => $new_zone), array('r_to_zone' => $zone));
    }

    // Copy logo theme images if needed
    require_code('themes2');
    $themes = find_all_themes();
    foreach (array_keys($themes) as $theme) {
        $zone_logo_img = find_theme_image('logo/' . $zone . '-logo', true, true, $theme);
        $zone_logo_img_new = find_theme_image('logo/' . $new_zone . '-logo', true, true, $theme);
        if (($zone_logo_img != '') && ($zone_logo_img_new == '')) {
            $GLOBALS['SITE_DB']->query_delete('theme_images', array('id' => 'logo/' . $new_zone . '-logo', 'theme' => $theme, 'lang' => get_site_default_lang()), '', 1);
            $GLOBALS['SITE_DB']->query_insert('theme_images', array('id' => 'logo/' . $new_zone . '-logo', 'theme' => $theme, 'path' => $zone_logo_img, 'lang' => get_site_default_lang()));
        }
    }

    global $ALL_ZONES_CACHE, $ALL_ZONES_TITLED_CACHE;
    $ALL_ZONES_CACHE = null;
    $ALL_ZONES_TITLED_CACHE = null;

    Self_learning_cache::erase_smart_cache();
}

/**
 * Delete a zone.
 *
 * @param  ID_TEXT $zone The name of the zone
 * @param  boolean $force Force, even if it contains pages
 * @param  boolean $skip_afm Whether to skip the AFM because we know it's not needed (or can't be loaded)
 */
function actual_delete_zone($zone, $force = false, $skip_afm = false)
{
    if (!is_null($GLOBALS['CURRENT_SHARE_USER'])) {
        warn_exit(do_lang_tempcode('SHARED_INSTALL_PROHIBIT'));
    }

    require_code('abstract_file_manager');
    if (!$skip_afm) {
        force_have_afm_details();
    }

    if (!$force) {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        disable_php_memory_limit();

        $pages = find_all_pages_wrap($zone, false, false, FIND_ALL_PAGES__ALL);
        $bad = array();
        foreach (array_keys($pages) as $page) {
            if ((substr($page, 0, 6) != 'panel_') && ($page != 'start')) {
                $bad[] = $page;
            }
        }
        if ($bad != array()) {
            require_lang('zones');
            warn_exit(do_lang_tempcode('DELETE_ZONE_ERROR', '<kbd>' . implode('</kbd>, <kbd>', $bad) . '</kbd>'));
        }
    }

    actual_delete_zone_lite($zone);

    if (file_exists(get_custom_file_base() . '/' . filter_naughty($zone))) {
        afm_delete_directory(filter_naughty($zone), true);
    }
}

/**
 * Delete a zones database stuff.
 *
 * @param  ID_TEXT $zone The name of the zone
 */
function actual_delete_zone_lite($zone)
{
    $zone_header_text = $GLOBALS['SITE_DB']->query_select_value_if_there('zones', 'zone_header_text', array('zone_name' => $zone));
    if (is_null($zone_header_text)) {
        return;
    }
    $zone_title = $GLOBALS['SITE_DB']->query_select_value('zones', 'zone_title', array('zone_name' => $zone));
    delete_lang($zone_header_text);
    delete_lang($zone_title);

    $GLOBALS['SITE_DB']->query_delete('zones', array('zone_name' => $zone), '', 1);
    $GLOBALS['SITE_DB']->query_delete('group_zone_access', array('zone_name' => $zone));
    $GLOBALS['SITE_DB']->query_delete('group_page_access', array('zone_name' => $zone));
    $GLOBALS['SITE_DB']->query_delete('comcode_pages', array('the_zone' => $zone), '', null, null, true); // May fail because the table might not exist when this is called
    if (addon_installed('redirects_editor')) {
        $GLOBALS['SITE_DB']->query_delete('redirects', array('r_from_zone' => $zone));
        $GLOBALS['SITE_DB']->query_delete('redirects', array('r_to_zone' => $zone));
    }
    $GLOBALS['SITE_DB']->query_delete('menu_items', array('i_url' => $zone . ':'));

    if (addon_installed('catalogues')) {
        update_catalogue_content_ref('zone', $zone, '');
    }

    persistent_cache_delete(array('ZONE', $zone));
    persistent_cache_delete('ALL_ZONES');

    global $ALL_ZONES_CACHE, $ALL_ZONES_TITLED_CACHE;
    $ALL_ZONES_CACHE = null;
    $ALL_ZONES_TITLED_CACHE = null;

    log_it('DELETE_ZONE', $zone);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        expunge_resourcefs_moniker('zone', $zone);
    }
}

/**
 * The do-next manager for after content management.
 *
 * @param  tempcode $title The title (output of get_screen_title)
 * @param  ?ID_TEXT $page The name of the page just handled (null: none)
 * @param  ID_TEXT $zone The name of the zone just handled (blank: none/welcome-zone)
 * @param  tempcode $completion_text The text to show (blank: default)
 * @return tempcode The UI
 */
function sitemap_do_next_manager($title, $page, $zone, $completion_text)
{
    if ($completion_text->is_empty()) {
        $completion_text = do_lang_tempcode('SUCCESS');
    }

    require_code('templates_donext');
    $special = array(
        array('menu/_generic_admin/add_one', array('cms_comcode_pages', array('type' => 'edit'), get_module_zone('cms_comcode_pages')), do_lang('COMCODE_PAGE_ADD')),
        array('menu/cms/comcode_page_edit', array('cms_comcode_pages', array('type' => 'browse'), get_module_zone('cms_comcode_pages')), do_lang_tempcode('COMCODE_PAGE_EDIT')),
    );
    if (!is_null($page)) {
        $special = array_merge($special, array(
            array('menu/_generic_admin/edit_this', array('_SELF', array('type' => '_edit', 'page_link' => $zone . ':' . $page), '_SELF'), do_lang('COMCODE_PAGE_EDIT_THIS')),
            array('menu/_generic_admin/view_this', array($page, array(), $zone), do_lang('COMCODE_PAGE_VIEW_THIS')),
        ));
    }
    if (addon_installed('redirects_editor')) {
        require_lang('redirects');
        $special[] = array('menu/adminzone/structure/redirects', array('admin_redirects', array('type' => 'browse'), get_module_zone('admin_redirects')), do_lang_tempcode('REDIRECTS'));
    }
    if (!has_js()) {
        $special = array_merge($special, array(
            array('menu/adminzone/structure/sitemap/page_delete', array('admin_sitemap', array('type' => 'delete'), get_module_zone('admin_sitemap')), do_lang_tempcode('DELETE_PAGES')),
            array('menu/adminzone/structure/sitemap/page_move', array('admin_sitemap', array('type' => 'move'), get_module_zone('admin_sitemap')), do_lang_tempcode('MOVE_PAGES')),
        ));
    } else {
        $special = array_merge($special, array(
            array('menu/adminzone/structure/sitemap/sitemap_editor', array('admin_sitemap', array('type' => 'sitemap'), get_module_zone('admin_sitemap')), do_lang_tempcode('SITEMAP_EDITOR')),
        ));
    }
    return do_next_manager($title, $completion_text,
        $special,
        do_lang('PAGES'),
        /* TYPED-ORDERED LIST OF 'LINKS'   */
        null, // Add one
        null, // Edit this
        null, // Edit one
        null, // View this
        null, // View archive
        null, // Add to category
        null, // Add one category
        null, // Edit one category
        null, // Edit this category
        null // View this category
    );
}

/**
 * Get a list of zones.
 *
 * @param  ?ID_TEXT $sel The zone in the list to select by default (null: use first)
 * @param  ?array $no_go A list of zone to not put into the list (null: none to skip)
 * @param  ?array $reorder A reordering (null: no reordering)
 * @param  ?TIME $updated_since Time from which content must be updated (null: no limit).
 * @return tempcode The list
 */
function create_selection_list_zones($sel = null, $no_go = null, $reorder = null, $updated_since = null)
{
    if (is_null($no_go)) {
        $no_go = array();
    }

    if (($sel === 'site') && (get_option('collapse_user_zones') == '1')) {
        $sel = '';
    }

    if (!is_null($updated_since)) {
        $rows = $GLOBALS['SITE_DB']->query('SELECT zone_name,zone_title FROM ' . get_table_prefix() . 'zones z WHERE EXISTS (SELECT * FROM ' . get_table_prefix() . 'comcode_pages c WHERE z.zone_name=c.the_zone AND p_add_date>' . strval($updated_since) . ') ORDER BY zone_name');
        $zones = array();
        foreach ($rows as $row) {
            $zones[] = array($row['zone_name'], get_translated_text($row['zone_title']));
        }
    } else {
        $zones = find_all_zones(false, true);
    }
    $content = new Tempcode();
    if (!is_null($reorder)) {
        $_zones_a = array();
        $_zones_b = array();
        foreach ($zones as $_zone) {
            list($zone, $title) = $_zone;
            if (in_array($zone, $reorder)) {
                $_zones_a[] = $_zone;
            } else {
                $_zones_b[] = $_zone;
            }
        }
        $zones = array_merge($_zones_a, $_zones_b);
    }
    foreach ($zones as $_zone) {
        list($zone, $title) = $_zone;
        if ((has_zone_access(get_member(), $zone)) && (!in_array($zone, $no_go))) {
            $content->attach(form_input_list_entry($zone, ((!is_null($sel)) && ($zone == $sel)), $title));
        }
    }
    return $content;
}

/**
 * Get a zone chooser interface.
 *
 * @param  boolean $inline Whether the zone chooser will be shown inline to something else (as opposed to providing it's own borderings)
 * @param  ?array $no_go A list of zone to not put into the list (null: none to skip)
 * @param  ?array $reorder A reordering (null: no reordering)
 * @return tempcode The zone chooser
 */
function get_zone_chooser($inline = false, $no_go = null, $reorder = null)
{
    $content = create_selection_list_zones(get_zone_name(), $no_go, $reorder);

    $content = do_template('ZONE_CHOOSE' . ($inline ? '_INLINE' : ''), array('CONTENT' => $content));
    return $content;
}

/**
 * Get the map of names/titles of the available templates.
 *
 * @return array The names and titles of all available templates (title refers to the text within the first [title] tag in the template file)
 */
function get_templates_list()
{
    $templates_dir = get_file_base() . '/data/modules/cms_comcode_pages/' . fallback_lang() . '/';
    $templates = array();
    if (($handle = @opendir($templates_dir)) !== false) {
        $unknown_count = 0;

        while (false !== ($entry = readdir($handle))) {
            if (substr($entry, -4) == '.txt' && $entry[0] != '.') {
                $template_path = $templates_dir . $entry;
                $matches = array();
                if (preg_match('#\[title[^\]]*\](.*)\[/title\]#', file_get_contents($template_path), $matches) != 0) {
                    $template_title = $matches[1];
                } else {
                    $unknown_count++;
                    $template_title = do_lang('UNKNOWN') . strval($unknown_count);
                }
                $templates[basename($entry, '.txt')] = $template_title;
            }
        }
        asort($templates);

        closedir($handle);
    }
    return $templates;
}

/**
 * Read the contents of a template file.
 *
 * @param  string $name The name of the template (based on the filename)
 * @return string The contents of the file (blank if it does not exist)
 */
function get_template_contents($name)
{
    $templates_dir = get_file_base() . '/data_custom/modules/cms_comcode_pages/' . either_param_string('lang', user_lang()) . '/';
    $template_path = $templates_dir . $name . '.txt';
    if (!is_file($template_path)) {
        $templates_dir = get_file_base() . '/data_custom/modules/cms_comcode_pages/' . fallback_lang() . '/';
        $template_path = $templates_dir . $name . '.txt';
    }
    $templates_dir = get_file_base() . '/data/modules/cms_comcode_pages/' . either_param_string('lang', user_lang()) . '/';
    $template_path = $templates_dir . $name . '.txt';
    if (!is_file($template_path)) {
        $templates_dir = get_file_base() . '/data/modules/cms_comcode_pages/' . fallback_lang() . '/';
        $template_path = $templates_dir . $name . '.txt';
    }
    if (!is_file($template_path)) {
        return '';
    }

    return file_get_contents($template_path);
}

/**
 * Save a Comcode page.
 *
 * @param  ID_TEXT $zone The zone
 * @param  ID_TEXT $new_file The page
 * @param  LANGUAGE_NAME $lang The language
 * @param  ID_TEXT $text The page text
 * @param  BINARY $validated The validated status
 * @param  ?ID_TEXT $parent_page The page parent (null: none)
 * @param  integer $order The page order
 * @param  ?TIME $add_time Add time (null: now)
 * @param  ?TIME $edit_time Edit time (null: not edited)
 * @param  BINARY $show_as_edit Whether to show as edited
 * @param  ?MEMBER $submitter The submitter (null: current member)
 * @param  ?ID_TEXT $file The old page name (null: not being renamed)
 * @param  SHORT_TEXT $meta_keywords Meta keywords for this resource (blank: implicit)
 * @param  LONG_TEXT $meta_description Meta description for this resource (blank: implicit)
 * @return PATH The save path
 */
function save_comcode_page($zone, $new_file, $lang, $text, $validated, $parent_page = null, $order = 0, $add_time = null, $edit_time = null, $show_as_edit = 0, $submitter = null, $file = null, $meta_keywords = '', $meta_description = '')
{
    if (is_null($submitter)) {
        $submitter = get_member();
    }
    if (is_null($add_time)) {
        $add_time = time();
    }
    if (is_null($file)) {
        $file = $new_file; // Not renamed
    }

    // Check page name
    require_code('type_sanitisation');
    if (!is_alphanumeric($new_file)) {
        warn_exit(do_lang_tempcode('BAD_CODENAME'));
    }
    require_code('zones2');
    check_page_name($zone, $new_file);

    require_code('urls2');
    suggest_new_idmoniker_for($new_file, '', $zone, $zone, $new_file);

    // Handle if the page was renamed - move stuff over
    $renaming_page = ($new_file != $file);
    if ($renaming_page) {
        // Got to rename against multiple possible languages
        $langs = find_all_langs(true);
        $rename_map = array();
        foreach (array_keys($langs) as $lang) {
            $old_path = zone_black_magic_filterer(filter_naughty($zone) . (($zone != '') ? '/' : '') . 'pages/comcode_custom/' . $lang . '/' . $file . '.txt', true);
            if (file_exists(get_file_base() . '/' . $old_path)) {
                $new_path = zone_black_magic_filterer(filter_naughty($zone) . (($zone != '') ? '/' : '') . 'pages/comcode_custom/' . $lang . '/' . $new_file . '.txt', true);
                if ((file_exists($new_path)) && (fileinode($new_path) != fileinode($old_path)/*avoid issue on case insensitive file systems while changing case*/)) {
                    warn_exit(do_lang_tempcode('ALREADY_EXISTS', escape_html($zone . ':' . $new_file)));
                }
                $rename_map[$old_path] = $new_path;
            }
            if (file_exists(get_file_base() . '/' . str_replace('/comcode_custom/', '/comcode/', $old_path))) {
                attach_message(do_lang_tempcode('ORIGINAL_PAGE_NO_RENAME'), 'warn');
            }
        }
        foreach ($rename_map as $path => $new_path) {
            rename(get_custom_file_base() . '/' . $path, get_custom_file_base() . '/' . $new_path);
        }

        // Got to rename various resources

        $GLOBALS['SITE_DB']->query_update('attachment_refs', array('r_referer_id' => $new_file), array('r_referer_id' => $file, 'r_referer_type' => 'comcode_page'));
 
        if (addon_installed('catalogues')) {
            update_catalogue_content_ref('comcode_page', $file, $new_file);
        }

        if (addon_installed('awards')) {
            $types = $GLOBALS['SITE_DB']->query_select('award_types', array('id'), array('a_content_type' => 'comcode_page'));
            foreach ($types as $type) {
                $GLOBALS['SITE_DB']->query_update('award_archive', array('content_id' => $new_file), array('content_id' => $file, 'a_type_id' => $type['id']));
            }
        }

        // Main page record rename
        $GLOBALS['SITE_DB']->query_update('comcode_pages', array(
            'p_parent_page' => $new_file,
        ), array('the_zone' => $zone, 'p_parent_page' => $file));
    }

    // Set meta-data
    require_code('seo2');
    if (($meta_keywords == '') && ($meta_description == '')) {
        seo_meta_set_for_implicit('comcode_page', $zone . ':' . $new_file, array($text), $text);
    } else {
        seo_meta_set_for_explicit('comcode_page', $zone . ':' . $new_file, $meta_keywords, $meta_description);
    }

    // Store in DB
    $GLOBALS['SITE_DB']->query_delete('comcode_pages', array( // To support rename
                                                              'the_zone' => $zone,
                                                              'the_page' => $file,
    ));
    $GLOBALS['SITE_DB']->query_delete('comcode_pages', array( // To stop conflicts
                                                              'the_zone' => $zone,
                                                              'the_page' => $new_file,
    ));
    $GLOBALS['SITE_DB']->query_insert('comcode_pages', array(
        'the_zone' => $zone,
        'the_page' => $new_file,
        'p_parent_page' => $parent_page,
        'p_validated' => $validated,
        'p_edit_date' => $edit_time,
        'p_add_date' => $add_time,
        'p_submitter' => $submitter,
        'p_show_as_edit' => $show_as_edit,
        'p_order' => $order,
    ));

    // Store page on disk
    $fullpath = zone_black_magic_filterer(get_custom_file_base() . '/' . filter_naughty($zone) . '/pages/comcode_custom/' . filter_naughty($lang) . '/' . filter_naughty($new_file) . '.txt');
    if ((!file_exists($fullpath)) || ($text != file_get_contents($fullpath))) {
        if (!file_exists(dirname($fullpath))) {
            require_code('files2');
            make_missing_directory(dirname($fullpath));
        }

        $myfile = @fopen($fullpath, GOOGLE_APPENGINE ? 'wb' : 'at');
        if ($myfile === false) {
            intelligent_write_error($fullpath);
        }
        @flock($myfile, LOCK_EX);
        if (!GOOGLE_APPENGINE) {
            ftruncate($myfile, 0);
        }
        if (fwrite($myfile, $text) < strlen($text)) {
            warn_exit(do_lang_tempcode('COULD_NOT_SAVE_FILE'));
        }
        @flock($myfile, LOCK_UN);
        fclose($myfile);
        sync_file($fullpath);
        fix_permissions($fullpath);

        $file_changed = true;
    } else {
        $file_changed = false;
    }

    // Save backup
    if ((file_exists($fullpath)) && (get_option('store_revisions') == '1') && ($file_changed)) {
        $time = time();
        @copy($fullpath, $fullpath . '.' . strval($time)) or intelligent_write_error($fullpath . '.' . strval($time));
        fix_permissions($fullpath . '.' . strval($time));
        sync_file($fullpath . '.' . strval($time));
    }

    // Empty caching
    erase_persistent_cache();
    //persistent_cache_delete(array('PAGE_INFO')); Already erases above
    decache('main_comcode_page_children');
    decache('menu');
    $caches = $GLOBALS['SITE_DB']->query_select('cached_comcode_pages', array('string_index'), array('the_zone' => $zone, 'the_page' => $file));
    $GLOBALS['SITE_DB']->query_delete('cached_comcode_pages', array('the_zone' => $zone, 'the_page' => $file));
    foreach ($caches as $cache) {
        delete_lang($cache['string_index']);
    }

    // Log
    log_it('COMCODE_PAGE_EDIT', $new_file, $zone);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        generate_resourcefs_moniker('comcode_page', $zone . ':' . $new_file);
    }

    return $fullpath;
}

/**
 * Delete a Composr page.
 *
 * @param  ID_TEXT $zone The zone
 * @param  ID_TEXT $page The page
 * @param  ?ID_TEXT $type The page type (null: Comcode page in Composr's fallback language) [NB: page is deleted in all languages regardless of which is given]
 * @param  boolean $use_afm Whether to use the AFM
 */
function delete_cms_page($zone, $page, $type = null, $use_afm = false)
{
    if (is_null($type)) {
        $type = 'comcode_custom/' . fallback_lang();
    }

    $_page = '';
    if (substr($type, 0, 7) == 'modules') {
        $_page = $page . '.php';
    } elseif (substr($type, 0, 7) == 'comcode') {
        $_page = $page . '.txt';
    } elseif (substr($type, 0, 4) == 'html') {
        $_page = $page . '.htm';
    }

    $GLOBALS['SITE_DB']->query_delete('menu_items', array('i_url' => $zone . ':' . $page));
    decache('menu');

    if ((substr($type, 0, 7) == 'comcode') || (substr($type, 0, 4) == 'html')) {
        $type_shortened = preg_replace('#/.+#', '', $type);

        if ((substr($type, 0, 7) == 'comcode') && (get_option('store_revisions') == '1')) {
            $time = time();
            $fullpath = zone_black_magic_filterer(((strpos($type, 'comcode/') !== false) ? get_file_base() : get_custom_file_base()) . '/' . filter_naughty($zone) . (($zone != '') ? '/' : '') . 'pages/' . filter_naughty($type) . '/' . $_page);
            $bs_path = zone_black_magic_filterer(str_replace('/comcode/', '/comcode_custom/', $fullpath) . '.' . strval($time));
            @copy($fullpath, $bs_path) or intelligent_write_error($fullpath);
            sync_file($bs_path);
            fix_permissions($bs_path);
        }

        $langs = find_all_langs(true);
        foreach (array_keys($langs) as $lang) {
            $_path = zone_black_magic_filterer(filter_naughty($zone) . (($zone != '') ? '/' : '') . 'pages/' . filter_naughty($type_shortened) . '/' . $lang . '/' . $_page, true);
            $path = ((strpos($type, 'comcode/') !== false) ? get_file_base() : get_custom_file_base()) . '/' . $_path;
            if (file_exists($path)) {
                if ($use_afm) {
                    afm_delete_file($_path);
                } else {
                    unlink(get_custom_file_base() . '/' . $_path);
                    sync_file($_path);
                }
            }
        }

        if (substr($type, 0, 7) == 'comcode') {
            require_code('attachments2');
            require_code('attachments3');
            delete_comcode_attachments('comcode_page', $zone . ':' . $page);
            $GLOBALS['SITE_DB']->query_delete('cached_comcode_pages', array('the_page' => $page, 'the_zone' => $zone));
            $GLOBALS['SITE_DB']->query_delete('comcode_pages', array('the_page' => $page, 'the_zone' => $zone));
            erase_persistent_cache();
            decache('main_comcode_page_children');

            require_code('seo2');
            seo_meta_erase_storage('comcode_page', $zone . ':' . $page);
        }
    } else {
        $_path = zone_black_magic_filterer(filter_naughty($zone) . (($zone != '') ? '/' : '') . 'pages/' . filter_naughty($type) . '/' . $_page, true);
        $path = ((strpos($type, '_custom') === false) ? get_file_base() : get_custom_file_base()) . '/' . $_path;
        if (file_exists($path)) {
            if ($use_afm) {
                afm_delete_file($_path);
            } else {
                unlink(get_custom_file_base() . '/' . $_path);
                sync_file($_path);
            }
        }
    }

    $GLOBALS['SITE_DB']->query_delete('https_pages', array('https_page_name' => $page), '', 1);

    if (addon_installed('catalogues')) {
        update_catalogue_content_ref('comcode_page', $page, '');
    }

    log_it('DELETE_PAGES', $page);
}
