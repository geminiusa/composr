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
 * @package    ecommerce
 */

/**
 * Hook class.
 */
class Hook_ecommerce_tax
{
    /**
     * Function for administrators to pick an identifier (only used by admins, usually the identifier would be picked via some other means in the wider Composr codebase).
     *
     * @param  ID_TEXT $type_code Product codename.
     * @return ?tempcode Input field in standard Tempcode format for fields (null: no identifier).
     */
    public function get_identifier_manual_field_inputter($type_code)
    {
        return null;
    }

    /**
     * Get the products handled by this eCommerce hook.
     *
     * IMPORTANT NOTE TO PROGRAMMERS: This function may depend only on the database, and not on get_member() or any GET/POST values.
     *  Such dependencies will break IPN, which works via a Guest and no dependable environment variables. It would also break manual transactions from the Admin Zone.
     *
     * @param  boolean $site_lang Whether to make sure the language for item_name is the site default language (crucial for when we read/go to third-party sales systems and use the item_name as a key).
     * @return array A map of product name to list of product details.
     */
    public function get_products($site_lang = false)
    {
        $products = array(
            'TAX' => array(PRODUCT_OTHER, '?', '', array(), do_lang('ecommerce:CUSTOM_PRODUCT_TAX', null, null, null, $site_lang ? get_site_default_lang() : user_lang())),
        );
        return $products;
    }
}
