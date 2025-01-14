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
 * @package    banners
 */

/**
 * Standard code module initialisation function.
 */
function init__banners()
{
    define('BANNER_PERMANENT', 0);
    define('BANNER_CAMPAIGN', 1);
    define('BANNER_DEFAULT', 2);
}

/**
 * Get tempcode for a banner 'feature box' for the given row
 *
 * @param  array $row The database field row of it
 * @param  ID_TEXT $zone The zone to use
 * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
 * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
 * @return tempcode A box for it, linking to the full page
 */
function render_banner_box($row, $zone = '_SEARCH', $give_context = true, $guid = '')
{
    require_lang('banners');

    $just_banner_row = db_map_restrict($row, array('name', 'caption'));

    $url = new Tempcode();

    $_title = $row['name'];
    $title = $give_context ? do_lang('CONTENT_IS_OF_TYPE', do_lang('BANNER'), $_title) : $_title;

    $summary = show_banner($row['name'], $row['b_title_text'], get_translated_tempcode('banners', $just_banner_row, 'caption'), $row['b_direct_code'], $row['img_url'], '', $row['site_url'], $row['the_type'], $row['submitter']);

    return do_template('SIMPLE_PREVIEW_BOX', array(
        '_GUID' => ($guid != '') ? $guid : 'aaea5f7f64297ab46aa3b3182fb57c37',
        'ID' => $row['name'],
        'TITLE' => $title,
        'TITLE_PLAIN' => $_title,
        'SUMMARY' => $summary,
        'URL' => $url,
        'FRACTIONAL_EDIT_FIELD_NAME' => $give_context ? null : 'name',
        'FRACTIONAL_EDIT_FIELD_URL' => $give_context ? null : '_SEARCH:cms_banners:__edit:' . $row['name'],
    ));
}

/**
 * Get tempcode for a banner type 'feature box' for the given row
 *
 * @param  array $row The database field row of it
 * @param  ID_TEXT $zone The zone to use
 * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
 * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
 * @return tempcode A box for it, linking to the full page
 */
function render_banner_type_box($row, $zone = '_SEARCH', $give_context = true, $guid = '')
{
    require_lang('banners');

    $url = new Tempcode();

    $_title = $row['id'];
    if ($_title == '') {
        $_title = do_lang('GENERAL');
    }
    $title = $give_context ? do_lang('CONTENT_IS_OF_TYPE', do_lang('_BANNER_TYPE'), $_title) : $_title;

    $num_entries = $GLOBALS['SITE_DB']->query_select_value('banners', 'COUNT(*)', array('validated' => 1));
    $entry_details = do_lang_tempcode('CATEGORY_SUBORDINATE_2', escape_html(integer_format($num_entries)));

    return do_template('SIMPLE_PREVIEW_BOX', array(
        '_GUID' => ($guid != '') ? $guid : 'ba1f8d9da6b65415483d0d235f29c3d4',
        'ID' => $row['id'],
        'TITLE' => $title,
        'SUMMARY' => '',
        'ENTRY_DETAILS' => $entry_details,
        'URL' => $url,
    ));
}

/**
 * Show a banner according to GET parameter specification.
 *
 * @param  boolean $ret Whether to return a result rather than outputting
 * @param  ?string $type Whether we are displaying or click-processing (null: get from URL param)
 * @set    "click" ""
 * @param  ?string $dest Specific banner to display (null: get from URL param) (blank: randomise)
 * @param  ?string $b_type Banner type to display (null: get from URL param)
 * @param  ?string $source The banner advertisor who is actively displaying the banner (calling up this function) and hence is rewarded (null: get from URL param) (blank: our own site)
 * @param  ?integer $width The width (null: standard for banner type)
 * @param  ?integer $height The height (null: standard for banner type)
 * @return ?tempcode Result (null: we weren't asked to return the result)
 */
