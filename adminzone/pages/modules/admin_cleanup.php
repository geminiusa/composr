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
 * @package    core_cleanup_tools
 */

/**
 * Module page class.
 */
class Module_admin_cleanup
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
        $info['version'] = 3;
        $info['locked'] = false;
        $info['update_require_upgrade'] = 1;
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
            'browse' => array('CLEANUP_TOOLS', 'menu/adminzone/tools/cleanup'),
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

        require_lang('cleanup');

        set_helper_panel_tutorial('tut_cleanup');

        if ($type == 'rebuild') {
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('CLEANUP_TOOLS'))));
            breadcrumb_set_self(do_lang_tempcode('DONE'));
        }

        $this->title = get_screen_title('CLEANUP_TOOLS');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return tempcode The result of execution.
     */
    public function run()
    {
        require_code('caches3');

        // Decide what we're doing
        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->choose_cache_type();
        }
        if ($type == 'rebuild') {
            return $this->do_rebuild();
        }

        return new Tempcode();
    }

    /**
     * The UI for choosing caches to empty.
     *
     * @return tempcode The UI
     */
    public function choose_cache_type()
    {
        $hooks = find_all_hooks('systems', 'cleanup');

        $url = build_url(array('page' => '_SELF', 'type' => 'rebuild'), '_SELF');

        require_code('form_templates');

        $fields_cache = new Tempcode();
        $fields_optimise = new Tempcode();
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/cleanup/' . filter_naughty_harsh($hook));
            $object = object_factory('Hook_cleanup_' . filter_naughty_harsh($hook), true);
            if (is_null($object)) {
                continue;
            }
            $output = $object->info();
            if (!is_null($output)) {
                $tick = form_input_tick($output['title'], $output['description'], $hook, false);
                if ($output['type'] == 'cache') {
                    $fields_cache->attach($tick);
                } else {
                    $fields_optimise->attach($tick);
                }
            }
        }

        $fields = new Tempcode();
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '4a9d6e722f246887160c444a062a9d00', 'SECTION_HIDDEN' => true, 'TITLE' => do_lang_tempcode('CACHES_PAGE_EXP_OPTIMISERS'), 'HELP' => '')));
        $fields->attach($fields_optimise);
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '3ddb387dba8c42ac4ef7b85621052e11', 'TITLE' => do_lang_tempcode('CACHES_PAGE_EXP_CACHES'), 'HELP' => do_lang_tempcode('CACHES_PAGE_CACHES'))));
        $fields->attach($fields_cache);

        return do_template('FORM_SCREEN', array('_GUID' => '85bfdf171484604594a157aa8983f920', 'SKIP_WEBSTANDARDS' => true, 'TEXT' => do_lang_tempcode('CACHES_PAGE'), 'SUBMIT_ICON' => 'menu__adminzone__tools__cleanup', 'SUBMIT_NAME' => do_lang_tempcode('PROCEED'), 'HIDDEN' => '', 'TITLE' => $this->title, 'FIELDS' => $fields, 'URL' => $url));
    }

    /**
     * The actualiser for emptying caches.
     *
     * @return tempcode The UI
     */
    public function do_rebuild()
    {
        $hooks = find_all_hooks('systems', 'cleanup');

        // Fiddle the order a bit
        if (array_key_exists('cns_topics', $hooks)) {
            unset($hooks['cns_topics']);
            $hooks['cns_topics'] = 'sources_custom';
        }
        if (array_key_exists('cns', $hooks)) {
            unset($hooks['cns']);
            $hooks['cns'] = 'sources_custom';
        }
        if (array_key_exists('cns_members', $hooks)) {
            unset($hooks['cns_members']);
            $hooks['cns_members'] = 'sources_custom';
        }

        $todo = array();
        foreach (array_keys($hooks) as $hook) {
            if (post_param_integer($hook, 0) == 1) {
                $todo[] = $hook;
            }
        }
        $messages = composr_cleanup($todo);
        $messages->attach(paragraph(do_lang_tempcode('SUCCESS')));

        return do_template('CLEANUP_COMPLETED_SCREEN', array('_GUID' => '598510a9ad9f01f3c0806319b32b5033', 'TITLE' => $this->title, 'MESSAGES' => $messages));
    }
}
