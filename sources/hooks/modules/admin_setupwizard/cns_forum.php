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
 * @package    cns_forum
 */

/**
 * Hook class.
 */
class Hook_sw_cns_forum
{
    /**
     * Run function for features in the setup wizard.
     *
     * @return array Current settings.
     */
    public function get_current_settings()
    {
        $settings = array();

        $dbs_back = $GLOBALS['NO_DB_SCOPE_CHECK'];
        $GLOBALS['NO_DB_SCOPE_CHECK'] = true;

        require_lang('cns');

        if (!is_cns_satellite_site()) {
            $test = $GLOBALS['SITE_DB']->query_select_value_if_there('f_groups', 'id', array('id' => db_get_first_id() + 7));
            $settings['have_default_rank_set'] = is_null($test) ? '0' : '1';

            $test = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'f_emoticons WHERE e_code<>\':P\' AND e_code<>\';)\' AND e_code<>\':)\' AND e_code<>\':)\' AND e_code<>\':\\\'(\'');
            $settings['have_default_full_emoticon_set'] = (count($test) != 0) ? '1' : '0';

            $have_default_cpf_set = false;
            $fields_l = array('im_jabber', 'im_skype', 'interests', 'location', 'occupation', 'sn_google', 'sn_facebook', 'sn_twitter');
            foreach ($fields_l as $field) {
                $test = $GLOBALS['SITE_DB']->query_select_value_if_there('f_custom_fields', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('cf_name') => do_lang('DEFAULT_CPF_' . $field . '_NAME')));
                if (!is_null($test)) {
                    $have_default_cpf_set = true;
                    break;
                }
            }
            $settings['have_default_cpf_set'] = $have_default_cpf_set ? '1' : '0';
        }

        $GLOBALS['NO_DB_SCOPE_CHECK'] = $dbs_back;

        return $settings;
    }

    /**
     * Run function for features in the setup wizard.
     *
     * @param  array $field_defaults Default values for the fields, from the install-profile.
     * @return tempcode An input field.
     */
    public function get_fields($field_defaults)
    {
        if (get_forum_type() != 'cns') {
            return new Tempcode();
        }

        $current_settings = $this->get_current_settings();
        $field_defaults += $current_settings; // $field_defaults will take precedence, due to how "+" operator works in PHP

        require_lang('cns');
        $fields = new Tempcode();

        if (!is_cns_satellite_site()) {
            if ($current_settings['have_default_rank_set'] == '1') {
                $fields->attach(form_input_tick(do_lang_tempcode('HAVE_DEFAULT_RANK_SET'), do_lang_tempcode('DESCRIPTION_HAVE_DEFAULT_RANK_SET'), 'have_default_rank_set', $field_defaults['have_default_rank_set'] == '1'));
            }

            $fields->attach(form_input_tick(do_lang_tempcode('HAVE_DEFAULT_FULL_EMOTICON_SET'), do_lang_tempcode('DESCRIPTION_HAVE_DEFAULT_FULL_EMOTICON_SET'), 'have_default_full_emoticon_set', $field_defaults['have_default_full_emoticon_set'] == '1'));

            if ($current_settings['have_default_cpf_set'] == '1') {
                $fields->attach(form_input_tick(do_lang_tempcode('HAVE_DEFAULT_CPF_SET'), do_lang_tempcode('DESCRIPTION_HAVE_DEFAULT_CPF_SET'), 'have_default_cpf_set', $field_defaults['have_default_cpf_set'] == '1'));
            }
        }

        return $fields;
    }

    /**
     * Run function for setting features from the setup wizard.
     */
    public function set_fields()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $dbs_back = $GLOBALS['NO_DB_SCOPE_CHECK'];
        $GLOBALS['NO_DB_SCOPE_CHECK'] = true;

        require_lang('cns');
        if (!is_cns_satellite_site()) {
            if (post_param_integer('have_default_rank_set', 0) == 0) {
                $group_rows = $GLOBALS['SITE_DB']->query_select('f_groups', array('id'), array('id' => db_get_first_id() + 8));
                if (array_key_exists(0, $group_rows)) {
                    $promotion_target = cns_get_group_property(db_get_first_id() + 8, 'promotion_target');
                    if (!is_null($promotion_target)) {
                        $GLOBALS['SITE_DB']->query_update('f_groups', array('g_promotion_target' => null, 'g_promotion_threshold' => null, 'g_rank_image' => ''), array('id' => db_get_first_id() + 8), '', 1);
                        for ($i = db_get_first_id() + 4; $i < db_get_first_id() + 8; $i++) {
                            require_code('cns_groups_action');
                            require_code('cns_groups_action2');
                            cns_delete_group($i);
                        }
                    }
                    $GLOBALS['SITE_DB']->query_update('f_groups', lang_remap('g_name', $group_rows[0]['id'], do_lang('MEMBER')), array('id' => db_get_first_id() + 8), '', 1);
                }
            }
            if (post_param_integer('have_default_full_emoticon_set', 0) == 0) {
                $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'f_emoticons WHERE e_code<>\':P\' AND e_code<>\';)\' AND e_code<>\':)\' AND e_code<>\':)\' AND e_code<>\':\\\'(\'');
            }
            if (post_param_integer('have_default_cpf_set', 0) == 0) {
                $fields = array('im_skype', 'interests', 'location', 'occupation');
                foreach ($fields as $field) {
                    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('f_custom_fields', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('cf_name') => do_lang('DEFAULT_CPF_' . $field . '_NAME')));
                    if (!is_null($test)) {
                        require_code('cns_members_action');
                        require_code('cns_members_action2');
                        cns_delete_custom_field($test);
                    }
                }
            }
        }

        $GLOBALS['NO_DB_SCOPE_CHECK'] = $dbs_back;
    }

    /**
     * Run function for blocks in the setup wizard.
     *
     * @return array Map of block names, to display types.
     */
    public function get_blocks()
    {
        if (get_forum_type() == 'cns') {
            return array(array(), array('side_cns_private_topics' => array('PANEL_NONE', 'PANEL_NONE')));
        }
        return array(array(), array());
    }
}
