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
 * @package    core_html_abstractions
 */

/**
 * Hook class.
 */
class Hook_addon_registry_core_html_abstractions
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
        return 'Core rendering functionality.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_themes',
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
            'sources/hooks/systems/addon_registry/core_html_abstractions.php',
            'themes/default/templates/JS_REFRESH.tpl',
            'themes/default/templates/META_REFRESH_LINE.tpl',
            'themes/default/templates/ANCHOR.tpl',
            'themes/default/templates/HYPERLINK.tpl',
            'themes/default/templates/HYPERLINK_POPUP_WINDOW.tpl',
            'themes/default/templates/HYPERLINK_TOOLTIP.tpl',
            'themes/default/templates/HYPERLINK_BUTTON.tpl',
            'themes/default/templates/HYPERLINK_EMAIL.tpl',
            'themes/default/templates/DIV.tpl',
            'themes/default/templates/PARAGRAPH.tpl',
            'themes/default/templates/FLOATER.tpl',
            'themes/default/templates/BASIC_HTML_WRAP.tpl',
            'themes/default/templates/STANDALONE_HTML_WRAP.tpl',
            'themes/default/templates/HTML_HEAD.tpl',
            'themes/default/templates/POOR_XHTML_WRAPPER.tpl',
            'themes/default/templates/WITH_WHITESPACE.tpl',
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
            'templates/POOR_XHTML_WRAPPER.tpl' => 'poor_xhtml_wrapper',
            'templates/JS_REFRESH.tpl' => 'js_refresh',
            'templates/ANCHOR.tpl' => 'anchor',
            'templates/STANDALONE_HTML_WRAP.tpl' => 'standalone_html_wrap',
            'templates/META_REFRESH_LINE.tpl' => 'meta_refresh_line',
            'templates/HYPERLINK_POPUP_WINDOW.tpl' => 'hyperlink_popup_window',
            'templates/BASIC_HTML_WRAP.tpl' => 'basic_html_wrap',
            'templates/HTML_HEAD.tpl' => 'basic_html_wrap',
            'templates/FLOATER.tpl' => 'floater',
            'templates/HYPERLINK.tpl' => 'hyperlink',
            'templates/HYPERLINK_BUTTON.tpl' => 'hyperlink_button',
            'templates/HYPERLINK_EMAIL.tpl' => 'hyperlink_email',
            'templates/HYPERLINK_TOOLTIP.tpl' => 'hyperlink_tooltip',
            'templates/PARAGRAPH.tpl' => 'paragraph',
            'templates/DIV.tpl' => 'div',
            'templates/WITH_WHITESPACE.tpl' => 'with_whitespace'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poor_xhtml_wrapper()
    {
        return array(
            lorem_globalise(do_lorem_template('POOR_XHTML_WRAPPER', array(
                'CONTENT' => lorem_phrase_html(),
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
    public function tpl_preview__js_refresh()
    {
        return array(
            lorem_globalise(do_lorem_template('JS_REFRESH', array(
                'FORM_NAME' => lorem_word_html(),
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
    public function tpl_preview__anchor()
    {
        return array(
            lorem_globalise(do_lorem_template('ANCHOR', array(
                'NAME' => lorem_word(),
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
    public function tpl_preview__standalone_html_wrap()
    {
        return array(
            lorem_globalise(do_lorem_template('STANDALONE_HTML_WRAP', array(
                'TITLE' => lorem_phrase(),
                'CONTENT' => lorem_chunk_html(),
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
    public function tpl_preview__meta_refresh_line()
    {
        return array(
            lorem_globalise(do_lorem_template('META_REFRESH_LINE', array(
                'URL' => placeholder_url(),
                'TIME' => placeholder_date_raw(),
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
    public function tpl_preview__hyperlink_popup_window()
    {
        return array(
            lorem_globalise(do_lorem_template('HYPERLINK_POPUP_WINDOW', array(
                'TITLE' => lorem_phrase(),
                'CAPTION' => lorem_phrase(),
                'URL' => placeholder_url(),
                'WIDTH' => placeholder_number(),
                'HEIGHT' => placeholder_number(),
                'REL' => lorem_phrase(),
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
    public function tpl_preview__basic_html_wrap()
    {
        return array(
            lorem_globalise(do_lorem_template('BASIC_HTML_WRAP', array(
                'TITLE' => lorem_phrase(),
                'CONTENT' => lorem_chunk_html(),
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
    public function tpl_preview__floater()
    {
        return array(
            lorem_globalise(do_lorem_template('FLOATER', array(
                'FLOAT' => 'left',
                'CONTENT' => lorem_phrase(),
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
    public function tpl_preview__hyperlink_email()
    {
        return array(
            lorem_globalise(do_lorem_template('HYPERLINK_EMAIL', array(
                'VALUE' => lorem_phrase(),
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
    public function tpl_preview__hyperlink_tooltip()
    {
        return array(
            lorem_globalise(do_lorem_template('HYPERLINK_TOOLTIP', array(
                'TOOLTIP' => lorem_phrase(),
                'CAPTION' => lorem_phrase(),
                'URL' => placeholder_url(),
                'NEW_WINDOW' => lorem_phrase(),
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
    public function tpl_preview__paragraph()
    {
        return array(
            lorem_globalise(do_lorem_template('PARAGRAPH', array(
                'TEXT' => lorem_sentence_html(),
                'CLASS' => lorem_phrase(),
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
    public function tpl_preview__div()
    {
        return array(
            lorem_globalise(do_lorem_template('DIV', array(
                'TEMPCODE' => lorem_phrase(),
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
    public function tpl_preview__with_whitespace()
    {
        return array(
            lorem_globalise(do_lorem_template('WITH_WHITESPACE', array(
                'CONTENT' => lorem_phrase(),
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
    public function tpl_preview__hyperlink()
    {
        return array(
            lorem_globalise(do_lorem_template('HYPERLINK', array(
                'REL' => null,
                'POST_DATA' => null,
                'ACCESSKEY' => null,
                'NEW_WINDOW' => false,
                'TITLE' => lorem_phrase(),
                'URL' => placeholder_url(),
                'CAPTION' => lorem_word(),
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
    public function tpl_preview__hyperlink_button()
    {
        return array(
            lorem_globalise(do_lorem_template('HYPERLINK_BUTTON', array(
                'REL' => null,
                'POST_DATA' => '',
                'ACCESSKEY' => null,
                'NEW_WINDOW' => false,
                'TITLE' => lorem_phrase(),
                'URL' => placeholder_url(),
                'CAPTION' => lorem_word(),
            )), null, '', true)
        );
    }
}
