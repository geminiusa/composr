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
 * @package    core
 */

/**
 * Clear caching for a particular seo entry.
 *
 * @param  ID_TEXT $type The type of resource (e.g. download)
 * @param  ID_TEXT $id The ID of the resource
 */
function seo_meta_clear_caching($type, $id)
{
    if (function_exists('decache')) {
        decache('side_tag_cloud');
    }

    if (function_exists('persistent_cache_delete')) {
        persistent_cache_delete(array('seo', $type, $id));
    }
}

/**
 * Erase a seo entry... as these shouldn't be left hanging around once content is deleted.
 *
 * @param  ID_TEXT $type The type of resource (e.g. download)
 * @param  ID_TEXT $id The ID of the resource
 * @param  boolean $do_decache Whether to clear caching for this too
 */
function seo_meta_erase_storage($type, $id, $do_decache = true)
{
    $rows = $GLOBALS['SITE_DB']->query_select('seo_meta', array('meta_description'), array('meta_for_type' => $type, 'meta_for_id' => $id), '', 1);
    if (array_key_exists(0, $rows)) {
        delete_lang($rows[0]['meta_description']);
        $GLOBALS['SITE_DB']->query_delete('seo_meta', array('meta_for_type' => $type, 'meta_for_id' => $id), '', 1);
    }

    $rows = $GLOBALS['SITE_DB']->query_select('seo_meta_keywords', array('meta_keyword'), array('meta_for_type' => $type, 'meta_for_id' => $id));
    foreach ($rows as $row) {
        delete_lang($row['meta_keyword']);
    }
    $GLOBALS['SITE_DB']->query_delete('seo_meta_keywords', array('meta_for_type' => $type, 'meta_for_id' => $id));

    if ($do_decache) {
        seo_meta_clear_caching($type, $id);
    }
}

/**
 * Get template fields to insert into a form page, for manipulation of seo fields.
 *
 * @param  ID_TEXT $type The type of resource (e.g. download)
 * @param  ?ID_TEXT $id The ID of the resource (null: adding)
 * @param  boolean $show_header Whether to show a header
 * @return tempcode Form page tempcode fragment
 */
function seo_get_fields($type, $id = null, $show_header = true)
{
    require_code('form_templates');
    if (is_null($id)) {
        list($keywords, $description) = array('', '');
    } else {
        list($keywords, $description) = seo_meta_get_for($type, $id);
    }

    $fields = new Tempcode();
    if ((get_option('enable_seo_fields') != 'no') && ((get_option('enable_seo_fields') != 'only_on_edit') || (!is_null($id)))) {
        if ($show_header) {
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array(
                '_GUID' => '545aefd48d73cf01bdec7226dc6d93fb',
                'SECTION_HIDDEN' => $keywords == '' && $description == '',
                'TITLE' => do_lang_tempcode('SEO'),
                'HELP' => (get_option('show_docs') === '0') ? null : protect_from_escaping(symbol_tempcode('URLISE_LANG', array(do_lang('TUTORIAL_ON_THIS'), get_tutorial_url('tut_seo'), 'tut_seo', '1'))),
            )));
        }
        $fields->attach(form_input_line_multi(do_lang_tempcode('KEYWORDS'), do_lang_tempcode('DESCRIPTION_META_KEYWORDS'), 'meta_keywords[]', array_map('trim', explode(',', preg_replace('#,+#', ',', $keywords))), 0));
        $fields->attach(form_input_line(do_lang_tempcode('META_DESCRIPTION'), do_lang_tempcode('DESCRIPTION_META_DESCRIPTION'), 'meta_description', $description, false));
    }
    return $fields;
}

/**
 * Explictly sets the meta information for the specified resource.
 *
 * @param  ID_TEXT $type The type of resource (e.g. download)
 * @param  ID_TEXT $id The ID of the resource
 * @param  SHORT_TEXT $keywords The keywords to use
 * @param  SHORT_TEXT $description The description to use
 */
