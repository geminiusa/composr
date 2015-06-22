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
 * Remove an item from the general cache (most commonly used for blocks).
 *
 * @param  mixed $cached_for The type of what we are caching (e.g. block name) (ID_TEXT or an array of ID_TEXT, the array may be pairs re-specifying $identifier)
 * @param  ?array $identifier A map of identifiying characteristics (null: no identifying characteristics, decache all)
 * @param  ?MEMBER $member Member to only decache for (null: no limit)
 */
function _decache($cached_for, $identifier = null, $member = null)
{
    if (!is_array($cached_for)) {
        $cached_for = array($cached_for);
    }

    $cached_for_sz = serialize($cached_for);

    static $done_already = array();
    if ($identifier === null) {
        if (array_key_exists($cached_for_sz, $done_already)) {
            return;
        }
    }

    $where = '';

    $bot_statuses = array(true, false);
    $timezones = array_keys(get_timezone_list());

    foreach ($cached_for as $_cached_for) {
        if (is_array($_cached_for)) {
            $_identifier = $_cached_for[1];
            $_cached_for = $_cached_for[0];
        } else {
            $_identifier = $identifier;
        }

        // NB: If we use persistent cache we still need to decache from DB, in case we're switching between for whatever reason. Or maybe some users use persistent cache and others don't. Or maybe some nodes do and others don't.

        if ($GLOBALS['PERSISTENT_CACHE'] !== null) {
            persistent_cache_delete(array('CACHE', $_cached_for));
        }

        if ($where != '') {
            $where .= ' OR ';
        }

        $where .= db_string_equal_to('cached_for', $_cached_for);
        if ($_identifier !== null) {
            $where .= ' AND ' . db_string_equal_to('identifier', md5(serialize($_identifier)));
        }
        if ($member !== null) {
            $where .= ' AND the_member=' . strval($member);
        }
    }

    $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'cache WHERE ' . $where, null, null, false, true);

    if ($identifier === null) {
        $done_already[$cached_for_sz] = true;
    }
}

/**
 * Request that CRON loads up a block's caching in the background.
 *
 * @param  ID_TEXT $codename The codename of the block
 * @param  ?array $map Parameters to call up block with if we have to defer caching (null: none)
 * @param  integer $special_cache_flags Flags representing how we should cache
 * @param  boolean $tempcode Whether we are caching Tempcode (needs special care)
 */
function request_via_cron($codename, $map, $special_cache_flags, $tempcode)
{
    require_code('temporal');

    global $TEMPCODE_SETGET;
    $map = array(
        'c_theme' => $GLOBALS['FORUM_DRIVER']->get_theme(),
        'c_lang' => user_lang(),
        'c_codename' => $codename,
        'c_map' => serialize($map),
        'c_staff_status' => (($special_cache_flags & CACHE_AGAINST_STAFF_STATUS) != 0) ? $GLOBALS['FORUM_DRIVER']->is_staff(get_member()) : null,
        'c_member' => (($special_cache_flags & CACHE_AGAINST_BOT_STATUS) != 0) ? get_member() : null,
        'c_groups' => (($special_cache_flags & CACHE_AGAINST_PERMISSIVE_GROUPS) != 0) ? implode(',', array_map('strval', filter_group_permissivity($GLOBALS['FORUM_DRIVER']->get_members_groups(get_member())))) : '',
        'c_is_bot' => (($special_cache_flags & CACHE_AGAINST_BOT_STATUS) != 0) ? (is_null(get_bot_type()) ? 0 : 1) : null,
        'c_timezone' => (($special_cache_flags & CACHE_AGAINST_TIMEZONE) != 0) ? get_users_timezone(get_member()) : '',
        'c_store_as_tempcode' => $tempcode ? 1 : 0,
    );
    if (is_null($GLOBALS['SITE_DB']->query_select_value_if_there('cron_caching_requests', 'id', $map))) {
        $GLOBALS['SITE_DB']->query_insert('cron_caching_requests', $map);
    }
}

