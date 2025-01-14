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
 * @package    staff
 */

/**
 * Module page class.
 */
class Module_admin_staff
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
        $info['update_require_upgrade'] = 1;
        $info['version'] = 3;
        $info['locked'] = true;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        $usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true);
        foreach (array_keys($usergroups) as $id) {
            $GLOBALS['SITE_DB']->query_insert('group_page_access', array('page_name' => 'admin_staff', 'zone_name' => 'adminzone', 'group_id' => $id));
        }
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
            'browse' => array('MANAGE_STAFF', 'menu/site_meta/staff'),
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

        require_lang('staff');

        set_helper_panel_tutorial('tut_staff');

        if ($type == 'browse') {
            $this->title = get_screen_title('MANAGE_STAFF');
        }

        if ($type == 'edit') {
            $this->title = get_screen_title('EDIT_STAFF');
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

        if ($type == 'browse') {
            return $this->staff_interface();
        }
        if ($type == 'edit') {
            return $this->staff_edit();
        }

        return new Tempcode();
    }

    /**
     * The UI for editing staff information.
     *
     * @return tempcode The UI
     */
    public function staff_interface()
    {
        if (get_forum_type() == 'none') {
            warn_exit(do_lang_tempcode('NO_MEMBER_SYSTEM_INSTALLED'));
        }

        if (get_option('is_on_staff_filter') == '0') {
            $text = do_lang_tempcode('STAFF_FILTER_OFF');
        } else {
            $text = do_lang_tempcode('STAFF_FILTER_ON');
        }

        $admin_groups = array_merge($GLOBALS['FORUM_DRIVER']->get_super_admin_groups(), $GLOBALS['FORUM_DRIVER']->get_moderator_groups());
        $staff = $GLOBALS['FORUM_DRIVER']->member_group_query($admin_groups, 400);
        if (count($staff) >= 400) {
            warn_exit(do_lang_tempcode('TOO_MANY_TO_CHOOSE_FROM'));
        }
        $available = new Tempcode();
        require_code('form_templates');
        foreach ($staff as $row_staff) {
            $id = $GLOBALS['FORUM_DRIVER']->mrow_id($row_staff);
            $username = $GLOBALS['FORUM_DRIVER']->mrow_username($row_staff);
            $role = get_cms_cpf('role', $id);
            $fullname = get_cms_cpf('fullname', $id);

            $fields = form_input_line(do_lang_tempcode('REALNAME'), '', 'fullname_' . strval($id), $fullname, false);
            $fields->attach(form_input_line(do_lang_tempcode('ROLE'), do_lang_tempcode('DESCRIPTION_ROLE'), 'role_' . strval($id), $role, false));

            if (get_option('is_on_staff_filter') == '1') {
                if ($GLOBALS['FORUM_DRIVER']->is_staff($id)) {
                    $submit_name = do_lang_tempcode('REMOVE');
                    $submit_type = 'remove';
                } else {
                    $submit_name = do_lang_tempcode('ADD');
                    $submit_type = 'add';
                }

                $fields->attach(form_input_tick($submit_name, '', $submit_type . '_' . strval($id), false));
            }

            $form = do_template('FORM_GROUP', array('_GUID' => '0e7d362817a7f3ae190536adf632fe59', 'HIDDEN' => form_input_hidden('staff_' . strval($id), strval($id)), 'FIELDS' => $fields));

            $available->attach(do_template('STAFF_EDIT_WRAPPER', array('_GUID' => 'ab0516dba94c20b4d97f68677053b20d', 'FORM' => $form, 'USERNAME' => $username)));
        }
        if (!$available->is_empty()) {
            $post_url = build_url(array('page' => '_SELF', 'type' => 'edit'), '_SELF');
            $available = do_template('FORM_GROUPED', array('_GUID' => '5b74208b6c420edcdeb34bb49f1e9dcb', 'TEXT' => '', 'URL' => $post_url, 'FIELD_GROUPS' => $available, 'SUBMIT_ICON' => 'buttons__save', 'SUBMIT_NAME' => do_lang_tempcode('SAVE'), 'SUPPORT_AUTOSAVE' => true));
        }

        return do_template('STAFF_ADMIN_SCREEN', array('_GUID' => '101087b0dbe5d679a55bb661ad7350fa', 'TITLE' => $this->title, 'TEXT' => $text, 'FORUM_STAFF' => $available));
    }

    /**
     * The actualiser for editing staff information.
     *
     * @return tempcode The UI
     */
    public function staff_edit()
    {
        foreach ($_POST as $key => $val) {
            if (!is_string($val)) {
                continue;
            }
            if (substr($key, 0, 6) == 'staff_') {
                $id = intval($val); // e.g. $key=staff_2, $val=2  - so could also say $id=intval(substr($key,6));

                $this->_staff_edit($id, post_param_string('role_' . strval($id)), post_param_string('fullname_' . strval($id)));

                if ((post_param_integer('remove_' . strval($id), 0) == 1) && (get_option('is_on_staff_filter') == '1')) {
                    $this->_staff_remove($id);
                } elseif (post_param_integer('add_' . strval($id), 0) == 1) {
                    $this->_staff_add($id);
                }
            }
        }

        // Show it worked / Refresh
        $url = build_url(array('page' => '_SELF', 'type' => 'browse'), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }

    /**
     * Edit a member of staff.
     *
     * @param  MEMBER $id The member ID of the staff being edited
     * @param  SHORT_TEXT $role The role of the staff member
     * @param  SHORT_TEXT $fullname The full-name of the staff member
     */
    public function _staff_edit($id, $role, $fullname)
    {
        $GLOBALS['FORUM_DRIVER']->set_custom_field($id, 'role', $role);
        $GLOBALS['FORUM_DRIVER']->set_custom_field($id, 'fullname', $fullname);

        log_it('EDIT_STAFF', strval($id));
    }

    /**
     * Add a member of staff.
     *
     * @param  MEMBER $id The ID of the member to add as staff
     */
    public function _staff_add($id)
    {
        $sites = get_cms_cpf('sites', $id);
        if ($sites != '') {
            $sites .= ', ';
        }
        $sites .= substr(get_site_name(), 0, 200);
        $GLOBALS['FORUM_DRIVER']->set_custom_field($id, 'sites', $sites);

        log_it('ADD_STAFF', strval($id));
    }

    /**
     * Remove a member of staff.
     *
     * @param  MEMBER $id The ID of the member to remove from the staff
     */
    public function _staff_remove($id)
    {
        $sites = get_cms_cpf('sites', $id);

        // Lets try to cleanly remove it
        $sites = str_replace(', ' . substr(get_site_name(), 0, 200), '', $sites);
        $sites = str_replace(',' . substr(get_site_name(), 0, 200), '', $sites);
        $sites = str_replace(substr(get_site_name(), 0, 200) . ', ', '', $sites);
        $sites = str_replace(substr(get_site_name(), 0, 200) . ',', '', $sites);
        $sites = str_replace(substr(get_site_name(), 0, 200), '', $sites);
        if (substr($sites, 0, 2) == ', ') {
            $sites = substr($sites, 2);
        }

        $GLOBALS['FORUM_DRIVER']->set_custom_field($id, 'sites', $sites);

        log_it('REMOVE_STAFF', strval($id));
    }
}
