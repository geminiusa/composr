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
 * @package    core_forum_drivers
 */

require_code('forum/shared/wbb');

/**
 * Forum driver class.
 *
 * @package    core_forum_drivers
 */
class Forum_driver_wbb22 extends forum_driver_wbb_shared
{
    /**
     * Get the ID of the usergroup that is for guests.
     *
     * @return GROUP The guest usergroup
     */
    protected function _get_guest_group()
    {
        $guest_group = $this->connection->query_select_value_if_there('groups', 'groupid', array('title' => 'Guests'));
        if (is_null($guest_group)) {
            $guest_group = 5;
        }
        return $guest_group;
    }

    /**
     * From a member row, get the member's primary usergroup.
     *
     * @param  array $r The profile-row
     * @return GROUP The member's primary usergroup
     */
    public function mrow_group($r)
    {
        return $this->connection->query_select_value('user2groups', 'groupid', array('userid' => $r['userid']));
    }

    /**
     * Get an array of members who are in at least one of the given array of usergroups.
     *
     * @param  array $groups The array of usergroups
     * @param  ?integer $max Return up to this many entries for primary members and this many entries for secondary members (null: no limit, only use no limit if querying very restricted usergroups!)
     * @param  integer $start Return primary members after this offset and secondary members after this offset
     * @return ?array The array of members (null: no members)
     */
    public function member_group_query($groups, $max = null, $start = 0)
    {
        $_groups = '';
        foreach ($groups as $group) {
            if ($_groups != '') {
                $_groups .= ' OR ';
            }
            $_groups .= 'groupid=' . strval($group);
        }
        return $this->connection->query('SELECT * FROM ' . $this->connection->get_table_prefix() . 'user2groups g LEFT JOIN ' . $this->connection->get_table_prefix() . 'users u ON g.userid=u.userid WHERE ' . $_groups . ' ORDER BY groupid ASC', $max, $start, false, true);
    }

    /**
     * Find out if the given member ID is banned.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is banned
     */
    public function is_banned($member)
    {
        return false;
    }

    /**
     * Find a list of all forum skins (aka themes).
     *
     * @return array The list of skins
     */
    public function get_skin_list()
    {
        $table = 'styles';
        $codename = 'stylename';

        $rows = $this->connection->query_select($table, array($codename));
        return collapse_1d_complexity($codename, $rows);
    }

    /**
     * Try to find the theme that the logged-in/guest member is using, and map it to a Composr theme.
     * The themes/map.ini file functions to provide this mapping between forum themes, and Composr themes, and has a slightly different meaning for different forum drivers. For example, some drivers map the forum themes theme directory to the Composr theme name, whilst others made the humanly readeable name.
     *
     * @param  boolean $skip_member_specific Whether to avoid member-specific lookup
     * @return ID_TEXT The theme
     */
    public function _get_theme($skip_member_specific = false)
    {
        // Cache
        $def = '';

        // Load in remapper
        require_code('files');
        $map = file_exists(get_file_base() . '/themes/map.ini') ? better_parse_ini_file(get_file_base() . '/themes/map.ini') : array();

        if (!$skip_member_specific) {
            // Work out
            $member = get_member();
            if ($member > 0) {
                $skin = $this->get_member_row_field($member, 'styleid');
            } else {
                $skin = 0;
            }
            if ($skin > 0) { // User has a custom theme
                $bb = $this->connection->query_select_value('styles', 'stylename', array('styleid' => $skin));
                $def = !is_null($map[$bb]) ? $map[$bb] : $bb;
            }
        }

        // Look for a skin according to our site name (we bother with this instead of 'default' because Composr itself likes to never choose a theme when forum-theme integration is on: all forum [via map] or all Composr seems cleaner, although it is complex)
        if ((!(strlen($def) > 0)) || (!file_exists(get_custom_file_base() . '/themes/' . $def))) {
            $bb = $this->connection->query_select_value_if_there('styles', 'stylename', array('stylename' => get_site_name()));
            if (!is_null($bb)) {
                $def = !is_null($map[$bb]) ? $map[$bb] : $bb;
            }
        }

        // Default then!
        if ((!(strlen($def) > 0)) || (!file_exists(get_custom_file_base() . '/themes/' . $def))) {
            $def = array_key_exists('default', $map) ? $map['default'] : 'default';
        }

        return $def;
    }

    /**
     * Find if the specified member ID is marked as staff or not.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is staff
     */
    protected function _is_staff($member)
    {
        $rows = $this->connection->query_select('user2groups', array('groupid'), array('userid' => $member));
        foreach ($rows as $g) {
            $usergroup = $g['groupid'];
            if ($this->connection->query_select_value_if_there('groups', 'securitylevel', array('groupid' => $usergroup)) >= 2) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find if the specified member ID is marked as a super admin or not.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is a super admin
     */
    protected function _is_super_admin($member)
    {
        $rows = $this->connection->query_select('user2groups', array('groupid'), array('userid' => $member));
        foreach ($rows as $g) {
            $usergroup = $g['groupid'];
            if ($this->connection->query_select_value_if_there('groups', 'securitylevel', array('groupid' => $usergroup)) >= 3) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the IDs of the admin usergroups.
     *
     * @return array The admin usergroup IDs
     */
    protected function _get_super_admin_groups()
    {
        return collapse_1d_complexity('groupid', $this->connection->query('SELECT groupid FROM ' . $this->connection->get_table_prefix() . 'groups WHERE securitylevel>=4'));
    }

    /**
     * Get the IDs of the moderator usergroups.
     * It should not be assumed that a member only has one usergroup - this depends upon the forum the driver works for. It also does not take the staff site filter into account.
     *
     * @return array The moderator usergroup IDs
     */
    protected function _get_moderator_groups()
    {
        return collapse_1d_complexity('groupid', $this->connection->query('groups', array('groupid'), array('securitylevel' => 3)));
    }

    /**
     * Get the forum usergroup list.
     *
     * @return array The usergroup list
     */
    protected function _get_usergroup_list()
    {
        return collapse_2d_complexity('groupid', 'title', $this->connection->query_select('groups', array('groupid', 'title')));
    }

    /**
     * Get the forum usergroup relating to the specified member ID.
     *
     * @param  MEMBER $member The member ID
     * @return array The array of forum usergroups
     */
    protected function _get_members_groups($member)
    {
        if ($member == $this->get_guest_id()) {
            return array($this->get_member_row_field($member, 'groupid'));
        }

        $groups = $this->connection->query_select('user2groups', array('groupid'), array('userid' => $member));
        $out = array();
        foreach ($groups as $group) {
            $out[] = $group['groupid'];
        }
        $p = $this->get_member_row_field($member, 'groupid');
        if (!in_array($p, $out)) {
            $out[] = $p;
        }

        return $out;
    }
}
