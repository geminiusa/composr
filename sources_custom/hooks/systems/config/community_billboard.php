<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_config_community_billboard
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'COST_community_billboard',
            'type' => 'integer',
            'category' => 'POINTSTORE',
            'group' => 'COMMUNITY_BILLBOARD_MESSAGE',
            'explanation' => 'CONFIG_OPTION_community_billboard',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'community_billboard',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '200';
    }
}
