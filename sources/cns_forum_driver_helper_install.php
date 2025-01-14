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

/* This file exists to alleviate PHP memory usage. It shaves over 100KB of memory need for any Conversr request. */

/**
 * Add the specified custom field to the forum (some forums implemented this using proper custom profile fields, others through adding a new field).
 *
 * @param  object $this_ref Link to the real forum driver
 * @param  string $name The name of the new custom field
 * @param  integer $length The length of the new custom field
 * @param  BINARY $locked Whether the field is locked
 * @param  BINARY $viewable Whether the field is for viewing
 * @param  BINARY $settable Whether the field is for setting
 * @param  BINARY $required Whether the field is required
 * @param  string $description Description
 * @param  string $type The field type
 * @param  BINARY $encrypted Whether the field is encrypted
 * @param  ?string $default Default field value (null: standard for field type)
 * @return boolean Whether the custom field was created successfully
 */
function _helper_install_create_custom_field($this_ref, $name, $length, $locked = 1, $viewable = 0, $settable = 0, $required = 0, $description = '', $type = 'long_text', $encrypted = 0, $default = null)
{
    cns_require_all_forum_stuff();
    require_code('cns_members_action');

    $name = 'cms_' . $name;
    $id = $this_ref->connection->query_select_value_if_there('f_custom_fields', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('cf_name') => $name));
    if (is_null($id)) {
        if (is_null($default)) {
            $default = (strpos($name, 'points') !== false) ? '0' : '';
        }
        $id = cns_make_custom_field($name, $locked, $description, $default, $viewable, $viewable, $settable, $encrypted, $type, $required);
    }
    return !is_null($id);
}

/**
 * Get an array of attributes to take in from the installer. Almost all forums require a table prefix, which the requirement there-of is defined through this function.
 * The attributes have 4 values in an array
 * - name, the name of the attribute for _config.php
 * - default, the default value (perhaps obtained through autodetection from forum config)
 * - description, a textual description of the attributes
 * - title, a textual title of the attribute
 *
 * @return array The attributes for the forum
 */
function _helper_install_specifics()
{
    $a = array();
    $a['name'] = 'cns_table_prefix';
    $a['default'] = 'cms_';
    $a['description'] = do_lang('MOST_DEFAULT');
    $a['title'] = do_lang('TABLE_PREFIX');
    $b = array();
    $b['name'] = 'clear_existing_forums_on_install';
    $b['default'] = 'no';
    $b['description'] = do_lang_tempcode('DESCRIPTION_CLEAR_EXISTING_FORUMS_ON_INSTALL');
    $b['title'] = do_lang_tempcode('CLEAR_EXISTING_FORUMS_ON_INSTALL');
    $c = array();
    $c['name'] = 'admin_username';
    $c['default'] = 'admin';
    $c['description'] = do_lang_tempcode('DESCRIPTION_ADMIN_USERNAME');
    $c['title'] = do_lang_tempcode('ADMIN_USERNAME');
    $d = array();
    $d['name'] = 'cns_admin_password';
    $d['default'] = '';
    $d['description'] = do_lang_tempcode('DESCRIPTION_ADMIN_USERS_PASSWORD');
    $d['title'] = do_lang_tempcode('ADMIN_USERS_PASSWORD');
    return array($a, $b, $c, $d);
}

/**
 * Searches for forum auto-config at this path.
 *
 * @param  PATH $path The path in which to search
 * @return boolean Whether the forum auto-config could be found
 */
function _helper_install_test_load_from($path)
{
    global $PROBED_FORUM_CONFIG;
    $PROBED_FORUM_CONFIG['sql_database'] = 'cms';
    $PROBED_FORUM_CONFIG['sql_user'] = $GLOBALS['DB_STATIC_OBJECT']->db_default_user();
    $PROBED_FORUM_CONFIG['sql_pass'] = $GLOBALS['DB_STATIC_OBJECT']->db_default_password();

    $base_url = post_param_string('base_url', 'http://' . cms_srv('HTTP_HOST') . dirname(cms_srv('SCRIPT_NAME')));

    $PROBED_FORUM_CONFIG['board_url'] = $base_url . '/forum';
    return true;
}
