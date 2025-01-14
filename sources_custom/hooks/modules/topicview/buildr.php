<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_topicview_buildr
{
    /**
     * Execute the module.
     *
     * @param  MEMBER $member_id The ID of the member we are getting detail hooks for
     * @return ?tempcode Results (null: no action)
     */
    public function run($member_id)
    {
        global $OCWORLD_MEMBER_CACHE;
        if (!isset($OCWORLD_MEMBER_CACHE)) {
            $OCWORLD_MEMBER_CACHE = array();
        }
        if (array_key_exists($member_id, $OCWORLD_MEMBER_CACHE)) {
            return $OCWORLD_MEMBER_CACHE[$member_id];
        }

        $zone = get_page_zone('buildr', false);
        if (is_null($zone)) {
            return null;
        }
        if (!has_zone_access(get_member(), $zone)) {
            return null;
        }

        $rows = $GLOBALS['SITE_DB']->query_select('w_members m LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_realms r ON m.location_realm=r.id', array('*'), array('m.id' => $member_id), '', 1, 0, true);
        if ((!is_null($rows)) && (array_key_exists(0, $rows))) {
            $row = $rows[0];
            $room = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_x' => $row['location_x'], 'location_y' => $row['location_y'], 'location_realm' => $row['location_realm']));
            if (is_null($room)) {
                return null;
            }

            require_lang('buildr');
            $a = do_template('CNS_MEMBER_BOX_CUSTOM_FIELD', array('_GUID' => '3d36d5ae8bcb66d59a0676200571fb1a', 'NAME' => do_lang_tempcode('_W_ROOM'), 'VALUE' => do_lang_tempcode('W_ROOM_COORD', escape_html($room), strval($row['location_realm']), array(strval($row['location_x']), strval($row['location_y'])))));
            $b = do_template('CNS_MEMBER_BOX_CUSTOM_FIELD', array('_GUID' => '72c62771f7796d69d1f1a616c2591206', 'NAME' => do_lang_tempcode('_W_REALM'), 'VALUE' => $row['name']));
            $a->attach($b);

            $OCWORLD_MEMBER_CACHE[$member_id] = $a;

            return $a;
        }
        return null;
    }
}
