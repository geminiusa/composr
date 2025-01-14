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
 * @package    stats_block
 */

/**
 * Block class.
 */
class Block_side_stats
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
        $info['version'] = 3;
        $info['update_require_upgrade'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array();
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
        $info['cache_on'] = '';
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 15;
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
        $full_tpl = new Tempcode();

        // Inbuilt
        $bits = new Tempcode();
        $on_forum = $GLOBALS['FORUM_DRIVER']->get_num_users_forums();
        if (!is_null($on_forum)) {
            if (get_option('activity_show_stats_count_users_online') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => '5ac97313d4c83e8afdeec09a48cea030',
                    'KEY' => do_lang_tempcode('COUNT_ONSITE'),
                    'VALUE' => integer_format(get_num_users_site()),
                )));
            }
            if (get_option('activity_show_stats_count_users_online_record') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => 'dc6d0893cf98703a951da168c6a9d0ac',
                    'KEY' => do_lang_tempcode('COUNT_ONSITE_RECORD'),
                    'VALUE' => integer_format(get_num_users_peak()),
                )));
            }
            if (get_option('activity_show_stats_count_users_online_forum') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => '14f2fdbf59e86c34d93cbf16bed3f0eb',
                    'KEY' => do_lang_tempcode('COUNT_ONFORUMS'),
                    'VALUE' => integer_format($on_forum),
                )));
            }
            $title = do_lang_tempcode('SECTION_USERS');
        } else {
            if (get_option('activity_show_stats_count_users_online') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => '9c9760b2ed9e985e96b53c91c511e84e',
                    'KEY' => do_lang_tempcode('USERS_ONLINE'),
                    'VALUE' => integer_format(get_num_users_site()),
                )));
            }
            if (get_option('activity_show_stats_count_users_online_record') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => 'd18068d747fe1fe364042133e4b3ba84',
                    'KEY' => do_lang_tempcode('USERS_ONLINE_RECORD'),
                    'VALUE' => integer_format(get_num_users_peak()),
                )));
            }
            $title = do_lang_tempcode('ACTIVITY');
        }
        if (addon_installed('stats')) {
            if (get_option('activity_show_stats_count_page_views_today') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => 'fc9760b2ed9e985e96b53c91c511e84e',
                    'KEY' => do_lang_tempcode('PAGE_VIEWS_TODAY'),
                    'VALUE' => integer_format($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24))),
                )));
            }
            if (get_option('activity_show_stats_count_page_views_this_week') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => 'gc9760b2ed9e985e96b53c91c511e84e',
                    'KEY' => do_lang_tempcode('PAGE_VIEWS_THIS_WEEK'),
                    'VALUE' => integer_format($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24 * 7))),
                )));
            }
            if (get_option('activity_show_stats_count_page_views_this_month') == '1') {
                $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array(
                    '_GUID' => 'hc9760b2ed9e985e96b53c91c511e84e',
                    'KEY' => do_lang_tempcode('PAGE_VIEWS_THIS_MONTH'),
                    'VALUE' => integer_format($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24 * 31))),
                )));
            }
        }
        if (!$bits->is_empty_shell()) {
            $full_tpl->attach(do_template('BLOCK_SIDE_STATS_SECTION', array('_GUID' => 'e2408c71a7c74f1d14089412d4538b6d', 'SECTION' => $title, 'CONTENT' => $bits)));
        }

        $_hooks = find_all_hooks('blocks', 'side_stats');
        if (array_key_exists('stats_forum', $_hooks)) { // Fudge the order
            $forum_hook = $_hooks['stats_forum'];
            unset($_hooks['stats_forum']);
            $_hooks = array_merge(array('stats_forum' => $forum_hook), $_hooks);
        }
        foreach (array_keys($_hooks) as $hook) {
            require_code('hooks/blocks/side_stats/' . filter_naughty_harsh($hook));
            $object = object_factory('Hook_stats_' . filter_naughty_harsh($hook), true);
            if (is_null($object)) {
                continue;
            }
            $bits = $object->run();
            if (!$bits->is_empty_shell()) {
                $full_tpl->attach($bits);
            }
        }

        return do_template('BLOCK_SIDE_STATS', array('_GUID' => '0e9986c117c2a3c04690840fedcbddcd', 'CONTENT' => $full_tpl));
    }
}
