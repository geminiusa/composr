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
 * @package    banners
 */

/**
 * Hook class.
 */
class Hook_preview_banner
{
    /**
     * Find whether this preview hook applies.
     *
     * @return array Triplet: Whether it applies, the attachment ID type, whether the forum DB is used [optional]
     */
    public function applies()
    {
        $applies = (get_param_string('page', '') == 'cms_banners') && ((get_param_string('type', '') == 'add') || (get_param_string('type', '') == '_edit'));
        return array($applies, null, false);
    }

    /**
     * Run function for preview hooks.
     *
     * @return array A pair: The preview, the updated post Comcode
     */
    public function run()
    {
        require_code('uploads');
        require_lang('banners');

        // Check according to banner type
        $title_text = post_param_string('title_text', '');
        $direct_code = post_param_string('direct_code', '');
        $url_param_name = 'image_url';
        $file_param_name = 'file';
        require_code('uploads');
        $is_upload = is_plupload() || (array_key_exists($file_param_name, $_FILES)) && (array_key_exists('tmp_name', $_FILES[$file_param_name]) && (is_uploaded_file($_FILES[$file_param_name]['tmp_name'])));
        $_banner_type_rows = $GLOBALS['SITE_DB']->query_select('banner_types', array('*'), array('id' => post_param_string('b_type')), '', 1);
        if (!array_key_exists(0, $_banner_type_rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $banner_type_row = $_banner_type_rows[0];
        if ($banner_type_row['t_is_textual'] == 0) {
            if ($direct_code == '') {
                $urls = get_url($url_param_name, $file_param_name, 'uploads/banners', 0, $is_upload ? CMS_UPLOAD_IMAGE : CMS_UPLOAD_ANYTHING);
                $img_url = fixup_protocolless_urls($urls[0]);
                if ($img_url == '') {
                    warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN_UPLOAD_BANNERS'));
                }
            } else {
                $img_url = '';
            }
        } else {
            $img_url = '';
            if ($title_text == '') {
                warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN_BANNERS'));
            }

            if (strlen($title_text) > $banner_type_row['t_max_file_size']) {
                warn_exit(do_lang_tempcode('BANNER_TOO_LARGE_2', escape_html(integer_format(strlen($title_text))), escape_html(integer_format($banner_type_row['t_max_file_size']))));
            }
        }

        require_code('banners');
        $preview = show_banner(post_param_string('name'), post_param_string('title_text', ''), comcode_to_tempcode(post_param_string('caption')), post_param_string('direct_code', ''), $img_url, '', post_param_string('site_url'), post_param_string('b_type'), get_member());

        return array($preview, null);
    }
}
