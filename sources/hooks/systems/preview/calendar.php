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
 * @package    calendar
 */

/**
 * Hook class.
 */
class Hook_preview_calendar
{
    /**
     * Find whether this preview hook applies.
     *
     * @return array Triplet: Whether it applies, the attachment ID type, whether the forum DB is used [optional]
     */
    public function applies()
    {
        require_code('uploads');
        $applies = (get_param_string('page', '') == 'cms_calendar') && ((get_param_string('type', '') == 'add') || (get_param_string('type', '') == '_edit')) && ((is_plupload()) || (count($_FILES) != 0));
        return array($applies, 'calendar', false);
    }
}
