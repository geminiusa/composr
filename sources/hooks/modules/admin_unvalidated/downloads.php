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
 * @package    downloads
 */

/**
 * Hook class.
 */
class Hook_unvalidated_downloads
{
    /**
     * Find details on the unvalidated hook.
     *
     * @return ?array Map of hook info (null: hook is disabled).
     */
    public function info()
    {
        if (!module_installed('downloads')) {
            return null;
        }

        require_lang('downloads');

        $info = array();
        $info['db_table'] = 'download_downloads';
        $info['db_identifier'] = 'id';
        $info['db_validated'] = 'validated';
        $info['db_title'] = 'name';
        $info['db_title_dereference'] = true;
        $info['db_add_date'] = 'add_date';
        $info['db_edit_date'] = 'edit_date';
        $info['edit_module'] = 'cms_downloads';
        $info['edit_type'] = '_edit';
        $info['edit_identifier'] = 'id';
        $info['title'] = do_lang_tempcode('SECTION_DOWNLOADS');

        return $info;
    }
}
