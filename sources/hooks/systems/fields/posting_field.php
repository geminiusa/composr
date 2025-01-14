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
 * @package    core_fields
 */

/**
 * Hook class.
 */
class Hook_fields_posting_field
{
    // ==============
    // Module: search
    // ==============

    /**
     * Get special Tempcode for inputting this field.
     *
     * @param  array $field The field details
     * @return ?array Specially encoded input detail rows (null: nothing special)
     */
    public function get_search_inputter($field)
    {
        return null;
    }

    /**
     * Get special SQL from POSTed parameters for this field.
     *
     * @param  array $field The field details
     * @param  integer $i We're processing for the ith row
     * @return ?array Tuple of SQL details (array: extra trans fields to search, array: extra plain fields to search, string: an extra table segment for a join, string: the name of the field to use as a title, if this is the title, extra WHERE clause stuff) (null: nothing special)
     */
    public function inputted_to_sql_for_search($field, $i)
    {
        return null;
    }

    // ===================
    // Backend: fields API
    // ===================

    /**
     * Get some info bits relating to our field type, that helps us look it up / set defaults.
     *
     * @param  ?array $field The field details (null: new field)
     * @param  ?boolean $required Whether a default value cannot be blank (null: don't "lock in" a new default value)
     * @param  ?string $default The given default value as a string (null: don't "lock in" a new default value)
     * @param  ?object $db Database connection (null: main site database)
     * @return array Tuple of details (row-type,default-value-to-use,db row-type)
     */
    public function get_field_value_row_bits($field, $required = null, $default = null, $db = null)
    {
        if ($required !== null) {
            if (($required) && ($default == '')) {
                $default = 'default';
            }
        }
        return array('long_trans', $default, 'long_trans');
    }

    /**
     * Convert a field value to something renderable.
     *
     * @param  array $field The field details
     * @param  mixed $ev The raw value
     * @return mixed Rendered field (tempcode or string)
     */
    public function render_field_value($field, $ev)
    {
        if (is_object($ev)) {
            return $ev;
        }
        return escape_html($ev);
    }

    // ======================
    // Frontend: fields input
    // ======================

    /**
     * Get form inputter.
     *
     * @param  string $_cf_name The field name
     * @param  string $_cf_description The field description
     * @param  array $field The field details
     * @param  ?string $actual_value The actual current value of the field (null: none)
     * @param  boolean $new Whether this is for a new entry
     * @param  boolean $last Whether this is the last field in the catalogue
     * @return ?tempcode The Tempcode for the input field (null: skip the field - it's not input)
     */
    public function get_field_inputter($_cf_name, $_cf_description, $field, $actual_value, $new, $last = true)
    {
        if (is_null($actual_value)) {
            $actual_value = ''; // Plug anomaly due to unusual corruption
        }

        require_lang('javascript');
        require_javascript('posting');
        require_javascript('editing');
        require_javascript('ajax');
        require_javascript('plupload');
        require_css('widget_plupload');

        require_lang('comcode');

        $tabindex = get_form_field_tabindex();

        $input_name = empty($field['cf_input_name']) ? ('field_' . strval($field['id'])) : $field['cf_input_name'];

        $actual_value = filter_form_field_default($_cf_name, $actual_value);

        list($attachments, $attach_size_field) = get_attachments($input_name);

        $hidden_fields = new Tempcode();
        $hidden_fields->attach($attach_size_field);

        $help_zone = get_comcode_zone('userguide_comcode', false);

        $emoticon_chooser = $GLOBALS['FORUM_DRIVER']->get_emoticon_chooser($input_name);

        $comcode_editor = get_comcode_editor($input_name);
        $comcode_editor_small = get_comcode_editor($input_name, true);

        $w = (has_js()) && (browser_matches('wysiwyg') && (strpos($actual_value, '{$,page hint: no_wysiwyg}') === false));

        $class = '';
        attach_wysiwyg();
        if ($w) {
            $class .= ' wysiwyg';
        }

        global $LAX_COMCODE;
        $temp = $LAX_COMCODE;
        $LAX_COMCODE = true;
        $GLOBALS['COMCODE_PARSE_URLS_CHECKED'] = 100; // Little hack to stop it checking any URLs
        /*We want to always reparse with semi-parse mode if (is_null($default_parsed)) */
        $default_parsed = comcode_to_tempcode($actual_value, null, false, null, null, null, true);
        $LAX_COMCODE = $temp;

        $attachments_done = true;

        $ret = new Tempcode();

        $ret->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('TITLE' => $_cf_name)));

        $ret->attach(do_template('POSTING_FIELD', array(
            '_GUID' => 'b6c65227a28e0650154393033e005f67',
            'REQUIRED' => ($field['cf_required'] == 1),
            'DESCRIPTION' => $_cf_description,
            'HIDDEN_FIELDS' => $hidden_fields,
            'PRETTY_NAME' => $_cf_name,
            'NAME' => $input_name,
            'TABINDEX_PF' => strval($tabindex)/*not called TABINDEX due to conflict with FORM_STANDARD_END*/,
            'COMCODE_EDITOR' => $comcode_editor,
            'COMCODE_EDITOR_SMALL' => $comcode_editor_small,
            'CLASS' => $class,
            'COMCODE_URL' => is_null($help_zone) ? new Tempcode() : build_url(array('page' => 'userguide_comcode'), $help_zone),
            'EMOTICON_CHOOSER' => $emoticon_chooser,
            'POST' => $actual_value,
            'DEFAULT_PARSED' => $default_parsed,
            'ATTACHMENTS' => $attachments,
        )));

        if (!$last) {
            $ret->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '168edca41bd0c3da936d9154d696163e', 'TITLE' => do_lang_tempcode('ADDITIONAL_INFO'))));
        }

        return $ret;
    }

    /**
     * Find the posted value from the get_field_inputter field
     *
     * @param  boolean $editing Whether we were editing (because on edit, it could be a fractional edit)
     * @param  array $field The field details
     * @param  ?string $upload_dir Where the files will be uploaded to (null: do not store an upload, return NULL if we would need to do so)
     * @param  ?array $old_value Former value of field (null: none)
     * @return ?string The value (null: could not process)
     */
    public function inputted_to_field_value($editing, $field, $upload_dir = 'uploads/catalogues', $old_value = null)
    {
        $id = $field['id'];
        $tmp_name = 'field_' . strval($id);
        return post_param_string($tmp_name, $editing ? STRING_MAGIC_NULL : '');
    }
}
