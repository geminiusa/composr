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
class Hook_notification_cns_topic_invite extends Hook_Notification
{
    /**
     * Find a bitmask of settings (email, SMS, etc) a notification code supports for listening on.
     *
     * @param  ID_TEXT $notification_code Notification code
     * @return integer Allowed settings
     */
    public function allowed_settings($notification_code)
    {
        return A__ALL & ~A_INSTANT_PT;
    }

    /**
     * Find the initial setting that members have for a notification code (only applies to the member_could_potentially_enable members).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @return integer Initial setting
     */
    public function get_initial_setting($notification_code, $category = null)
    {
        return A_INSTANT_EMAIL;
    }

    /**
     * Find the setting that members have for a notification code if they have done some action triggering automatic setting (e.g. posted within a topic).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @return integer Automatic setting
     */
    public function get_default_auto_setting($notification_code, $category = null)
    {
        return A_INSTANT_EMAIL;
    }

    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority)
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes()
    {
        $list = array();
        $list['cns_topic_invite'] = array(do_lang('notifications:MESSAGES'), do_lang('cns:NOTIFICATION_TYPE_cns_topic_invite'));
        return $list;
    }

    /**
     * Get a list of members who have enabled this notification (i.e. have permission to AND have chosen to or are defaulted to).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @param  ?array $to_member_ids List of member IDs we are restricting to (null: no restriction). This effectively works as a intersection set operator against those who have enabled.
     * @param  integer $start Start position (for pagination)
     * @param  integer $max Maximum (for pagination)
     * @return array A pair: Map of members to their notification setting, and whether there may be more
     */
    public function list_members_who_have_enabled($notification_code, $category = null, $to_member_ids = null, $start = 0, $max = 300)
    {
        $members = $this->_all_members_who_have_enabled($notification_code, $category, $to_member_ids, $start, $max);
        $members = $this->_all_members_who_have_enabled_with_privilege($members, 'use_pt', $notification_code, $category, $to_member_ids, $start, $max);
        $members = $this->_all_members_who_have_enabled_with_zone_access($members, 'forum', $notification_code, $category, $to_member_ids, $start, $max);

        return $members;
    }
}
