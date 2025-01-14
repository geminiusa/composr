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
 * @package    core_menus
 */

/**
 * Block class.
 */
class Block_menu
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
        $info['parameters'] = array('title', 'type', 'param', 'tray_status', 'silent_failure');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        /* Ideally we would not cache as we would need to cache for all screens due to context sensitive link display (either you're here or match key filtering). However in most cases that only happens per page, so we will cache per page -- and people can turn off caching via the standard block parameter for that if needed.*/
        $info = array();
        $info['cache_on'] = array('block_menu__cache_on');
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 24 * 140;
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
        if (!array_key_exists('param', $map)) {
            $map['param'] = '';
        }

        $type = array_key_exists('type', $map) ? $map['type'] : 'embossed';
        $silent_failure = array_key_exists('silent_failure', $map) ? $map['silent_failure'] : '0';
        $tray_status = array_key_exists('tray_status', $map) ? $map['tray_status'] : '';

        if ($type != 'tree') {
            $exists = file_exists(get_file_base() . '/themes/default/templates/MENU_BRANCH_' . $type . '.tpl');
            if (!$exists) {
                $exists = file_exists(get_custom_file_base() . '/themes/default/templates_custom/MENU_BRANCH_' . $type . '.tpl');
            }
            $theme = $GLOBALS['FORUM_DRIVER']->get_theme();
            if ((!$exists) && ($theme != 'default')) {
                $exists = file_exists(get_custom_file_base() . '/themes/' . $theme . '/templates/MENU_BRANCH_' . $type . '.tpl');
                if (!$exists) {
                    $exists = file_exists(get_custom_file_base() . '/themes/' . $theme . '/templates_custom/MENU_BRANCH_' . $type . '.tpl');
                }
            }
            if (!$exists) {
                $type = 'tree';
            }
        }

        if ($map['param'] == '') {
            disable_php_memory_limit();
        }

        require_code('menus');
        $menu = build_menu($type, $map['param'], $silent_failure == '1');
        $menu->handle_symbol_preprocessing(); // Optimisation: we are likely to have lots of page-links in here, so we want to spawn them to be detected for mass moniker loading

        if ((array_key_exists('title', $map)) && ($map['title'] != '')) {
            $menu = do_template('BLOCK_MENU', array('_GUID' => 'ae46aa37a9c5a526f43b26a391164436', 'CONTENT' => $menu, 'TYPE' => $type, 'PARAM' => $map['param'], 'TRAY_STATUS' => $tray_status, 'TITLE' => comcode_to_tempcode($map['title'], null, true)));
        }

        return $menu;
    }
}

/**
 * Find the cache signature for the block.
 *
 * @param  array $map The block parameters.
 * @return array The cache signature.
 */
function block_menu__cache_on($map)
{
    /*
    Menu caching is problematic. "Is active" caching theoretically would need doing against each URL.
     (or to use JavaScript, or Tempcode pre-processing, to implement that -- but that would be messy)
    We therefore assume that menu links are maximally distinguished by zone&page&type parameters.
     (special case -- catalogue index screens are also distinguished by ID, as catalogues vary a lot)

    There is a simple workaround if our assumptions don't hold up. Just turn off caching for the
    particular menu block instance. cache="0". It won't hurt very much, menus are relatively fast.
    */

    $menu = array_key_exists('param', $map) ? $map['param'] : '';
    $page = get_page_name();
    $url_type = get_param_string('type', 'browse');
    return array(
        ((substr($menu, 0, 1) != '_') && (substr($menu, 0, 3) != '!!!') && (has_actual_page_access(get_member(), 'admin_menus'))),
        get_zone_name(),
        $page,
        $url_type,
        ($page == 'catalogues' && $url_type == 'index') ? get_param_string('id', '') : '', // Catalogues need a little extra work to distinguish them
        array_key_exists('type', $map) ? $map['type'] : 'embossed',
        $menu,
        array_key_exists('title', $map) ? $map['title'] : '',
        array_key_exists('silent_failure', $map) ? $map['silent_failure'] : '0',
        array_key_exists('tray_status', $map) ? $map['tray_status'] : '',
    );
}