function banners_script($ret = false, $type = null, $dest = null, $b_type = null, $source = null, $width = null, $height = null)
{
    require_code('images');
    require_lang('banners');

    // If this is being called for a click through
    if ($type === null) {
        $type = get_param_string('type', '');
    }

    if ($type == 'click') {
        // Input parameters
        if ($source === null) {
            $source = get_param_string('source', '');
        }
        if ($dest === null) {
            $dest = get_param_string('dest', '');
        }

        // Has the banner been clicked before?
        $test = $GLOBALS['SITE_DB']->query_select_value('banner_clicks', 'MAX(c_date_and_time)', array('c_ip_address' => get_ip_address(), 'c_banner_id' => $dest));
        $unique = ($test === null) || ($test < time() - 60 * 60 * 24);

        // Find the information about the dest
        $rows = $GLOBALS['SITE_DB']->query_select('banners', array('site_url', 'hits_to', 'campaign_remaining'), array('name' => $dest));
        if (!array_key_exists(0, $rows)) {
            fatal_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];
        $url = $myrow['site_url'];
        $page_link = url_to_page_link($url);
        if ($page_link != '') {
            $keep = symbol_tempcode('KEEP', array((strpos($url, '?') === false) ? '1' : '0'));
            $url .= $keep->evaluate();
        }

        if ($unique) {
            if (get_db_type() != 'xml') {
                if (!$GLOBALS['SITE_DB']->table_is_locked('banners')) {
                    $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET hits_to=(hits_to+1) WHERE ' . db_string_equal_to('name', $dest), 1);
                }
            }
            $campaignremaining = $myrow['campaign_remaining'];
            if ($campaignremaining !== null) {
                if (get_db_type() != 'xml') {
                    if (!$GLOBALS['SITE_DB']->table_is_locked('banners')) {
                        $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET campaign_remaining=(campaign_remaining-1) WHERE ' . db_string_equal_to('name', $dest), 1);
                    }
                }
            }
        }

        // Find the information about the source
        if (($source != '') && ($unique)) {
            $rows = $GLOBALS['SITE_DB']->query_select('banners', array('hits_from', 'campaign_remaining'), array('name' => $source));
            if (!array_key_exists(0, $rows)) {
                fatal_exit(do_lang_tempcode('BANNER_MISSING_SOURCE'));
            }
            $myrow = $rows[0];
            if (get_db_type() != 'xml') {
                $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET hits_from=(hits_from+1) WHERE ' . db_string_equal_to('name', $source), 1);
            }
            $campaignremaining = $myrow['campaign_remaining'];
            if ($campaignremaining !== null) {
                if (get_db_type() != 'xml') {
                    if (!$GLOBALS['SITE_DB']->table_is_locked('banners')) {
                        $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET campaign_remaining=(campaign_remaining+1) WHERE ' . db_string_equal_to('name', $source), 1);
                    }
                }
            }
        }

        // Log the click
        $GLOBALS['SITE_DB']->query_insert('banner_clicks', array(
            'c_date_and_time' => time(),
            'c_member_id' => get_member(),
            'c_ip_address' => get_ip_address(),
            'c_source' => $source,
            'c_banner_id' => $dest
        ));

        if ((strpos($url, "\n") !== false) || (strpos($url, "\r") !== false)) {
            log_hack_attack_and_exit('HEADER_SPLIT_HACK');
        }
        header('Location: ' . $url);
    } // Being called to display a banner
    else {
        if ($dest === null) {
            $dest = get_param_string('dest', '');
        }
        if ($b_type === null) {
            $b_type = get_param_string('b_type', '');
        }

        // A community banner then...
        // ==========================

        // Input parameters (clicks-in from source site)
        if ($source === null) {
            $source = get_param_string('source', '');
        }

        // To allow overriding to specify a specific banner
        if ($dest != '') {
            $myquery = 'SELECT * FROM ' . get_table_prefix() . 'banners WHERE ' . db_string_equal_to('name', $dest);
        } else {
            $myquery = 'SELECT * FROM ' . get_table_prefix() . 'banners WHERE ((the_type<>' . strval(BANNER_CAMPAIGN) . ') OR (campaign_remaining>0)) AND ((expiry_date IS NULL) OR (expiry_date>' . strval(time()) . ')) AND ' . db_string_not_equal_to('name', $source) . ' AND validated=1 AND ' . db_string_equal_to('b_type', $b_type);
        }

        // Run Query
        $rows = $GLOBALS['SITE_DB']->query($myquery, 500/*reasonable limit - old ones should be turned off*/, null, true, true, array('caption' => 'SHORT_TRANS__COMCODE'));
        if ($rows === null) {
            $rows = array(); // Error, but tolerate it as it could be on each page load
        }

        // Filter out what we don't have permission for
        if (get_option('use_banner_permissions') == '1') {
            require_code('permissions');
            $groups = _get_where_clause_groups(get_member());
            if ($groups !== null) {
                $perhaps = collapse_1d_complexity('category_name', $GLOBALS['SITE_DB']->query('SELECT DISTINCT category_name FROM ' . get_table_prefix() . 'group_category_access WHERE ' . db_string_equal_to('module_the_name', 'banners') . ' AND (' . $groups . ')', null, null, false, true));
                $new_rows = array();
                foreach ($rows as $row) {
                    if (in_array($row['name'], $perhaps)) {
                        $new_rows[] = $row;
                    }
                }
                $rows = $new_rows;
            }
        }

        // Are we allowed to show default banners?
        $show_defaults = true;
        foreach ($rows as $counter => $myrow) {
            if ($myrow['the_type'] == BANNER_CAMPAIGN) {
                $show_defaults = false;
            }
        }

        // Remove ones already shown on this page-view
        static $shown_already = array(); // NB: Holds shown ones for any banner types, not specifically the restraints we are working on here. This could be true if you have multiple banner spots: count($shown_already)>count($rows)
        if (!running_script('banner')) {
            $old_rows = $rows;
            foreach ($rows as $counter => $myrow) {
                if (array_key_exists($myrow['name'], $shown_already)) {
                    unset($rows[$counter]);
                }
            }
            if (count($rows) == 0) {
                $rows = $old_rows;
            }
        }

        // Count the total of all importance_modulus entries
        $tally = 0;
        $counter = 0;
        $bound = array();
        shuffle($rows); // Should not be needed, but mt_rand seems to not be very good when running from the website. Also does a re-index, which we require.
        while (array_key_exists($counter, $rows)) {
            $myrow = $rows[$counter];

            if (($myrow['the_type'] == 2) && (!$show_defaults)) {
                $myrow['importance_modulus'] = 0;
            }
            $tally += $myrow['importance_modulus'];
            $bound[$counter] = $tally;
            $counter++;
        }
        if ($tally == 0) {
            require_code('permissions');
            if ((has_actual_page_access(null, 'cms_banners')) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_banners'))) {
                $add_banner_url = build_url(array('page' => 'cms_banners', 'type' => 'add', 'b_type' => $b_type), get_module_zone('cms_banners'));
            } else {
                $add_banner_url = new Tempcode();
            }
            $content = do_template('BANNERS_NONE', array('_GUID' => 'b786ec327365d1ef38134ce401db9dd2', 'ADD_BANNER_URL' => $add_banner_url));
            if ($ret) {
                return $content;
            }
            $echo = do_template('BASIC_HTML_WRAP', array('_GUID' => '00c8549b88dac8a1291450eb5b681d80', 'TARGET' => '_top', 'TITLE' => do_lang_tempcode('BANNER'), 'CONTENT' => $content));
            $echo->evaluate_echo();
            return null;
        }

        // Choose which banner to show from the results
        $rand = mt_rand(1, $tally);
        for ($i = 0; $i < $counter; $i++) {
            if ($rand <= $bound[$i]) {
                break;
            }
        }

        $name = $rows[$i]['name'];
        $shown_already[$name] = true;

        // Update the counts (ones done per-view)
        if ((get_db_type() != 'xml') && (get_value('no_banner_count_updates') !== '1')) {
            if (!$GLOBALS['SITE_DB']->table_is_locked('banners')) {
                $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET views_to=(views_to+1) WHERE ' . db_string_equal_to('name', $name), 1, null, false, true);
            }
        }
        if ($source != '') {
            if (get_db_type() != 'xml') {
                if (!$GLOBALS['SITE_DB']->table_is_locked('banners')) {
                    $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'banners SET views_from=(views_from+1) WHERE ' . db_string_equal_to('name', $name), 1, null, false, true);
                }
            }
        }

        // Display!
        $img = $rows[$i]['img_url'];
        $caption = get_translated_tempcode('banners', $rows[$i], 'caption');
        $content = show_banner($name, $rows[$i]['b_title_text'], $caption, array_key_exists('b_direct_code', $rows[$i]) ? $rows[$i]['b_direct_code'] : '', $img, $source, $rows[$i]['site_url'], $rows[$i]['b_type'], $rows[$i]['submitter'], $width, $height);
        if ($ret) {
            return $content;
        }
        $echo = do_template('BASIC_HTML_WRAP', array('_GUID' => 'd23424ded86c850f4ae0006241407ff9', 'TITLE' => do_lang_tempcode('BANNER'), 'CONTENT' => $content));
        $echo->evaluate_echo();
    }

    return null;
}

