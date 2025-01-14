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
 * @package    core_comcode_pages
 */

/**
 * Hook class.
 */
class Hook_content_meta_aware_comcode_page
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
            'supports_custom_fields' => true,

            'content_type_label' => 'zones:COMCODE_PAGE',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'comcode_pages',
            'id_field' => array('the_page', 'the_zone'),
            'id_field_numeric' => false,
            'parent_category_field' => 'the_zone',
            'parent_category_meta_aware_type' => null,
            'is_category' => false,
            'is_entry' => true,
            'category_field' => array('the_zone', 'the_page'), // For category permissions
            'category_type' => '<page>', // For category permissions ("<page>" means use page permissions)
            'parent_spec__table_name' => null,
            'parent_spec__parent_name' => null,
            'parent_spec__field_name' => null,
            'category_is_string' => true,

            'title_field' => 'the_page',
            'title_field_dereference' => false,
            'description_field' => null,
            'thumb_field' => null,

            'view_page_link_pattern' => '_WILD:_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_comcode_pages:_edit:page_link=_WILD',
            'view_category_page_link_pattern' => '_WILD:',
            'add_url' => (function_exists('has_submit_permission') && has_submit_permission('high', get_member(), get_ip_address(), 'cms_comcode_pages')) ? (get_module_zone('cms_comcode_pages') . ':cms_comcode_pages:edit') : null,
            'archive_url' => ((!is_null($zone)) ? $zone : get_page_zone('sitemap')) . ':sitemap',

            'support_url_monikers' => true,

            'views_field' => null,
            'submitter_field' => 'p_submitter',
            'add_time_field' => 'p_add_date',
            'edit_time_field' => 'p_edit_date',
            'date_field' => 'p_add_date',
            'validated_field' => 'p_validated',

            'seo_type_code' => 'comcode_page',

            'feedback_type_code' => null,

            'permissions_type_code' => null,

            'search_hook' => 'comcode_pages',

            'addon_name' => 'core_comcode_pages',

            'module' => null,
            'cms_page' => 'cms_comcode_pages',

            'commandr_filesystem_hook' => 'comcode_pages',
            'commandr_filesystem__is_folder' => false,

            'rss_hook' => 'comcode_pages',

            'actionlog_regexp' => '\w+_COMCODE_PAGE',
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
        unset($zone); // Meaningless here

        require_code('zones2');

        return render_comcode_page_box($row, $give_context, $include_breadcrumbs, $root, $guid);
    }
}
