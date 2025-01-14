<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    image_slider
 */

/**
 * Hook class.
 */
class Hook_addon_registry_image_slider
{
    /**
     * Get a list of file permissions to set
     *
     * @return array File permissions to set
     */
    public function get_chmod_array()
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Graphical';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array('Thiago Silva Ferreira', 'Robert Penner');
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'MIT licence / BSD licence';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'A block to show a slider from some gallery/galleries.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'galleries',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/image_slider.php',
            'sources_custom/blocks/main_image_slider.php',
            'themes/default/css_custom/skitter.css',
            'themes/default/images_custom/skitter/ajax-loader.gif',
            'themes/default/images_custom/skitter/index.html',
            'themes/default/images_custom/skitter/sprite-clean.png',
            'themes/default/images_custom/skitter/sprite-default.png',
            'themes/default/images_custom/skitter/sprite-minimalist.png',
            'themes/default/images_custom/skitter/sprite-round.png',
            'themes/default/images_custom/skitter/sprite-square.png',
            'themes/default/templates_custom/BLOCK_MAIN_IMAGE_SLIDER.tpl',
            'themes/default/javascript_custom/skitter.js',
            'lang_custom/EN/image_slider.ini',
        );
    }
}
