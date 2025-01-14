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
 * @package    core_configuration
 */

/**
 * Hook class.
 */
class Hook_config_valid_types
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'FILE_TYPES',
            'type' => 'line',
            'category' => 'SECURITY',
            'group' => 'UPLOADED_FILES',
            'explanation' => 'CONFIG_OPTION_valid_types',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,

            'addon' => 'core_configuration',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return 'swf,sql,odg,odp,odt,ods,pdf,pgp,dot,doc,ppt,csv,xls,docx,pptx,xlsx,pub,txt,log,psd,tga,tif,gif,png,ico,bmp,jpg,jpeg,flv,avi,mov,3gp,mpg,mpeg,mp4,m4v,webm,asf,wmv,zip,tar,rar,gz,wav,mp3,ogg,ogv,torrent,php,css,tpl,ini,eml,patch,diff,iso,dmg';
    }
}
