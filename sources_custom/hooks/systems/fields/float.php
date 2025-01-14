<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 */
class Hook_fields_float
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
        $type = '_FLOAT';
        $extra = '';
        $display = get_translated_text($field['cf_name']);

        $range_search = (option_value_from_field_array($field, 'range_search', 'off') == 'on');
        if ($range_search) {
            $type .= '_RANGE';
            $special = get_param_string('option_' . strval($field['id']) . '_from', '') . ';' . get_param_string('option_' . strval($field['id']) . '_to', '');
        } else {
            $special = get_param_string('option_' . strval($field['id']), '');
        }

        return array('NAME' => strval($field['id']) . $extra, 'DISPLAY' => $display, 'TYPE' => $type, 'SPECIAL' => $special);
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
        $range_search = (option_value_from_field_array($field, 'range_search', 'off') == 'on');
        if ($range_search) {
            return null;
        }

        return exact_match_sql($field, $i, 'float');
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
     * @return array Tuple of details (row-type,default-value-to-use,db row-type)
     */
    public function get_field_value_row_bits($field, $required = null, $default = null)
    {
        unset($field);
        if ($required !== null) {
            if (($required) && ($default == '')) {
                $default = '0';
            }
        }
        return array('float_unescaped', $default, 'float');
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
        require_lang('google_map');
        $_cf_name = get_translated_text($field['cf_name']);
        if (($_cf_name == do_lang('LATITUDE_FIELD_NAME')) || ($_cf_name == do_lang('LONGITUDE_FIELD_NAME'))) {
            if (is_object($ev)) {
                if ($ev->evaluate() == do_lang('NA_EM')) {
                    return ''; // Cleanup noisy data
                }
            }
        }

        if (is_object($ev)) {
            if ($ev->evaluate() == do_lang('NA_EM')) {
                return '';
            }

            return $ev;
        }

        if ($ev == '') {
            return '';
        }

        $ev = float_format(floatval($ev));

        if (($GLOBALS['XSS_DETECT']) && (ocp_is_escaped($ev))) {
            ocp_mark_as_escaped($ev);
        }
        return $ev;
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
     * @return ?tempcode The Tempcode for the input field (null: skip the field - it's not input)
     */
    public function get_field_inputter($_cf_name, $_cf_description, $field, $actual_value, $new)
    {
        require_lang('google_map');

        if ($actual_value === do_lang('NA')) {
            $actual_value = null;
        }

        $input_name = empty($field['cf_input_name']) ? ('field_' . strval($field['id'])) : $field['cf_input_name'];

        if ($_cf_name == do_lang('LONGITUDE_FIELD_NAME')) { // Assumes there is a Latitude field too, although not critical
            $pretty_name = $_cf_name;
            $description = $_cf_description;
            $required = $field['cf_required'] == 1;

            $latitude = '0';
            $longitude = '0';

            if ((isset($actual_value)) && ($actual_value != '') && ($actual_value != do_lang('NA'))) {
                $longitude = float_to_raw_string(floatval($actual_value), 10);
            }
            global $LATITUDE;
            if ((isset($LATITUDE)) && ($LATITUDE != '') && ($LATITUDE != do_lang('NA'))) {
                $latitude = float_to_raw_string(floatval($LATITUDE), 10);
            }

            if ($latitude == '0.0000000000') {
                $latitude = '0';
            }
            if ($longitude == '0.0000000000') {
                $longitude = '0';
            }

            $input = do_template('FORM_SCREEN_INPUT_MAP_POSITION', array('_GUID' => '86d69d152d7bfd125e6216c9ac936cfd', 'REQUIRED' => $required, 'NAME' => $input_name, 'LATITUDE' => $latitude, 'LONGITUDE' => $longitude));
            $lang_string = 'MAP_POSITION_FIELD_field_' . strval($field['id']);
            $test = do_lang($lang_string, null, null, null, null, false);
            if (is_null($test)) {
                $lang_string = 'MAP_POSITION_FIELD';
            }
            return _form_input($input_name, do_lang_tempcode($lang_string), '', $input, $required, false);
        }

        if ($_cf_name == do_lang('LATITUDE_FIELD_NAME')) { // Assumes there is a Longitude field too
            global $LATITUDE;
            $LATITUDE = $actual_value; // Store for when Longitude field is rendered - critical, else won't be entered
            return new Tempcode();
        }

        return form_input_float($_cf_name, $_cf_description, $input_name, (is_null($actual_value) || ($actual_value === '')) ? null : floatval($actual_value), $field['cf_required'] == 1);
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
        require_lang('google_map');

        $id = $field['id'];
        $tmp_name = 'field_' . strval($id);
        $default = STRING_MAGIC_NULL;
        if (get_translated_text($field['cf_name']) == do_lang('LATITUDE_FIELD_NAME')) {
            $default = post_param_string('latitude', STRING_MAGIC_NULL);
        }
        if (get_translated_text($field['cf_name']) == do_lang('LONGITUDE_FIELD_NAME')) {
            $default = post_param_string('longitude', STRING_MAGIC_NULL);
        }

        return post_param_string($tmp_name, $default);
    }
}