/**
 * Get a nice, formatted XHTML list to select a banner type
 *
 * @param  ?ID_TEXT $it The currently selected licence (null: none selected)
 * @return tempcode The list of categories
 */
function create_selection_list_banner_types($it = null)
{
    $list = new Tempcode();
    $rows = $GLOBALS['SITE_DB']->query_select('banner_types', array('id', 't_image_width', 't_image_height', 't_is_textual'), null, 'ORDER BY id');
    foreach ($rows as $row) {
        $caption = ($row['id'] == '') ? do_lang('GENERAL') : $row['id'];

        if ($row['t_is_textual'] == 1) {
            $type_line = do_lang_tempcode('BANNER_TYPE_LINE_TEXTUAL', $caption);
        } else {
            $type_line = do_lang_tempcode('BANNER_TYPE_LINE', $caption, strval($row['t_image_width']), strval($row['t_image_height']));
        }

        $list->attach(form_input_list_entry($row['id'], $it === $row['id'], $type_line));
    }
    return $list;
}

/**
 * Get the tempcode for the display of the defined banner.
 *
 * @param  ID_TEXT $name The name of the banner
 * @param  SHORT_TEXT $title_text The title text of the banner (displayed for a text banner only)
 * @param  tempcode $caption The caption of the banner
 * @param  LONG_TEXT $direct_code The full HTML/PHP for the banner
 * @param  URLPATH $img_url The URL to the banner image
 * @param  ID_TEXT $source The name of the banner for the site that will get the return-hit (blank: none)
 * @param  URLPATH $url The URL to the banner's target
 * @param  ID_TEXT $b_type The banner type
 * @param  MEMBER $submitter The submitting user
 * @param  ?integer $width The width (null: standard for banner type)
 * @param  ?integer $height The height (null: standard for banner type)
 * @return tempcode The rendered banner
 */
