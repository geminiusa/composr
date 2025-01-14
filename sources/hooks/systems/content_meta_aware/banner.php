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
 * Hook class.
 */
class Hook_content_meta_aware_banner
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

            'content_type_label' => 'BANNER',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'banners',
            'id_field' => 'name',
            'id_field_numeric' => false,
            'parent_category_field' => 'b_type',
            'parent_category_meta_aware_type' => 'banner_type',
            'is_category' => false,
            'is_entry' => true,
            'category_field' => 'b_type', // For category permissions
            'category_type' => 'banner_type', // For category permissions
            'parent_spec__table_name' => null,
            'parent_spec__parent_name' => null,
            'parent_spec__field_name' => null,
            'category_is_string' => true,

            'title_field' => 'name',
            'title_field_dereference' => false,
            'description_field' => 'caption',
            'thumb_field' => 'img_url',

            'view_page_link_pattern' => '_SEARCH:banners:view:source=_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_banners:_edit:_WILD',
            'view_category_page_link_pattern' => null,
            'add_url' => (function_exists('has_submit_permission') && has_submit_permission('mid', get_member(), get_ip_address(), 'cms_banners')) ? (get_module_zone('cms_banners') . ':cms_banners:add') : null,
            'archive_url' => null,

            'support_url_monikers' => false,

            'views_field' => null,
            'submitter_field' => 'submitter',
            'add_time_field' => 'add_date',
            'edit_time_field' => 'edit_date',
            'date_field' => 'add_date',
            'validated_field' => 'validated',

            'seo_type_code' => null,

            'feedback_type_code' => null,

            'permissions_type_code' => null, // NULL if has no permissions

            'search_hook' => null,

            'addon_name' => 'banners',

            'cms_page' => 'cms_banners',
            'module' => 'banners',

            'commandr_filesystem_hook' => 'banners',
            'commandr_filesystem__is_folder' => false,

            'rss_hook' => null,

            'actionlog_regexp' => '\w+_BANNER',
        );
    }

    /**
     * Run function for content hooks. Renders a content box for an award/randomisation.
     *
     * @param  array $row The database row for the content
     * @param  ID_TEXT $zone The zone to display in
     * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
     * @param  boolean $include_breadcrumbs Whether to include breadcrumbs (if there are any)
     * @param  ?ID_TEXT $root Virtual root to use (null: none)
     * @param  boolean $attach_to_url_filter Whether to copy through any filter parameters in the URL, under the basis that they are associated with what this box is browsing
     * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
     * @return tempcode Results
     */
    public function run($row, $zone, $give_context = true, $include_breadcrumbs = true, $root = null, $attach_to_url_filter = false, $guid = '')
    {
        require_code('banners');

        return render_banner_box($row, $zone, $give_context, $guid);
    }
}