/**
 * Put a result into the cache.
 *
 * @param  MINIID_TEXT $codename The codename to check for caching
 * @param  integer $ttl The TTL of what is being cached in minutes
 * @param  LONG_TEXT $cache_identifier The requisite situational information (a serialized map) [-> further restraints when reading]
 * @param  ?BINARY $staff_status Staff status to limit to (null: Not limiting by this)
 * @param  ?MEMBER $member Member to limit to (null: Not limiting by this)
 * @param  SHORT_TEXT $groups Sorted permissive usergroup list to limit to (blank: Not limiting by this)
 * @param  ?BINARY $is_bot Bot status to limit to (null: Not limiting by this)
 * @param  MINIID_TEXT $timezone Timezone to limit to (blank: Not limiting by this)
 * @param  mixed $cache The result we are caching
 * @param  ?array $_langs_required A list of the language files that need loading to use tempcode embedded in the cache (null: none required)
 * @param  ?array $_javascripts_required A list of the javascript files that need loading to use tempcode embedded in the cache (null: none required)
 * @param  ?array $_csss_required A list of the css files that need loading to use tempcode embedded in the cache (null: none required)
 * @param  boolean $tempcode Whether we are caching Tempcode (needs special care)
 * @param  ?ID_TEXT $theme The theme this is being cached for (null: current theme)
 * @param  ?LANGUAGE_NAME $lang The language this is being cached for (null: current language)
 */
function put_into_cache($codename, $ttl, $cache_identifier, $staff_status, $member, $groups, $is_bot, $timezone, $cache, $_langs_required = null, $_javascripts_required = null, $_csss_required = null, $tempcode = false, $theme = null, $lang = null)
{
    if ($theme === null) {
        $theme = $GLOBALS['FORUM_DRIVER']->get_theme();
    }
    if ($lang === null) {
        $lang = user_lang();
    }

    global $KEEP_MARKERS, $SHOW_EDIT_LINKS;
    if ($KEEP_MARKERS || $SHOW_EDIT_LINKS) {
        return;
    }

    $dependencies = (is_null($_langs_required)) ? '' : implode(':', $_langs_required);
    $dependencies .= '!';
    $dependencies .= (is_null($_javascripts_required)) ? '' : implode(':', $_javascripts_required);
    $dependencies .= '!';
    $dependencies .= (is_null($_csss_required)) ? '' : implode(':', $_csss_required);

    $big_mainstream_cache = false;//($codename != 'menu') && ($ttl > 60 * 5) && (get_users_timezone(get_member()) == get_site_timezone());
    if ($big_mainstream_cache) {
        cms_profile_start_for('put_into_cache');
    }

    if (!is_null($GLOBALS['PERSISTENT_CACHE'])) {
        $pcache = array('dependencies' => $dependencies, 'date_and_time' => time(), 'the_value' => $cache);
        persistent_cache_set(array('CACHE', $codename, md5($cache_identifier), $lang, $theme), $pcache, false, $ttl * 60);
    } else {
        $GLOBALS['SITE_DB']->query_delete('cache', array(
            'lang' => $lang,
            'the_theme' => $theme,
            'cached_for' => $codename,
            'identifier' => md5($cache_identifier)
        ), '', 1);
        $GLOBALS['SITE_DB']->query_insert('cache', array(
            'dependencies' => $dependencies,
            'lang' => $lang,
            'cached_for' => $codename,
            'identifier' => md5($cache_identifier),
            'the_theme' => $theme,
            'staff_status' => $staff_status,
            'the_member' => $member,
            'groups' => $groups,
            'is_bot' => $is_bot,
            'timezone' => $timezone,
            'the_value' => $tempcode ? $cache->to_assembly($lang) : serialize($cache),
            'date_and_time' => time(),
        ), false, true);
    }

    if ($big_mainstream_cache) {
        cms_profile_end_for('put_into_cache', $codename . ' - ' . $cache_identifier);
    }
}
