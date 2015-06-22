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
 * @package    core_cns
 */

/**
 * Block class.
 */
class Block_main_members
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array(
            'display_mode',
            'must_have_avatar',
            'must_have_photo',
            'include_form',
            'select',
            'filter',
            'filters_row_a',
            'filters_row_b',
            'usergroup',
            'max',
            'start',
            'pagination',
            'sort',
            'parent_gallery',
            'per_row',
            'guid',
        );
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = '(strpos(serialize($_GET),\'filter_\')!==false)?null:array(
            array_key_exists(\'display_mode\',$map)?$map[\'display_mode\']:\'avatars\',
            array_key_exists(\'must_have_avatar\',$map)?($map[\'must_have_avatar\']==\'1\'):false,
            array_key_exists(\'must_have_photo\',$map)?($map[\'must_have_photo\']==\'1\'):false,
            array_key_exists(\'include_form\',$map)?($map[\'include_form\']==\'1\'):true,
            array_key_exists(\'filter\',$map)?$map[\'filter\']:\'*\',
            array_key_exists(\'filters_row_a\',$map)?$map[\'filters_row_a\']:\'\',
            array_key_exists(\'filters_row_b\',$map)?$map[\'filters_row_b\']:\'\',
            array_key_exists(\'select\',$map)?$map[\'select\']:\'\',
            array_key_exists(\'usergroup\',$map)?$map[\'usergroup\']:\'\',
            get_param_integer($block_id.\'_max\',array_key_exists(\'max\',$map)?intval($map[\'max\']):30),
            get_param_integer($block_id.\'_start\',array_key_exists(\'start\',$map)?intval($map[\'start\']):0),
            ((array_key_exists(\'pagination\',$map)?$map[\'pagination\']:\'0\')==\'1\'),
            get_param_string($block_id.\'_sort\',array_key_exists(\'sort\',$map)?$map[\'sort\']:\'m_join_time DESC\'),
            array_key_exists(\'parent_gallery\',$map)?$map[\'parent_gallery\']:\'\',
            array_key_exists(\'per_row\',$map)?intval($map[\'per_row\']):0,
            array_key_exists(\'guid\',$map)?$map[\'guid\']:\'\',
        )';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = 60;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return tempcode The result of execution.
     */
    public function run($map)
    {
        if (get_forum_type() != 'cns') {
            return paragraph(do_lang_tempcode('NO_CNS'), 'red_alert');
        }

        require_code('cns_members');
        require_code('cns_members2');
        require_code('selectcode');

        require_css('cns_member_directory');
        require_lang('cns_member_directory');

        $block_id = get_block_id($map);

        $guid = array_key_exists('guid', $map) ? $map['guid'] : '';

        $has_exists = db_has_subqueries($GLOBALS['SITE_DB']->connection_read);

        $where = 'id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());

        $usergroup = array_key_exists('usergroup', $map) ? $map['usergroup'] : '';

        $filter = array_key_exists('filter', $map) ? $map['filter'] : '';
        if ((!empty($map['filters_row_a'])) || (!empty($map['filters_row_b']))) {
            $filters_row_a = array_key_exists('filters_row_a', $map) ? $map['filters_row_a'] : '';
            $filters_row_b = array_key_exists('filters_row_b', $map) ? $map['filters_row_b'] : '';
        } else {
            $filters_row_a = 'm_username=' . php_addslashes(do_lang('USERNAME'));
            if ($usergroup == '') {
                $filters_row_a .= ',usergroup=' . php_addslashes(do_lang('GROUP'));
            }
            $filters_row_b = '';
            $cpfs = cns_get_all_custom_fields_match(cns_get_all_default_groups(), 1, 1, null, null, 1, null);
            $_filters_row_a = 2;
            $_filters_row_b = 0;
            foreach ($cpfs as $cpf) {
                $cf_name = get_translated_text($cpf['cf_name']);
                if (in_array($cpf['cf_type'], array('float', 'integer', 'list', 'long_text', 'long_trans', 'short_text', 'short_text_multi', 'short_trans', 'short_trans_multi'))) {
                    $filter_term = str_replace(',', '\,', $cf_name) . '=' . str_replace(',', '\,', $cf_name);
                    if ($_filters_row_a < 6) {
                        if ($filters_row_a != '') {
                            $filters_row_a .= ',';
                        }
                        $filters_row_a .= $filter_term;
                        $_filters_row_a++;
                    } else {
                        if ($filters_row_b != '') {
                            $filters_row_b .= ',';
                        }
                        $filters_row_b .= $filter_term;
                        $_filters_row_b++;
                    }
                }
            }
        }
        foreach (array($filters_row_a, $filters_row_b) as $filters_row) {
            foreach (array_keys(block_params_str_to_arr($filters_row)) as $filter_term) {
                if ($filter_term != '') {
                    if ($filter_term == 'usergroup') {
                        $usergroup = either_param_string('filter_' . $block_id . '_' . $filter_term, $usergroup);
                    } else {
                        if ($filter != '') {
                            $filter .= ',';
                        }
                        $filter .= $filter_term . '~=<' . fix_id($block_id . '_' . $filter_term) . '>';
                    }
                }
            }
        }
        if ($filter != '') {
            require_code('filtercode');
            $content_type = 'member';
            list($filter_extra_select, $filter_extra_join, $filter_extra_where) = filtercode_to_sql($GLOBALS['FORUM_DB'], parse_filtercode($filter), $content_type, '');
            $extra_select_sql = implode('', $filter_extra_select);
            $extra_join_sql = implode('', $filter_extra_join);
        } else {
            $extra_select_sql = '';
            $extra_join_sql = '';
            $filter_extra_where = '';
        }
        $where .= $filter_extra_where;

        $select = array_key_exists('select', $map) ? $map['select'] : '*';
        $where .= ' AND (' . selectcode_to_sqlfragment($select, 'id') . ')';

        if ($usergroup != '') {
            $where .= ' AND (1=0';
            foreach (explode(',', $usergroup) as $_usergroup) {
                if (is_numeric($_usergroup)) {
                    $group_id = intval($_usergroup);
                } else {
                    $group_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_groups', 'id', array($GLOBALS['FORUM_DB']->translate_field_ref('g_name') => $_usergroup));
                    if (is_null($group_id)) {
                        return paragraph(do_lang_tempcode('MISSING_RESOURCE'), 'red_alert');
                    }
                }
                if ($has_exists) {
                    $where .= ' OR (m_primary_group=' . strval($group_id) . ' OR EXISTS(SELECT gm_member_id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_group_members x WHERE x.gm_member_id=r.id AND gm_validated=1 AND gm_group_id=' . strval($group_id) . '))';
                } else {
                    $where .= ' OR (m_primary_group=' . strval($group_id) . ' OR gm_member_id=r.id AND gm_group_id=' . strval($group_id) . ')';
                }
            }
            $where .= ')';
        }

        if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
            $where .= ' AND m_validated=1';
        }

        $include_form = array_key_exists('include_form', $map) ? ($map['include_form'] == '1') : true;

        $must_have_avatar = array_key_exists('must_have_avatar', $map) ? ($map['must_have_avatar'] == '1') : false;
        if ($must_have_avatar) {
            $where .= ' AND ' . db_string_not_equal_to('m_avatar_url', '');
        }
        $must_have_photo = array_key_exists('must_have_photo', $map) ? ($map['must_have_photo'] == '1') : false;
        if ($must_have_photo) {
            $where .= ' AND ' . db_string_not_equal_to('m_photo_url', '');
        }

        $display_mode = array_key_exists('display_mode', $map) ? $map['display_mode'] : 'avatars';
        $show_avatar = true;
        switch ($display_mode) {
            case 'listing':
                $show_avatar = true;
                break;

            case 'boxes':
                $show_avatar = true;
                break;

            case 'photos':
                $show_avatar = false;
                break;

            case 'media':
                if (addon_installed('galleries')) {
                    require_css('galleries');
                    $show_avatar = true;
                    break;
                }
            // intentionally rolls on...

            case 'avatars':
            default:
                $show_avatar = false;
                $display_mode = 'avatars';
                break;
        }

        $parent_gallery = array_key_exists('parent_gallery', $map) ? $map['parent_gallery'] : '';
        if ($parent_gallery == '') {
            $parent_gallery = '%';
        }

        $per_row = array_key_exists('per_row', $map) ? intval($map['per_row']) : 0;
        if ($per_row == 0) {
            $per_row = null;
        }

        inform_non_canonical_parameter($block_id . '_sort');
        $sort = get_param_string($block_id . '_sort', array_key_exists('sort', $map) ? $map['sort'] : 'm_join_time DESC');
        $sortables = array(
            'm_username' => do_lang_tempcode('USERNAME'),
            'm_cache_num_posts' => do_lang_tempcode('COUNT_POSTS'),
            'm_join_time' => do_lang_tempcode('JOIN_DATE'),
            'm_last_visit_time' => do_lang_tempcode('LAST_VISIT_TIME'),
            'm_profile_views' => do_lang_tempcode('PROFILE_VIEWS'),
            'random' => do_lang_tempcode('RANDOM'),
        );
        if (strpos(get_db_type(), 'mysql') !== false) {
            $sortables['m_total_sessions'] = do_lang_tempcode('LOGIN_FREQUENCY');
        }
        if (strpos($sort, ' ') === false) {
            $sort .= ' ASC';
        }
        list($sortable, $sort_order) = explode(' ', $sort, 2);
        switch ($sort) {
            case 'random ASC':
            case 'random DESC':
                $sort = 'RAND() ASC';
                break;
            case 'm_total_sessions ASC':
                $sort = 'm_total_sessions/(UNIX_TIMESTAMP()-m_join_time) ASC';
                break;
            case 'm_total_sessions DESC':
                $sort = 'm_total_sessions/(UNIX_TIMESTAMP()-m_join_time) DESC';
                break;
            case 'm_join_time':
            case 'm_last_visit_time':
                $sort .= ',' . 'id ' . $sort_order; // Also order by ID, in case lots joined at the same time
                break;
            default:
                if (!isset($sortables[preg_replace('# (ASC|DESC)$#', '', $sort)])) {
                    $sort = 'm_join_time DESC';
                }
                break;
        }

        $sql = 'SELECT r.*' . $extra_select_sql . ' FROM ';
        $main_sql = $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members r';
        $main_sql .= $extra_join_sql;
        if ((!$has_exists) && ($usergroup != '')) {
            $main_sql .= ' LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_group_members g ON (r.id=g.gm_member_id AND gm_validated=1)';
        }
        $main_sql .= ' WHERE ' . $where;
        $sql .= $main_sql;
        $sql .= (can_arbitrary_groupby() ? ' GROUP BY r.id' : '');
        $sql .= ' ORDER BY ' . $sort;
        $count_sql = 'SELECT COUNT(DISTINCT r.id) FROM ' . $main_sql;

        inform_non_canonical_parameter($block_id . '_max');
        $max = get_param_integer($block_id . '_max', array_key_exists('max', $map) ? intval($map['max']) : 30);
        if ($max == 0) {
            $max = 30;
        }
        inform_non_canonical_parameter($block_id . '_start');
        $start = get_param_integer($block_id . '_start', array_key_exists('start', $map) ? intval($map['start']) : 0);

        $max_rows = $GLOBALS['FORUM_DB']->query_value_if_there($count_sql);

        $rows = $GLOBALS['FORUM_DB']->query($sql, ($display_mode == 'media') ? ($max + $start) : $max, ($display_mode == 'media') ? null : $start);
        $rows = remove_duplicate_rows($rows, 'id');

        /*if (count($rows)==0)   We let our template control no-result output
        {
            return do_template('BLOCK_NO_ENTRIES', array(
                'HIGH' => false,
                'TITLE' => do_lang_tempcode('RECENT', make_string_tempcode(integer_format($max)), do_lang_tempcode('MEMBERS')),
                'MESSAGE' => do_lang_tempcode('NO_ENTRIES'),
                'ADD_NAME' => '',
                'SUBMIT_URL' => '',
            ));
        }*/

        $hooks = null;
        if (is_null($hooks)) {
            $hooks = find_all_hooks('modules', 'topicview');
        }
        $hook_objects = null;
        if (is_null($hook_objects)) {
            $hook_objects = array();
            foreach (array_keys($hooks) as $hook) {
                require_code('hooks/modules/topicview/' . filter_naughty_harsh($hook));
                $object = object_factory('Hook_topicview_' . filter_naughty_harsh($hook), true);
                if (is_null($object)) {
                    continue;
                }
                $hook_objects[$hook] = $object;
            }
        }

        $cnt = 0;
        $member_boxes = array();
        foreach ($rows as $row) {
            $member_id = $row['id'];
            $box = render_member_box($member_id, true, $hooks, $hook_objects, $show_avatar, null, false);

            if ($display_mode == 'media') {
                $gallery_sql = 'SELECT name,fullname FROM ' . get_table_prefix() . 'galleries WHERE';
                $gallery_sql .= ' name LIKE \'' . db_encode_like('member\_' . strval($member_id) . '\_' . $parent_gallery) . '\'';
                $galleries = $GLOBALS['SITE_DB']->query($gallery_sql);
                foreach ($galleries as $gallery) {
                    $num_images = $GLOBALS['SITE_DB']->query_select_value('images', 'COUNT(*)', array('cat' => $gallery['name'], 'validated' => 1));
                    $num_videos = $GLOBALS['SITE_DB']->query_select_value('videos', 'COUNT(*)', array('cat' => $gallery['name'], 'validated' => 1));
                    if (($num_images > 0) || ($num_videos > 0)) {
                        if ($cnt >= $start) {
                            $member_boxes[] = array(
                                'I' => strval($cnt - $start + 1),
                                'BREAK' => (!is_null($per_row)) && (($cnt - $start + 1) % $per_row == 0),
                                'BOX' => $box,
                                'MEMBER_ID' => strval($member_id),
                                'GALLERY_NAME' => $gallery['name'],
                                'GALLERY_TITLE' => get_translated_text($gallery['fullname']),
                            );
                        }

                        $cnt++;
                        if ($cnt + $start == $max) {
                            break; // We have to read deep with media mode, as the number to display is not determinable within an SQL limit range
                        }
                    }
                }
            } else {
                $member_boxes[$member_id] = array(
                    'I' => strval($cnt + 1),
                    'BREAK' => (!is_null($per_row)) && (($cnt + 1) % $per_row == 0),
                    'BOX' => $box,
                    'MEMBER_ID' => strval($member_id),
                    'GALLERY_NAME' => '',
                    'GALLERY_TITLE' => '',
                );

                $cnt++;
                if ($cnt == $max) {
                    break;
                }
            }
        }

        require_code('templates_results_table');

        if (($display_mode == 'listing') && (count($rows) > 0)) {
            $results_entries = new Tempcode();

            $_fields_title = array();
            $_fields_title[] = (get_option('display_name_generator') == '') ? do_lang_tempcode('USERNAME') : do_lang_tempcode('NAME');
            $_fields_title[] = do_lang_tempcode('PRIMARY_GROUP');
            if (addon_installed('points')) {
                $_fields_title[] = do_lang_tempcode('POINTS');
            }
            if (addon_installed('cns_forum')) {
                $_fields_title[] = do_lang_tempcode('COUNT_POSTS');
            }
            if (get_option('use_lastondate') == '1') {
                $_fields_title[] = do_lang_tempcode('LAST_VISIT_TIME');
            }
            if (get_option('use_joindate') == '1') {
                $_fields_title[] = do_lang_tempcode('JOIN_DATE');
            }
            $fields_title = results_field_title($_fields_title, $sortables, 'md_sort', $sortable . ' ' . $sort_order);
            require_code('cns_members2');
            foreach ($rows as $row) {
                $_entry = array();

                $_entry[] = do_template('CNS_MEMBER_DIRECTORY_USERNAME', array(
                    'ID' => strval($row['id']),
                    'USERNAME' => $row['m_username'],
                    'URL' => $GLOBALS['FORUM_DRIVER']->member_profile_url($row['id'], true, true),
                    'AVATAR_URL' => addon_installed('cns_member_avatars') ? $row['m_avatar_url'] : $row['m_photo_thumb_url'],
                    'PHOTO_THUMB_URL' => $row['m_photo_thumb_url'],
                    'VALIDATED' => ($row['m_validated'] == 1),
                    'CONFIRMED' => ($row['m_validated_email_confirm_code'] == ''),
                    'BOX' => $member_boxes[$row['id']]['BOX'],
                ));

                $member_primary_group = cns_get_member_primary_group($row['id']);
                $primary_group = cns_get_group_link($member_primary_group);
                $_entry[] = $primary_group;

                if (addon_installed('points')) {
                    require_code('points');
                    $_entry[] = escape_html(integer_format(total_points($row['id'])));
                }

                if (addon_installed('cns_forum')) {
                    $_entry[] = escape_html(integer_format($row['m_cache_num_posts']));
                }

                if (get_option('use_lastondate') == '1') {
                    $_entry[] = escape_html(get_timezoned_date($row['m_last_visit_time'], false));
                }

                if (get_option('use_joindate') == '1') {
                    $_entry[] = escape_html(get_timezoned_date($row['m_join_time'], false));
                }

                $results_entries->attach(results_entry($_entry, false));
            }
            $results_table = results_table(do_lang_tempcode('MEMBERS'), $start, $block_id . '_start', $max, $block_id . '_max', $max_rows, $fields_title, $results_entries, $sortables, $sortable, $sort_order, $block_id . '_sort');

            $sorting = new Tempcode();
        } else {
            $results_table = new Tempcode();

            $do_pagination = ((array_key_exists('pagination', $map) ? $map['pagination'] : '0') == '1');
            if ($do_pagination) {
                require_code('templates_pagination');
                $pagination = pagination(do_lang_tempcode('MEMBERS'), $start, $block_id . '_start', $max, $block_id . '_max', $max_rows, true);
            } else {
                $pagination = new Tempcode();
            }

            $sorting = results_sorter($sortables, $sortable, $sort_order, $block_id . '_sort');
        }

        $_usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(true, false, false);
        $usergroups = array();
        require_code('cns_groups2');
        foreach ($_usergroups as $group_id => $group) {
            $num = cns_get_group_members_raw_count($group_id, true);
            $usergroups[$group_id] = array('USERGROUP' => $group, 'NUM' => strval($num));
        }

        $symbols = null;
        if (get_option('allow_alpha_search') == '1') {
            $alpha_query = $GLOBALS['FORUM_DB']->query('SELECT m_username FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval(db_get_first_id()) . ' ORDER BY m_username ASC');
            $symbols = array(array('START' => '0', 'SYMBOL' => do_lang('ALL')), array('START' => '0', 'SYMBOL' => '#'));
            foreach (array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z') as $s) {
                foreach ($alpha_query as $i => $q) {
                    if (strtolower(substr($q['m_username'], 0, 1)) == $s) {
                        break;
                    }
                }
                if (substr(strtolower($q['m_username']), 0, 1) != $s) {
                    $i = intval($symbols[count($symbols) - 1]['START']);
                }
                $symbols[] = array('START' => strval(intval($max * floor(floatval($i) / floatval($max)))), 'SYMBOL' => $s);
            }
        }

        $has_active_filter = false;
        foreach (array_keys($_GET) as $key) {
            if (substr($key, 0, strlen($block_id . '_filter_')) == $block_id . '_filter_') {
                $has_active_filter = true;
                break;
            }
        }

        return do_template('BLOCK_MAIN_MEMBERS', array(
            '_GUID' => $guid,
            'BLOCK_ID' => $block_id,
            'START' => strval($start),
            'MAX' => strval($max),
            'SORTABLE' => $sortable,
            'SORT_ORDER' => $sort_order,
            'FILTERS_ROW_A' => $filters_row_a,
            'FILTERS_ROW_B' => $filters_row_b,
            'ITEM_WIDTH' => is_null($per_row) ? '' : float_to_raw_string(99.0/*avoid possibility of rounding issues as pixels won't divide perfectly*/ / floatval($per_row)) . '%',
            'PER_ROW' => is_null($per_row) ? '' : strval($per_row),
            'DISPLAY_MODE' => $display_mode,
            'MEMBER_BOXES' => $member_boxes,
            'PAGINATION' => new Tempcode(),
            'RESULTS_TABLE' => $results_table,
            'USERGROUPS' => $usergroups,
            'SYMBOLS' => $symbols,
            'HAS_ACTIVE_FILTER' => $has_active_filter,
            'INCLUDE_FORM' => $include_form,
            'SORT' => $sorting,
        ));
    }
}
