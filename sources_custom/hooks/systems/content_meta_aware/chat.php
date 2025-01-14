<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 */
class Hook_content_meta_aware_chat
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

            'content_type_label' => 'chat:CHATROOM',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'chat_rooms',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => null,
            'parent_category_meta_aware_type' => null,
            'is_category' => true,
            'is_entry' => false,
            'category_field' => null, // For category permissions
            'category_type' => null, // For category permissions
            'category_is_string' => false,

            'title_field' => 'room_name',
            'title_field_dereference' => false,
            'description_field' => null,
            'thumb_field' => null,

            'view_page_link_pattern' => '_SEARCH:chat:room:_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_chat:room:_WILD',
            'view_category_page_link_pattern' => '_SEARCH:chat:room:_WILD',
            'add_url' => (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_chat')) ? (get_module_zone('cms_chat') . ':cms_chat:add_category') : null,
            'archive_url' => ((!is_null($zone)) ? $zone : get_module_zone('chat')) . ':chat',

            'support_url_monikers' => true,

            'views_field' => null,
            'submitter_field' => null,
            'add_time_field' => null,
            'edit_time_field' => null,
            'date_field' => null,
            'validated_field' => null,

            'seo_type_code' => null,

            'feedback_type_code' => null,

            'permissions_type_code' => null, // NULL if has no permissions

            'search_hook' => null,

            'addon_name' => 'chat',

            'cms_page' => 'cms_chat',
            'module' => 'chat',

            'commandr_filesystem_hook' => 'chat',
            'commandr_filesystem__is_folder' => false,

            'rss_hook' => null,

            'actionlog_regexp' => '\w+_CHAT',
        );
    }
}
