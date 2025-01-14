<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    tester
 */

/**
 * Hook class.
 */
class Hook_content_meta_aware_tester
{
    /**
     * Get content type details. Provides information to allow task reporting, randomisation, and add-screen linking, to function.
     *
     * @param  ?ID_TEXT $zone The zone to link through to (null: autodetect).
     * @return ?array Map of award content-type info (null: disabled).
     */
    public function info($zone = null)
    {
        return array(
            'supports_custom_fields' => false,

            'content_type_label' => 'tester:TEST_SECTION',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'tests',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => 't_section',
            'parent_category_meta_aware_type' => null,
            'is_category' => false,
            'is_entry' => true,
            'category_field' => null, // For category permissions
            'category_type' => null, // For category permissions
            'category_is_string' => false,

            'title_field' => null,
            'title_field_dereference' => false,
            'description_field' => null,
            'thumb_field' => null,

            'view_page_link_pattern' => '_SEARCH:tester:report:_WILD',
            'edit_page_link_pattern' => '_SEARCH:tester:_edit:_WILD',
            'view_category_page_link_pattern' => null,
            'add_url' => (has_submit_permission('mid', get_member(), get_ip_address(), 'tester')) ? (get_module_zone('tester') . ':tester:add') : null,
            'archive_url' => ((!is_null($zone)) ? $zone : get_module_zone('tester')) . ':tester',

            'support_url_monikers' => true,

            'views_field' => null,
            'submitter_field' => 't_assigned_to',
            'add_time_field' => null,
            'edit_time_field' => null,
            'date_field' => null,
            'validated_field' => null,

            'seo_type_code' => null,

            'feedback_type_code' => 'bug_report',

            'permissions_type_code' => null, // NULL if has no permissions

            'search_hook' => null,

            'addon_name' => 'tester',

            'cms_page' => 'cms_chat',
            'module' => 'tester',

            'commandr_filesystem_hook' => null,
            'commandr_filesystem__is_folder' => false,

            'rss_hook' => null,

            'actionlog_regexp' => '\w+_TEST',
        );
    }
}
