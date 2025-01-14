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
 * @package    core_adminzone_dashboard
 */

/**
 * Hook class.
 */
class Hook_addon_registry_core_adminzone_dashboard
{
    /**
     * Get a list of file permissions to set
     *
     * @return array File permissions to set
     */
    public function get_chmod_array()
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'The dashboard tools shown in the Admin Zone.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_adminzone',
        );
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/css/adminzone_dashboard.css',
            'sources/hooks/systems/addon_registry/core_adminzone_dashboard.php',
            'themes/default/templates/BLOCK_MAIN_STAFF_NEW_VERSION.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_TIPS.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_CUSTOM_TASK.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_NA.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1.tpl',
            'themes/default/templates/BLOCK_MAIN_NOTES.tpl',
            'lang/EN/staff_checklist.ini',
            'sources/hooks/systems/cron/staff_checklist_notify.php',
            'sources/hooks/systems/notifications/staff_checklist.php',
            'themes/default/images/checklist/checklist-.png',
            'themes/default/images/checklist/checklist0.png',
            'themes/default/images/checklist/checklist1.png',
            'themes/default/images/checklist/toggleicon.png',
            'themes/default/images/checklist/toggleicon2.png',
            'themes/default/images/checklist/index.html',
            'themes/default/images/checklist/not_completed.png',
            'lang/EN/tips.ini',
            'sources/hooks/systems/snippets/checklist_task_manage.php',
            'sources/hooks/blocks/main_staff_checklist/.htaccess',
            'sources/hooks/blocks/main_staff_checklist/forum.php',
            'sources/hooks/blocks/main_staff_checklist/index.html',
            'sources/hooks/blocks/main_staff_checklist/copyright.php',
            'sources/hooks/blocks/main_staff_checklist/cron.php',
            'sources/hooks/blocks/main_staff_checklist/open_site.php',
            'sources/hooks/blocks/main_staff_checklist/profile.php',
            'sources/blocks/main_staff_actions.php',
            'sources/blocks/main_staff_checklist.php',
            'sources/blocks/main_staff_new_version.php',
            'sources/blocks/main_staff_tips.php',
            'sources/blocks/main_staff_website_monitoring.php',
            'sources/blocks/main_staff_links.php',
            'themes/default/templates/BLOCK_MAIN_STAFF_LINKS.tpl',
            'themes/default/templates/BLOCK_MAIN_STAFF_WEBSITE_MONITORING.tpl',
            'themes/default/images/checklist/cross.png',
            'themes/default/images/checklist/cross2.png',
            'sources/hooks/systems/notifications/checklist_task.php',
            'themes/default/templates/BLOCK_MAIN_STAFF_ACTIONS.tpl',
        );
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return array(
            'templates/BLOCK_MAIN_STAFF_CHECKLIST_CUSTOM_TASK.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_NOTES.tpl' => 'block_main_notes',
            'templates/BLOCK_MAIN_STAFF_CHECKLIST.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_STAFF_NEW_VERSION.tpl' => 'administrative__block_main_staff_new_version',
            'templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_NA.tpl' => 'administrative__block_main_staff_checklist',
            'templates/BLOCK_MAIN_STAFF_TIPS.tpl' => 'administrative__block_main_staff_tips',
            'templates/BLOCK_MAIN_STAFF_LINKS.tpl' => 'administrative__block_main_staff_links',
            'templates/BLOCK_MAIN_STAFF_WEBSITE_MONITORING.tpl' => 'administrative__block_main_staff_website_monitoring',
            'templates/BLOCK_MAIN_STAFF_ACTIONS.tpl' => 'administrative__block_main_staff_actions',
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_website_monitoring()
    {
        $data = array();
        foreach (placeholder_array() as $v) {
            $data[] = array(
                'SITETITLE' => lorem_phrase(),
                'GRANK' => placeholder_number(),
                'ALEXAR' => placeholder_number(),
                'ALEXAT' => placeholder_number(),
                'URL' => placeholder_url(),
            );
        }

        $urls = array();
        foreach (placeholder_array() as $v) {
            $urls[] = array(
                '_loop_key' => lorem_word(),
                '_loop_var' => lorem_word_2(),
            );
        }

        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_WEBSITE_MONITORING', array(
                'GRIDDATA' => $data,
                'URL' => placeholder_url(),
                'SITEURLS' => $urls,
                'BLOCK_NAME' => '',
                'MAP' => '',
                'BLOCK_PARAMS' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_links()
    {
        $formatted = array();
        foreach (placeholder_array() as $v) {
            $formatted[] = array(
                'TITLE' => lorem_word(),
                'DESC' => lorem_word_2(),
            );
        }
        $unformatted = array();
        foreach (placeholder_array() as $v) {
            $unformatted[] = array(
                'LINKS' => placeholder_url(),
            );
        }

        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_LINKS', array(
                'FORMATTED_LINKS' => $formatted,
                'UNFORMATTED_LINKS' => $unformatted,
                'URL' => placeholder_url(),
                'BLOCK_NAME' => '',
                'MAP' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_main_notes()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_NOTES', array(
                'TITLE' => lorem_word(),
                'SCROLLS' => lorem_phrase(),
                'CONTENTS' => lorem_phrase(),
                'URL' => placeholder_url(),
                'BLOCK_NAME' => '',
                'MAP' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_checklist()
    {
        $_status = do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1');

        $info = do_lang_tempcode('DUE_TIME', placeholder_number(), placeholder_number());

        $dates = do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array(
            'URL' => placeholder_url(),
            'STATUS' => $_status,
            'CONTACT_US_MESSAGING' => lorem_phrase(),
            'TASK' => lorem_phrase(),
            'INFO' => $info,
            'NUM_QUEUE' => placeholder_id(),
        ));

        $status = do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0', array());

        $url = build_url(array(
            'page' => 'admin_orders',
            'type' => 'show_orders',
            'filter' => 'undispatched',
        ), get_module_zone('admin_orders'));

        $no_times = do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array(
            'URL' => placeholder_url(),
            'STATUS' => $status,
            'TASK' => lorem_phrase(),
            'CONTACT_US_MESSAGING' => lorem_phrase(),
            'INFO' => lorem_phrase(),
            'NUM_QUEUE' => placeholder_id(),
        ));

        $todo = new Tempcode();
        $todo->attach(do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array(
            'URL' => placeholder_url(),
            'STATUS' => do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1'),
            'CONTACT_US_MESSAGING' => lorem_phrase(),
            'TASK' => lorem_phrase(),
            'INFO' => lorem_phrase(),
            'NUM_QUEUE' => placeholder_id(),
        )));
        $todo->attach(do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array(
            'URL' => placeholder_url(),
            'STATUS' => do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_NA'),
            'CONTACT_US_MESSAGING' => lorem_phrase(),
            'TASK' => lorem_phrase(),
            'INFO' => lorem_phrase(),
            'NUM_QUEUE' => placeholder_id(),
        )));

        $custom_task = new Tempcode();
        foreach (placeholder_array() as $k => $v) {
            $custom_task->attach(do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST_CUSTOM_TASK', array(
                'TASK_DONE' => 'checklist0',
                'ADD_TIME' => placeholder_time(),
                'RECUR_INTERVAL' => '',
                'ID' => placeholder_id(),
                'TASK_TITLE' => lorem_word_2(),
            )));
        }

        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_CHECKLIST', array(
                'URL' => placeholder_url(),
                'NOTES' => lorem_phrase(),
                'CUSTOM_TASKS' => $custom_task,
                'DATES' => $dates,
                'NO_TIMES' => $no_times,
                'TODO_COUNTS' => $todo,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_new_version()
    {
        require_lang('version');
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_NEW_VERSION', array(
                'VERSION' => lorem_phrase(),
                'VERSION_TABLE' => placeholder_table(),
                'HAS_UPDATED_ADDONS' => true,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_tips()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_TIPS', array(
                'BLOCK_PARAMS' => '',
                'TIP' => lorem_phrase(),
                'TIP_CODE' => lorem_phrase(),
                'LEVEL' => lorem_phrase(),
                'COUNT' => placeholder_number(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__block_main_staff_actions()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_STAFF_ACTIONS', array(
                'BLOCK_PARAMS' => '',
                'CONTENT' => lorem_paragraph_html(),
            )), null, '', true)
        );
    }
}