function seo_meta_set_for_explicit($type, $id, $keywords, $description)
{
    if ($description == STRING_MAGIC_NULL) {
        return;
    }
    if ($keywords == STRING_MAGIC_NULL) {
        return;
    }

    seo_meta_erase_storage($type, $id, false);

    $description = str_replace("\n", ' ', $description);

    $map_general = array(
        'meta_for_type' => $type,
        'meta_for_id' => $id,
    );

    $map = $map_general;
    $map += insert_lang('meta_description', $description, 2);
    $GLOBALS['SITE_DB']->query_insert('seo_meta', $map);

    foreach (array_unique(explode(',', $keywords)) as $keyword) {
        if (trim($keyword) == '') {
            continue;
        }

        $map = $map_general;
        $map += insert_lang('meta_keyword', $keyword, 2);
        $GLOBALS['SITE_DB']->query_insert('seo_meta_keywords', $map);
    }

    seo_meta_clear_caching($type, $id);
}

/**
 * Automatically extracts meta information from some source data.
 *
 * @param  array $keyword_sources Array of content strings to summarise from
 * @param  SHORT_TEXT $description The description to use
 * @return array A pair: Keyword string generated, Description generated
 */
function _seo_meta_find_data($keyword_sources, $description = '')
{
    // These characters are considered to be word-characters
    require_code('textfiles');
    $word_chars = explode("\n", read_text_file('word_characters', '')); // We use this, as we have no easy multi-language way of detecting if something is a word character in non-latin alphabets (as they don't usually have upper/lower case which would be our detection technique)
    foreach ($word_chars as $i => $word_char) {
        $word_chars[$i] = trim($word_char);
    }
    $common_words = explode("\n", read_text_file('too_common_words', ''));
    foreach ($common_words as $i => $common_word) {
        $common_words[$i] = trim(cms_mb_strtolower($common_word));
    }

    $word_chars_flip = array_flip($word_chars);
    $common_words_flip = array_flip($common_words);

    $min_word_length = 3;

    $keywords = array(); // This will be filled
    $keywords_must_use = array(); // ...and/or this

    $this_word = '';

    $source = mixed();
    foreach ($keyword_sources as $source) { // Look in all our sources
        $must_use = false;
        if (is_array($source)) {
            list($source, $must_use) = $source;
        }

        $source = strip_comcode($source);
        if (cms_mb_strtoupper($source) == $source) {
            $source = cms_mb_strtolower($source); // Don't leave in all caps, as is ugly, and also would break our Proper Noun detection
        }

        $i = 0;
        $len_a = strlen($source);
        $len_b = cms_mb_strlen($source);
        $len = $len_a;
        $unicode = false;
        if ($len_b > $len_a) {
            $len = $len_b;
            $unicode = true;
        }
        $from = 0;
        $in_word = false;
        $word_is_caps = false;
        while ($i < $len) {
            if ($unicode) { // Slower :(
                $at = cms_mb_substr($source, $i, 1);
                $is_word_char = array_key_exists($at, $word_chars_flip) || cms_mb_strtolower($at) != cms_mb_strtoupper($at);

                if ($in_word) {
                    // Exiting word
                    if (($i == $len - 1) || ((!$is_word_char) && ((!$word_is_caps) || ($at != ' ') || (/*continuation of Proper Noun*/
                                    cms_mb_strtolower(cms_mb_substr($source, $i + 1, 1)) == cms_mb_substr($source, $i + 1, 1))))
                    ) {
                        while ((cms_mb_strlen($this_word) != 0) && (cms_mb_substr($this_word, -1) == '\'' || cms_mb_substr($this_word, -1) == '-' || cms_mb_substr($this_word, -1) == '.')) {
                            $this_word = cms_mb_substr($this_word, 0, cms_mb_strlen($this_word) - 1);
                        }
                        if (($i - $from) >= $min_word_length) {
                            if (!array_key_exists(cms_mb_strtolower($this_word), $common_words_flip)) {
                                if (!array_key_exists($this_word, $keywords)) {
                                    $keywords[$this_word] = 0;
                                }
                                if ($must_use) {
                                    $keywords_must_use[$this_word]++;
                                } else {
                                    $keywords[$this_word]++;
                                }
                            }
                        }
                        $in_word = false;
                    } else {
                        $this_word .= $at;
                    }
                } else {
                    // Entering word
                    if (($is_word_char) && ($at != '\'') && ($at != '-') && ($at != '.')/*Special latin cases, cannot start a word with a symbol*/) {
                        $word_is_caps = (cms_mb_strtolower($at) != $at);
                        $from = $i;
                        $in_word = true;
                        $this_word = $at;
                    }
                }
            } else {
                $at = $source[$i];
                $is_word_char = array_key_exists($at, $word_chars_flip);

                if ($in_word) {
                    // Exiting word
                    if (($i == $len - 1) || ((!$is_word_char) && ((!$word_is_caps) || ($at != ' ') || (/*continuation of Proper Noun*/
                                    strtolower(substr($source, $i + 1, 1)) == substr($source, $i + 1, 1))))
                    ) {
                        $this_word = substr($source, $from, $i - $from);
                        while ((strlen($this_word) != 0) && (substr($this_word, -1) == '\'' || substr($this_word, -1) == '-' || substr($this_word, -1) == '.')) {
                            $this_word = substr($this_word, 0, strlen($this_word) - 1);
                        }
                        if (($i - $from) >= $min_word_length) {
                            if (!array_key_exists($this_word, $common_words_flip)) {
                                if (!array_key_exists($this_word, $keywords)) {
                                    $keywords[$this_word] = 0;
                                }
                                if ($must_use) {
                                    $keywords_must_use[$this_word]++;
                                } else {
                                    $keywords[$this_word]++;
                                }
                            }
                        }
                        $in_word = false;
                    }
                } else {
                    // Entering word
                    if ($is_word_char) {
                        $word_is_caps = (strtolower($at) != $at);
                        $from = $i;
                        $in_word = true;
                    }
                }
            }
            $i++;
        }
    }

    arsort($keywords);

    $imp = '';
    foreach (array_keys($keywords_must_use) as $keyword) {
        if ($imp != '') {
            $imp .= ',';
        }
        $imp .= $keyword;
    }
    foreach (array_keys($keywords) as $i => $keyword) {
        if ($imp != '') {
            $imp .= ',';
        }
        $imp .= $keyword;
        if ($i == 10) {
            break;
        }
    }

    require_code('xhtml');
    $description = strip_comcode($description, true);
    $description = trim(preg_replace('#\s+---+\s+#', ' ', $description));

    if (strlen($description) > 160) {
        $description = substr($description, 0, 157) . '...';
    }

    return array($imp, $description);
}

/**
 * Sets the meta information for the specified resource, by auto-summarisation from the given parameters.
 *
 * @param  ID_TEXT $type The type of resource (e.g. download)
 * @param  ID_TEXT $id The ID of the resource
 * @param  array $keyword_sources Array of content strings to summarise from
 * @param  SHORT_TEXT $description The description to use
 * @return SHORT_TEXT Keyword string generated (it's also saved in the DB, so usually you won't want to collect this)
 */
function seo_meta_set_for_implicit($type, $id, $keyword_sources, $description)
{
    if ((!is_null(post_param_string('meta_keywords', null))) && ((post_param_string('meta_keywords') != '') || (post_param_string('meta_description') != ''))) {
        seo_meta_set_for_explicit($type, $id, post_param_string('meta_keywords'), post_param_string('meta_description'));
        return '';
    }

    if (get_option('automatic_meta_extraction') == '0') {
        return '';
    }

    list($imp, $description) = _seo_meta_find_data($keyword_sources, $description);

    seo_meta_set_for_explicit($type, $id, $imp, $description);

    if (function_exists('decache')) {
        decache('side_tag_cloud');
    }

    return $imp;
}
