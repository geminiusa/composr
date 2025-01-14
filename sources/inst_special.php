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

/*
These are special functions used by the installer and upgrader.
*/

/**
 * Get the list of files that need CHmodding for write access.
 *
 * @param  ID_TEXT $lang Language to use
 * @return array The list of files
 */
function get_chmod_array($lang)
{
    $extra_files = array('collaboration/pages/html_custom', 'collaboration/pages/html_custom/' . $lang, 'collaboration/pages/comcode_custom', 'collaboration/pages/comcode_custom/' . $lang,);

    if (function_exists('find_all_hooks')) {
        $hooks = find_all_hooks('systems', 'addon_registry');
        $hook_keys = array_keys($hooks);
        foreach ($hook_keys as $hook) {
            //require_code('hooks/systems/addon_registry/'.filter_naughty_harsh($hook));
            //$object=object_factory('Hook_addon_registry_'.filter_naughty_harsh($hook));
            //$extra_files=array_merge($extra_files,$object->get_chmod_array());

            // Save memory compared to above commented code...

            $path = get_custom_file_base() . '/sources_custom/hooks/systems/addon_registry/' . filter_naughty_harsh($hook) . '.php';
            if (!file_exists($path)) {
                $path = get_file_base() . '/sources/hooks/systems/addon_registry/' . filter_naughty_harsh($hook) . '.php';
            }
            $matches = array();
            if (preg_match('#function get_chmod_array\(\)\s*\{([^\}]*)\}#', file_get_contents($path), $matches) != 0) {
                if (!HHVM) {
                    $extra_files = array_merge($extra_files, eval($matches[1]));
                } else {
                    require_code('hooks/systems/addon_registry/' . $hook);
                    $hook = object_factory('Hook_addon_registry_' . $hook);
                    $extra_files = array_merge($extra_files, $hook->get_chmod_array());
                }
            }
        }
    }

    return array_merge(
        $extra_files,
        array(
            'safe_mode_temp', 'data_custom/modules/admin_backup', 'data_custom/modules/chat', 'data_custom/modules/web_notifications', 'data_custom/xml_config', 'data_custom/modules/admin_stats', 'data_custom/spelling/personal_dicts',
            'themes/map.ini', 'text_custom', 'text_custom/' . $lang,
            'data_custom/modules/chat/chat_last_msg.dat', 'data_custom/modules/chat/chat_last_event.dat', 'data_custom/modules/web_notifications/latest.dat',
            'caches/persistent', 'caches/lang', 'caches/lang/' . $lang, 'caches/self_learning', 'caches/self_learning/' . $lang, 'caches/guest_pages', 'caches/guest_pages/' . $lang,
            'lang_custom', 'lang_custom/' . $lang,
            'data_custom/errorlog.php', 'cms_sitemap.xml', 'cms_news_sitemap.xml', 'data_custom/permissioncheckslog.php',
            'pages/html_custom', 'site/pages/html_custom', 'docs/pages/html_custom', 'adminzone/pages/html_custom', 'forum/pages/html_custom', 'cms/pages/html_custom',
            'pages/html_custom/' . $lang, 'site/pages/html_custom/' . $lang, 'docs/pages/html_custom/' . $lang, 'adminzone/pages/html_custom/' . $lang, 'forum/pages/html_custom/' . $lang, 'cms/pages/html_custom/' . $lang,
            'pages/comcode_custom', 'site/pages/comcode_custom', 'docs/pages/comcode_custom', 'adminzone/pages/comcode_custom', 'forum/pages/comcode_custom', 'cms/pages/comcode_custom',
            'pages/comcode_custom/' . $lang, 'site/pages/comcode_custom/' . $lang, 'docs/pages/comcode_custom/' . $lang, 'adminzone/pages/comcode_custom/' . $lang, 'forum/pages/comcode_custom/' . $lang, 'cms/pages/comcode_custom/' . $lang,
            'themes/default/css_custom', 'themes/default/images_custom', 'themes/default/templates_custom', 'themes/default/javascript_custom', 'themes/default/xml_custom', 'themes/default/text_custom', 'themes/default/templates_cached', 'themes/default/templates_cached/' . $lang,
            'themes/admin/css_custom', 'themes/admin/images_custom', 'themes/admin/templates_custom', 'themes/admin/javascript_custom', 'themes/admin/xml_custom', 'themes/admin/text_custom', 'themes/admin/templates_cached', 'themes/admin/templates_cached/' . $lang,
            'themes/default/theme.ini',
            'uploads/incoming', 'uploads/website_specific', 'uploads/personal_sound_effects', 'uploads/banners', 'uploads/downloads', 'uploads/galleries', 'uploads/watermarks', 'uploads/repimages', 'uploads/galleries_thumbs', 'uploads/catalogues', 'uploads/attachments', 'uploads/attachments_thumbs', 'uploads/auto_thumbs', 'uploads/cns_avatars', 'uploads/cns_cpf_upload', 'uploads/cns_photos', 'uploads/cns_photos_thumbs', 'uploads/filedump',
            '_config.php', 'exports/backups', 'exports/file_backups', 'exports/addons', 'imports/addons'
        )
    );
}
