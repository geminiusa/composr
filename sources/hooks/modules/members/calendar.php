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
class Hook_members_calendar
{
    /**
     * Find member-related links to inject.
     *
     * @param  MEMBER $member_id The ID of the member we are getting link hooks for
     * @return array List of lists of tuples for results (by link section). Each tuple is: type,title,url
     */
    public function run($member_id)
    {
        if (!addon_installed('calendar')) {
            return array();
        }

        //if (!has_privilege(get_member(),'assume_any_member')) return array();  Now will have separate permission filtering
        if (!has_actual_page_access(get_member(), 'calendar', get_module_zone('calendar'))) {
            return array();
        }

        require_lang('calendar');
        return array(array('content', do_lang_tempcode('CALENDAR'), build_url(array('page' => 'calendar', 'type' => 'browse', 'member_id' => $member_id, 'private' => 1), get_module_zone('calendar')), 'menu/rich_content/calendar'));
    }
}
