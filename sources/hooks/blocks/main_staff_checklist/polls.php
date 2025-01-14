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
 * @package    polls
 */

/**
 * Hook class.
 */
class Hook_checklist_polls
{
    /**
     * Find items to include on the staff checklist.
     *
     * @return array An array of tuples: The task row to show, the number of seconds until it is due (or NULL if not on a timer), the number of things to sort out (or NULL if not on a queue), The name of the config option that controls the schedule (or NULL if no option).
     */
    public function run()
    {
        if (!addon_installed('polls')) {
            return array();
        }

        if (get_option('poll_update_time') == '') {
            return array();
        }

        require_lang('polls');

        $date = $GLOBALS['SITE_DB']->query_select_value_if_there('poll', 'date_and_time', array('is_current' => 1));

        $limit_hours = intval(get_option('poll_update_time'));

        $seconds_ago = mixed();
        if (!is_null($date)) {
            $status = ($seconds_ago > $limit_hours * 60 * 60) ? 0 : 1;
        } else {
            $status = 0;
        }

        $_status = ($status == 0) ? do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0') : do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1');

        require_code('config2');
        $config_url = config_option_url('poll_update_time');

        $url = build_url(array('page' => 'cms_polls', 'type' => 'edit'), get_module_zone('cms_polls'));
        $num_queue = $this->get_num_poll_queue();
        list($info, $seconds_due_in) = staff_checklist_time_ago_and_due($seconds_ago, $limit_hours);
        $info->attach(do_lang_tempcode('NUM_QUEUE', escape_html(integer_format($num_queue))));
        $tpl = do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array('_GUID' => '5d709aa8a09bbf3e46aefa7fe7e02660', 'CONFIG_URL' => $config_url, 'URL' => $url, 'STATUS' => $_status, 'TASK' => do_lang_tempcode('PRIVILEGE_choose_poll'), 'INFO' => $info));
        return array(array($tpl, $seconds_due_in, null, 'poll_update_time'));
    }

    /**
     * Get the number of polls in the queue.
     *
     * @return integer Number in queue
     */
    public function get_num_poll_queue()
    {
        $c = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'poll WHERE votes1+votes2+votes3+votes4+votes5+votes6+votes7+votes8+votes9+votes10=0 AND is_current=0');
        if (is_null($c)) {
            return 0;
        }
        return $c;
    }
}
