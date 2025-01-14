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
 * @package    filedump
 */

/**
 * Hook class.
 */
class Hook_choose_filedump_file
{
    /**
     * Run function for ajax-tree hooks. Generates XML for a tree list, which is interpreted by JavaScript and expanded on-demand (via new calls).
     *
     * @param  ?ID_TEXT $id The ID to do under (null: root)
     * @param  array $options Options being passed through
     * @param  ?ID_TEXT $default The ID to select by default (null: none)
     * @return string XML in the special category,entry format
     */
    public function run($id, $options, $default = null)
    {
        if ($id === null) {
            $id = '';
        }

        require_code('files2');
        require_code('images');
        $fullpath = get_custom_file_base() . '/uploads/filedump';
        if ($id != '') {
            $fullpath .= '/' . $id;
        }

        $levels_to_expand = array_key_exists('levels_to_expand', $options) ? ($options['levels_to_expand']) : intval(get_value('levels_to_expand__' . substr(get_class($this), 5), null, true));
        $options['levels_to_expand'] = max(0, $levels_to_expand - 1);

        $images_only = array_key_exists('images_only', $options) ? ($options['images_only'] == '1') : false;

        $folder = ((isset($options['folder'])) && ($options['folder'])); // We want to select folders, not files

        $out = '';

        $out .= '<options>' . serialize($options) . '</options>';

        if ((has_actual_page_access(null, 'filedump')) && (file_exists($fullpath))) {
            $files = get_directory_contents($fullpath, '', false, false);
            natsort($files);
            foreach ($files as $f) {
                if ($images_only && !is_image($f)) {
                    continue;
                }

                $description = $GLOBALS['SITE_DB']->query_select_value_if_there('filedump', 'description', array('name' => basename($f), 'path' => $id . '/'));

                $entry_id = 'uploads/filedump/' . (($id == '') ? '' : (rawurlencode($id) . '/')) . rawurlencode($f);

                if (is_dir($fullpath . '/' . $f)) {
                    $has_children = (count(get_directory_contents($fullpath . '/' . $f, '', false, false)) > 0);

                    if ($has_children) {
                        $out .= '<category id="' . xmlentities((($id == '') ? '' : ($id . '/')) . $f) . '" title="' . xmlentities($f) . '" has_children="' . ($has_children ? 'true' : 'false') . '" selectable="' . ($folder ? 'true' : 'false') . '"></category>';

                        if ($levels_to_expand > 0) {
                            $out .= '<expand>' . xmlentities((($id == '') ? '' : ($id . '/')) . $f) . '</expand>';
                        }
                    }
                } elseif (!$folder) {
                    if ((!isset($options['only_images'])) || (!$options['only_images']) || (is_image($f))) {
                        if ((is_null($description)) || (get_translated_text($description) == '')) {
                            $_description = '';
                            if (is_image($f)) {
                                $url = get_custom_base_url() . '/uploads/filedump/' . (($id == '') ? '' : ($id . '/')) . $f;
                                $_description = static_evaluate_tempcode(do_image_thumb($url, '', true, false, null, null, true));
                            }
                        } else {
                            $_description = escape_html(get_translated_text($description));
                        }
                        $out .= '<entry id="' . xmlentities($entry_id) . '" title="' . xmlentities($f) . '" description_html="' . xmlentities($_description) . '" selectable="true"></entry>';
                    }
                }
            }

            // Mark parent cats for pre-expansion
            if ((!is_null($default)) && ($default != '')) {
                $cat = '';
                foreach (explode('/', $default) as $_cat) {
                    if ($_cat != '') {
                        $cat .= '/';
                        $cat .= $_cat;
                    }
                    $out .= '<expand>' . $cat . '</expand>';
                }
            }
        }

        return '<result>' . $out . '</result>';
    }

    /**
     * Generate a simple selection list for the ajax-tree hook. Returns a normal <select> style <option>-list, for fallback purposes
     *
     * @param  ?ID_TEXT $id The ID to do under (null: root) - not always supported
     * @param  array $options Options being passed through
     * @param  ?ID_TEXT $it The ID to select by default (null: none)
     * @return tempcode The nice list
     */
    public function simple($id, $options, $it = null)
    {
        $out = '';

        if (has_actual_page_access(null, 'filedump')) {
            require_code('images');
            require_code('files2');
            $fullpath = get_custom_file_base() . '/uploads/filedump';
            if ($id != '') {
                $fullpath .= '/' . $id;
            }
            $tree = get_directory_contents($fullpath, '');

            foreach ($tree as $f) {
                if ((!isset($options['only_images'])) || (!$options['only_images']) || (is_image($f))) {
                    $rel = preg_replace('#^' . preg_quote($id, '#') . '/#', '', $f);
                    $out .= '<option value="' . escape_html('uploads/filedump/' . $f) . '"' . (($it === $f) ? ' selected="selected"' : '') . '>' . escape_html($rel) . '</option>' . "\n";
                }
            }
        }

        return make_string_tempcode($out);
    }
}
