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
 * @package    import
 */

/**
 * Module page class.
 */
class Module_admin_xml_storage
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
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
        return $info;
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return NULL to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('XML_STORAGE', 'menu/adminzone/tools/bulk_content_actions/xml_storage'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know meta-data for <head> before we start streaming output.
     *
     * @return ?tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        $type = get_param_string('type', 'browse');

        require_lang('xml_storage');

        set_helper_panel_text(comcode_lang_string('DOC_XML_STORAGE'));

        if ($type == '_import' || $type == '_export') {
            breadcrumb_set_self(do_lang_tempcode('_RESULTS'));
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('XML_STORAGE'))));
        }

        if ($type == 'browse') {
            $this->title = get_screen_title('XML_STORAGE');
        }

        if ($type == '_import') {
            $this->title = get_screen_title('IMPORT');
        }

        if ($type == '_export') {
            $this->title = get_screen_title('EXPORT');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return tempcode The result of execution.
     */
    public function run()
    {
        $type = get_param_string('type', 'browse');

        require_code('xml_storage');
        require_lang('import');
        require_lang('xml_storage');

        switch ($type) {
            case 'browse':
                return $this->ui();

            case '_import':
                return $this->_import();

            case '_export':
                return $this->_export();
        }

        return new Tempcode();
    }

    /**
     * Interface to import/export.
     *
     * @return tempcode The interface.
     */
    public function ui()
    {
        require_code('form_templates');

        url_default_parameters__enable();
        $import_url = build_url(array('page' => '_SELF', 'type' => '_import'), '_SELF');
        $import_fields = new Tempcode();
        $import_fields->attach(form_input_huge(do_lang_tempcode('XML_DATA'), '', 'xml', '', true));
        $import_form = do_template('FORM', array('_GUID' => '82005575f2a31c362d4a1a79d7a0c247', 'TABINDEX' => strval(get_form_field_tabindex()), 'URL' => $import_url, 'HIDDEN' => '', 'TEXT' => do_lang_tempcode('XML_IMPORT_TEXT'), 'FIELDS' => $import_fields, 'SUBMIT_ICON' => 'menu___generic_admin__import', 'SUBMIT_NAME' => do_lang_tempcode('IMPORT')));
        url_default_parameters__disable();

        $all_tables = find_all_xml_tables();
        $export_url = build_url(array('page' => '_SELF', 'type' => '_export'), '_SELF');
        $export_fields = new Tempcode();
        $nice_tables = new Tempcode();
        foreach ($all_tables as $table) {
            $nice_tables->attach(form_input_list_entry($table));
        }
        require_lang('comcode');
        $export_fields->attach(form_input_multi_list(do_lang_tempcode('TABLES'), do_lang_tempcode('DESCRIPTION_TABLES'), 'tables', $nice_tables, null, 15));
        $export_form = do_template('FORM', array('_GUID' => 'fafc396037e375bdd84582ef8170ec1b', 'TABINDEX' => strval(get_form_field_tabindex()), 'URL' => $export_url, 'HIDDEN' => '', 'TEXT' => do_lang_tempcode('XML_EXPORT_TEXT'), 'FIELDS' => $export_fields, 'SUBMIT_ICON' => 'menu___generic_admin__export', 'SUBMIT_NAME' => do_lang_tempcode('EXPORT')));

        return do_template('XML_STORAGE_SCREEN', array('_GUID' => '8618fbb96fe29689dbbf8edd60444b1e', 'TITLE' => $this->title, 'IMPORT_FORM' => $import_form, 'EXPORT_FORM' => $export_form));
    }

    /**
     * Actualiser to do an import.
     *
     * @return tempcode The results.
     */
    public function _import()
    {
        $xml = post_param_string('xml');

        $ops = import_from_xml($xml);

        $ops_nice = array();
        foreach ($ops as $op) {
            $ops_nice[] = array('OP' => $op[0], 'PARAM_A' => $op[1], 'PARAM_B' => array_key_exists(2, $op) ? $op[2] : '');
        }

        // Clear some caching
        require_code('caches3');
        erase_comcode_page_cache();
        erase_block_cache();
        erase_persistent_cache();

        return do_template('XML_STORAGE_IMPORT_RESULTS_SCREEN', array('_GUID' => '6960a7ab06fdccf81f480c3895eb8442', 'TITLE' => $this->title, 'OPS' => $ops_nice));
    }

    /**
     * Actualiser to do an export.
     *
     * @return tempcode The results.
     */
    public function _export()
    {
        if (!array_key_exists('tables', $_POST)) {
            warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN'));
        }

        $xml = export_to_xml($_POST['tables']);

        return do_template('XML_STORAGE_EXPORT_RESULTS_SCREEN', array('_GUID' => 'c7053f328b27709b529f2cd88513d85d', 'TITLE' => $this->title, 'XML' => $xml));
    }
}
