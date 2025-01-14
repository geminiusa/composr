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
 * @package    chat
 */

require_code('resource_fs');

/**
 * Hook class.
 */
class Hook_commandr_fs_chat extends Resource_fs_base
{
    public $file_resource_type = 'chat';

    /**
     * Standard commandr_fs function for seeing how many resources are. Useful for determining whether to do a full rebuild.
     *
     * @param  ID_TEXT $resource_type The resource type
     * @return integer How many resources there are
     */
    public function get_resources_count($resource_type)
    {
        return $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'COUNT(*)');
    }

    /**
     * Standard commandr_fs function for searching for a resource by label.
     *
     * @param  ID_TEXT $resource_type The resource type
     * @param  LONG_TEXT $label The resource label
     * @return array A list of resource IDs
     */
    public function find_resource_by_label($resource_type, $label)
    {
        $_ret = $GLOBALS['SITE_DB']->query_select('chat_rooms', array('id'), array('room_name' => $label));
        $ret = array();
        foreach ($_ret as $r) {
            $ret[] = strval($r['id']);
        }
        return $ret;
    }

    /**
     * Standard commandr_fs introspection function.
     *
     * @return array The properties available for the resource type
     */
    protected function _enumerate_file_properties()
    {
        return array(
            'welcome_message' => 'LONG_TRANS',
            'room_owner' => 'member',
            'allow' => 'SHORT_TEXT',
            'allow_groups' => 'SHORT_TEXT',
            'disallow' => 'SHORT_TEXT',
            'disallow_groups' => 'SHORT_TEXT',
            'room_lang' => 'LANGUAGE_NAME',
            'is_im' => 'BINARY',
        );
    }

    /**
     * Standard commandr_fs date fetch function for resource-fs hooks. Defined when getting an edit date is not easy.
     *
     * @param  array $row Resource row (not full, but does contain the ID)
     * @return ?TIME The edit date or add date, whichever is higher (null: could not find one)
     */
    protected function _get_file_edit_date($row)
    {
        $query = 'SELECT MAX(date_and_time) FROM ' . get_table_prefix() . 'adminlogs WHERE ' . db_string_equal_to('param_a', strval($row['id'])) . ' AND  (' . db_string_equal_to('the_type', 'ADD_CHATROOM') . ' OR ' . db_string_equal_to('the_type', 'EDIT_CHATROOM') . ')';
        return $GLOBALS['SITE_DB']->query_value_if_there($query);
    }

    /**
     * Standard commandr_fs add function for resource-fs hooks. Adds some resource with the given label and properties.
     *
     * @param  LONG_TEXT $filename Filename OR Resource label
     * @param  string $path The path (blank: root / not applicable)
     * @param  array $properties Properties (may be empty, properties given are open to interpretation by the hook but generally correspond to database fields)
     * @return ~ID_TEXT The resource ID (false: error, could not create via these properties / here)
     */
    public function file_add($filename, $path, $properties)
    {
        list($properties, $label) = $this->_file_magic_filter($filename, $path, $properties);

        require_code('chat2');

        $welcome = $this->_default_property_str($properties, 'welcome_message');
        $room_owner = $this->_default_property_int_null($properties, 'room_owner');
        $allow2 = $this->_default_property_str($properties, 'allow');
        $allow2_groups = $this->_default_property_str($properties, 'allow_groups');
        $disallow2 = $this->_default_property_str($properties, 'disallow');
        $disallow2_groups = $this->_default_property_str($properties, 'disallow_groups');
        $roomlang = $this->_default_property_str($properties, 'room_lang');
        if ($roomlang == '') {
            $roomlang = get_site_default_lang();
        }
        $is_im = $this->_default_property_int($properties, 'is_im');

        $id = add_chatroom($welcome, $label, $room_owner, $allow2, $allow2_groups, $disallow2, $disallow2_groups, $roomlang, $is_im);
        return strval($id);
    }

    /**
     * Standard commandr_fs load function for resource-fs hooks. Finds the properties for some resource.
     *
     * @param  SHORT_TEXT $filename Filename
     * @param  string $path The path (blank: root / not applicable). It may be a wildcarded path, as the path is used for content-type identification only. Filenames are globally unique across a hook; you can calculate the path using ->search.
     * @return ~array Details of the resource (false: error)
     */
    public function file_load($filename, $path)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);

        $rows = $GLOBALS['SITE_DB']->query_select('chat_rooms', array('*'), array('id' => intval($resource_id)), '', 1);
        if (!array_key_exists(0, $rows)) {
            return false;
        }
        $row = $rows[0];

        return array(
            'label' => $row['room_name'],
            'welcome_message' => $row['c_welcome'],
            'room_owner' => $row['room_owner'],
            'allow' => $row['allow_list'],
            'allow_groups' => $row['allow_list_groups'],
            'disallow' => $row['disallow_list'],
            'disallow_groups' => $row['disallow_list_groups'],
            'room_lang' => $row['room_language'],
            'is_im' => $row['is_im'],
        );
    }

    /**
     * Standard commandr_fs edit function for resource-fs hooks. Edits the resource to the given properties.
     *
     * @param  ID_TEXT $filename The filename
     * @param  string $path The path (blank: root / not applicable)
     * @param  array $properties Properties (may be empty, properties given are open to interpretation by the hook but generally correspond to database fields)
     * @return ~ID_TEXT The resource ID (false: error, could not create via these properties / here)
     */
    public function file_edit($filename, $path, $properties)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);
        list($properties,) = $this->_file_magic_filter($filename, $path, $properties);

        require_code('chat2');

        $label = $this->_default_property_str($properties, 'label');
        $welcome = $this->_default_property_str($properties, 'welcome_message');
        $room_owner = $this->_default_property_int_null($properties, 'room_owner');
        $allow2 = $this->_default_property_str($properties, 'allow');
        $allow2_groups = $this->_default_property_str($properties, 'allow_groups');
        $disallow2 = $this->_default_property_str($properties, 'disallow');
        $disallow2_groups = $this->_default_property_str($properties, 'disallow_groups');
        $roomlang = $this->_default_property_str($properties, 'room_lang');
        if ($roomlang == '') {
            $roomlang = get_site_default_lang();
        }
        $is_im = $this->_default_property_int($properties, 'is_im');

        edit_chatroom(intval($resource_id), $welcome, $label, $room_owner, $allow2, $allow2_groups, $disallow2, $disallow2_groups, $roomlang);

        return $resource_id;
    }

    /**
     * Standard commandr_fs delete function for resource-fs hooks. Deletes the resource.
     *
     * @param  ID_TEXT $filename The filename
     * @param  string $path The path (blank: root / not applicable)
     * @return boolean Success status
     */
    public function file_delete($filename, $path)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);

        require_code('chat2');
        delete_chatroom(intval($resource_id));

        return true;
    }
}