function show_banner($name, $title_text, $caption, $direct_code, $img_url, $source, $url, $b_type, $submitter, $width = null, $height = null)
{
    // If this is an image, we <img> it, else we <iframe> it
    require_code('images');
    if ($img_url != '') { // Flash/Image/Iframe
        if (substr($img_url, -4) == '.swf') { // Flash
            if (url_is_local($img_url)) {
                $img_url = get_custom_base_url() . '/' . $img_url;
            }
            $_banner_type_row = $GLOBALS['SITE_DB']->query_select('banner_types', array('t_image_width', 't_image_height'), array('id' => $b_type), '', 1);
            if (is_null($width)) {
                if (array_key_exists(0, $_banner_type_row)) {
                    $banner_type_row = $_banner_type_row[0];
                } else {
                    $banner_type_row = array('t_image_width' => 728, 't_image_height' => 90);
                }
            } else {
                $banner_type_row = array('t_image_width' => $width, 't_image_height' => $height);
            }
            $content = do_template('BANNER_FLASH', array('_GUID' => '25525a3722715e79a83af4cec53fe072', 'B_TYPE' => $b_type, 'WIDTH' => strval($banner_type_row['t_image_width']), 'HEIGHT' => strval($banner_type_row['t_image_height']), 'SOURCE' => $source, 'DEST' => $name, 'CAPTION' => $caption, 'IMG' => $img_url));
        } elseif (($url != '') || (is_image($img_url))) { // Image; Can't rely on image check, because often they have script-generated URLs
            if (url_is_local($img_url)) {
                if (substr($img_url, 0, 12) == 'data/images/') {
                    $img_url = cdn_filter(get_base_url() . '/' . $img_url);
                } else {
                    $img_url = get_custom_base_url() . '/' . $img_url;
                }
            }
            static $banner_type_rows = array();
            if (isset($banner_type_rows[$b_type])) {
                $banner_type_row = $banner_type_rows[$b_type];
            } else {
                $_banner_type_row = $GLOBALS['SITE_DB']->query_select('banner_types', array('t_image_width', 't_image_height'), array('id' => $b_type), '', 1);
                if (is_null($width)) {
                    if (array_key_exists(0, $_banner_type_row)) {
                        $banner_type_row = $_banner_type_row[0];
                    } else {
                        $banner_type_row = array('t_image_width' => 728, 't_image_height' => 90);
                    }
                } else {
                    $banner_type_row = array('t_image_width' => $width, 't_image_height' => $height);
                }
                $banner_type_rows[$b_type] = $banner_type_row;
            }
            $local = (url_is_local($url)) || (substr($url, 0, strlen(get_base_url())) == get_base_url());
            $content = do_template('BANNER_IMAGE', array('_GUID' => '6aaf45b7bb7349393024c24458549e9e', 'LOCAL' => $local, 'URL' => $url, 'B_TYPE' => $b_type, 'WIDTH' => strval($banner_type_row['t_image_width']), 'HEIGHT' => strval($banner_type_row['t_image_height']), 'SOURCE' => $source, 'DEST' => $name, 'CAPTION' => $caption, 'IMG' => $img_url));
        } else { // Iframe
            if (url_is_local($img_url)) {
                $img_url = get_custom_base_url() . '/' . $img_url;
            }
            $_banner_type_row = $GLOBALS['SITE_DB']->query_select('banner_types', array('t_image_width', 't_image_height'), array('id' => $b_type), '', 1);
            if (is_null($width)) {
                if (array_key_exists(0, $_banner_type_row)) {
                    $banner_type_row = $_banner_type_row[0];
                } else {
                    $banner_type_row = array('t_image_width' => 728, 't_image_height' => 90);
                }
            } else {
                $banner_type_row = array('t_image_width' => $width, 't_image_height' => $height);
            }
            $content = do_template('BANNER_IFRAME', array('_GUID' => 'deeef9834bc308b5d07e025ab9c04c0e', 'B_TYPE' => $b_type, 'IMG' => $img_url, 'WIDTH' => strval($banner_type_row['t_image_width']), 'HEIGHT' => strval($banner_type_row['t_image_height'])));
        }
    } else { // Text/HTML/PHP
        if ($direct_code == '') { // Text
            if ($url == '') {
                $filtered_url = '';
            } else {
                $filtered_url = (strpos($url, '://') !== false) ? substr($url, strpos($url, '://') + 3) : $url;
                if (strpos($filtered_url, '/') !== false) {
                    $filtered_url = substr($filtered_url, 0, strpos($filtered_url, '/'));
                }
            }
            $content = do_template('BANNER_TEXT', array('_GUID' => '18ff8f7b14f5ca30cc19a2ad11ecdd62', 'B_TYPE' => $b_type, 'TITLE_TEXT' => $title_text, 'CAPTION' => $caption, 'SOURCE' => $source, 'DEST' => $name, 'URL' => $url, 'FILTERED_URL' => $filtered_url));
        } else { // HTML/PHP
            require_code('permissions');
            if (has_privilege($submitter, 'use_html_banner')) {
                if (is_null($GLOBALS['CURRENT_SHARE_USER'])) { // Only allow PHP code if not a shared install
                    $matches = array();
                    $num_matches = preg_match_all('#\<\?(.*)\?\>#U', $direct_code, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        if (has_privilege($submitter, 'use_php_banner')) {
                            $php_code = $matches[1][$i];
                            if (substr($php_code, 0, 3) == 'php') {
                                $php_code = substr($php_code, 3);
                            }
                            ob_start();
                            $evaled = eval($php_code);
                            if (!is_string($evaled)) {
                                $evaled = '';
                            }
                            $evaled .= ob_get_clean();
                        } else {
                            $evaled = do_lang('BANNER_PHP_NOT_RUN');
                        }
                        $direct_code = str_replace($matches[0][$i], $evaled, $direct_code);
                    }
                }
                $content = make_string_tempcode($direct_code);
            } else {
                $content = do_lang_tempcode('BANNER_HTML_NOT_RUN');
            }
        }
    }

    return $content;
}

/**
 * Get a list of banners.
 *
 * @param  ?AUTO_LINK $it The ID of the banner selected by default (null: no specific default)
 * @param  ?MEMBER $only_owned Only show banners owned by the member (null: no such restriction)
 * @return tempcode The list
 */
function create_selection_list_banners($it = null, $only_owned = null)
{
    $where = is_null($only_owned) ? null : array('submitter' => $only_owned);
    $rows = $GLOBALS['SITE_DB']->query_select('banners', array('name'), $where, 'ORDER BY name', 150);
    if (count($rows) == 300) {
        $rows = $GLOBALS['SITE_DB']->query_select('banners', array('name'), $where, 'ORDER BY add_date DESC', 150);
    }
    $out = new Tempcode();
    foreach ($rows as $myrow) {
        $selected = ($myrow['name'] == $it);
        $out->attach(form_input_list_entry($myrow['name'], $selected));
    }

    return $out;
}
