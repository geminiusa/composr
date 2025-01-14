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
 * @package    cns_reported_posts
 */

/**
 * Hook class.
 */
class Hook_config_reported_posts_forum
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'REPORTED_POSTS_FORUM',
            'type' => 'forum',
            'category' => 'FORUMS',
            'group' => 'GENERAL',
            'explanation' => 'CONFIG_OPTION_reported_posts_forum',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,

            'addon' => 'cns_reported_posts',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return do_lang('cns_config:REPORTED_POSTS_FORUM', '', '', '', get_site_default_lang());
    }
}
