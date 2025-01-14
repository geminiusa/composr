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
 * @package    commandr
 */

/**
 * Hook class.
 */
class Hook_commandr_notification_commandrchat
{
    /**
     * Run function for Commandr notification hooks.
     *
     * @param  ?integer $timestamp The "current" time on which to base queries (null: now)
     * @return ~array Array of section, type and message responses (false: nothing)
     */
    public function run($timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        }
        $messages = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'commandrchat WHERE c_incoming=1 AND c_timestamp>=' . strval($timestamp));

        require_code('comcode_compiler');

        if (count($messages) > 0) {
            $_messages = array();
            foreach ($messages as $message) {
                $_messages[apply_emoticons($message['c_message'])] = $message['c_url'];
            }
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'commandrchat WHERE c_timestamp>=' . strval($timestamp));

            return array(do_lang('COMMANDR'), do_lang('_NEW_COMMANDRCHAT_MESSAGES'), do_template('COMMANDR_COMMANDRCHAT_NOTIFICATION', array('_GUID' => 'f6a3a17ace63675690319f6a7540c86a', 'MESSAGE_COUNT' => integer_format(count($messages)), 'MESSAGES' => $_messages)));
        } else {
            return false;
        }
    }
}
