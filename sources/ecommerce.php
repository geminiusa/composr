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
 * Standard code module initialisation function.
 */
function init__ecommerce()
{
    if (!defined('PRODUCT_PURCHASE_WIZARD')) {
        define('PRODUCT_PURCHASE_WIZARD', 0);
        define('PRODUCT_INVOICE', 1);
        define('PRODUCT_SUBSCRIPTION', 2);
        define('PRODUCT_OTHER', 3);
        define('PRODUCT_CATALOGUE', 4);
        define('PRODUCT_ORDERS', 5);

        define('ECOMMERCE_PRODUCT_AVAILABLE', 0);
        define('ECOMMERCE_PRODUCT_NO_GUESTS', 1); // Only used if current user really is a Guest
        define('ECOMMERCE_PRODUCT_ALREADY_HAS', 2);
        define('ECOMMERCE_PRODUCT_DISABLED', 3);
        define('ECOMMERCE_PRODUCT_PROHIBITED', 4);
        define('ECOMMERCE_PRODUCT_OUT_OF_STOCK', 5);
        define('ECOMMERCE_PRODUCT_MISSING', 6);
        define('ECOMMERCE_PRODUCT_INTERNAL_ERROR', 7);
    }

    require_lang('ecommerce');
}

/**
 * Check whether the system is in test mode (normally, not).
 *
 * @return boolean The answer.
 */
function ecommerce_test_mode()
{
    return get_option('ecommerce_test_mode') == '1';
}

/**
 * Get the symbol of the currency we're trading in.
 *
 * @param  ?ID_TEXT $currency The currency (null: configured).
 * @return ID_TEXT The currency symbol.
 */
function ecommerce_get_currency_symbol($currency = null)
{
    if ($currency === null) {
        $currency = get_option('currency');
    }
    require_code('currency');
    list($symbol,) = get_currency_symbol($currency);
    return $symbol;
}

/**
 * Find a transaction fee from a transaction amount. Regular fees aren't taken into account.
 *
 * @param  ?ID_TEXT $trans_id The transaction ID (null: auto-generate)
 * @param  ID_TEXT $purchase_id The purchase ID
 * @param  SHORT_TEXT $item_name The item name
 * @param  SHORT_TEXT $amount The amount
 * @param  ?integer $length The length (null: not a subscription)
 * @param  ID_TEXT $length_units The length units
 * @param  ?ID_TEXT $via The service the payment will go via via (null: autodetect).
 * @return array A pair: The form fields, Hidden fields
 */
function get_transaction_form_fields($trans_id, $purchase_id, $item_name, $amount, $length, $length_units, $via = null)
{
    if (is_null($via)) {
        $via = get_option('payment_gateway');
    }

    if (is_null($trans_id)) {
        require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
        $object = object_factory('Hook_' . $via);
        if (!method_exists($object, 'do_transaction')) {
            warn_exit(do_lang_tempcode('LOCAL_PAYMENT_NOT_SUPPORTED', escape_html($via)));
        }
        $trans_id = $object->generate_trans_id();
    }

    $GLOBALS['SITE_DB']->query_insert('trans_expecting', array(
        'id' => $trans_id,
        'e_purchase_id' => $purchase_id,
        'e_item_name' => $item_name,
        'e_amount' => $amount,
        'e_member_id' => get_member(),
        'e_ip_address' => get_ip_address(),
        'e_session_id' => get_session_id(),
        'e_time' => time(),
        'e_length' => $length,
        'e_length_units' => $length_units,
    ));

    require_code('form_templates');

    $fields = new Tempcode();
    $hidden = new Tempcode();

    $fields->attach(form_input_line(do_lang_tempcode('CARDHOLDER_NAME'), do_lang_tempcode('DESCRIPTION_CARDHOLDER_NAME'), 'name', ecommerce_test_mode() ? $GLOBALS['FORUM_DRIVER']->get_username(get_member()) : get_cms_cpf('payment_cardholder_name'), true));
    $fields->attach(form_input_list(do_lang_tempcode('CARD_TYPE'), '', 'card_type', $object->create_selection_list_card_types(ecommerce_test_mode() ? 'Visa' : get_cms_cpf('payment_type'))));
    $fields->attach(form_input_line(do_lang_tempcode('CARD_NUMBER'), do_lang_tempcode('DESCRIPTION_CARD_NUMBER'), 'card_number', ecommerce_test_mode() ? '4444333322221111' : get_cms_cpf('payment_card_number'), true));
    $fields->attach(form_input_line(do_lang_tempcode('CARD_START_DATE'), do_lang_tempcode('DESCRIPTION_CARD_START_DATE'), 'start_date', ecommerce_test_mode() ? date('m/y', utctime_to_usertime(time() - 60 * 60 * 24 * 365)) : get_cms_cpf('payment_card_start_date'), true));
    $fields->attach(form_input_line(do_lang_tempcode('CARD_EXPIRY_DATE'), do_lang_tempcode('DESCRIPTION_CARD_EXPIRY_DATE'), 'expiry_date', ecommerce_test_mode() ? date('m/y', utctime_to_usertime(time() + 60 * 60 * 24 * 365)) : get_cms_cpf('payment_card_expiry_date'), true));
    $fields->attach(form_input_integer(do_lang_tempcode('CARD_ISSUE_NUMBER'), do_lang_tempcode('DESCRIPTION_CARD_ISSUE_NUMBER'), 'issue_number', intval(get_cms_cpf('payment_card_issue_number')), false));
    $fields->attach(form_input_line(do_lang_tempcode('CARD_CV2'), do_lang_tempcode('DESCRIPTION_CARD_CV2'), 'cv2', ecommerce_test_mode() ? '123' : get_cms_cpf('payment_card_cv2'), true));

    // Shipping address fields
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_firstname'), '', 'first_name', get_cms_cpf('firstname'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_lastname'), '', 'last_name', get_cms_cpf('last_name'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_building_name_or_number'), '', 'address1', get_cms_cpf('building_name_or_number'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_city'), '', 'city', get_cms_cpf('city'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_state'), '', 'zip', get_cms_cpf('state'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_post_code'), '', 'zip', get_cms_cpf('post_code'), true));
    $fields->attach(form_input_line(do_lang_tempcode('SPECIAL_CPF__cms_country'), '', 'country', get_cms_cpf('country'), true));

    $hidden->attach(form_input_hidden('trans_id', $trans_id));

    // Set purchase ID as hidden form field to get back after transaction
    $fields->attach(form_input_hidden('customfld1', $purchase_id));

    return array($fields, $hidden);
}

/**
 * Find a transaction fee from a transaction amount. Regular fees aren't taken into account.
 *
 * @param  float $amount A transaction amount.
 * @param  ID_TEXT $via The service the payment went via.
 * @return float The fee
 */
function get_transaction_fee($amount, $via)
{
    if ($via == '') {
        return 0.0;
    }
    if ($via == 'manual') {
        return 0.0;
    }

    if ((file_exists(get_file_base() . '/sources/hooks/systems/ecommerce_via/' . $via)) || (file_exists(get_file_base() . '/sources_custom/hooks/systems/ecommerce_via/' . $via))) {
        require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
        $object = object_factory('Hook_' . $via);
        return $object->get_transaction_fee($amount);
    }

    return 0.0;
}

/**
 * Make a transaction (payment) button.
 *
 * @param  ID_TEXT $type_code The product codename.
 * @param  SHORT_TEXT $item_name The human-readable product title.
 * @param  ID_TEXT $purchase_id The purchase ID.
 * @param  float $amount A transaction amount.
 * @param  ID_TEXT $currency The currency to use.
 * @param  ?ID_TEXT $via The service the payment will go via via (null: autodetect).
 * @return tempcode The button
 */
function make_transaction_button($type_code, $item_name, $purchase_id, $amount, $currency, $via = null)
{
    if (is_null($via)) {
        $via = get_option('payment_gateway');
    }
    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
    $object = object_factory('Hook_' . $via);
    return $object->make_transaction_button($type_code, $item_name, $purchase_id, $amount, $currency);
}

/**
 * Make a subscription (payment) button.
 *
 * @param  ID_TEXT $type_code The product codename.
 * @param  SHORT_TEXT $item_name The human-readable product title.
 * @param  ID_TEXT $purchase_id The purchase ID.
 * @param  float $amount A transaction amount.
 * @param  integer $length The subscription length in the units.
 * @param  ID_TEXT $length_units The length units.
 * @set    d w m y
 * @param  ID_TEXT $currency The currency to use.
 * @param  ?ID_TEXT $via The service the payment will go via via (null: autodetect).
 * @return tempcode The button
 */
function make_subscription_button($type_code, $item_name, $purchase_id, $amount, $length, $length_units, $currency, $via = null)
{
    if (is_null($via)) {
        $via = get_option('payment_gateway');
    }
    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
    $object = object_factory('Hook_' . $via);
    return $object->make_subscription_button($type_code, $item_name, $purchase_id, $amount, $length, $length_units, $currency);
}

/**
 * Make a subscription cancellation button.
 *
 * @param  AUTO_LINK $purchase_id The purchase ID.
 * @param  ID_TEXT $via The service the payment will go via via.
 * @return ?tempcode The button (null: no special cancellation -- just delete the subscription row to stop Composr regularly re-charging)
 */
function make_cancel_button($purchase_id, $via)
{
    if ($via == '') {
        return null;
    }
    if ($via == 'manual') {
        return null;
    }
    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
    $object = object_factory('Hook_' . $via);
    if (!method_exists($object, 'make_cancel_button')) {
        return null;
    }
    return $object->make_cancel_button($purchase_id);
}

/**
 * Send an invoice notification to a member.
 *
 * @param  MEMBER $member_id The member to send to.
 * @param  AUTO_LINK $id The invoice ID.
 */
function send_invoice_notification($member_id, $id)
{
    // Send out notification
    require_code('notifications');
    $_url = build_url(array('page' => 'invoices', 'type' => 'browse'), get_module_zone('invoices'), null, false, false, true);
    $url = $_url->evaluate();
    dispatch_notification('invoice', null, do_lang('INVOICE_SUBJECT', strval($id), null, null, get_lang($member_id)), do_lang('INVOICE_MESSAGE', $url, get_site_name(), null, get_lang($member_id)), array($member_id));
}

/**
 * Find all products, except ones from hooks that might have too many to list (so don't rely on this for important backend tasks).
 *
 * @param  boolean $site_lang Whether to make sure the language for item_name is the site default language (crucial for when we read/go to third-party sales systems and use the item_name as a key).
 * @return array A list of maps of product details.
 */
function find_all_products($site_lang = false)
{
    $_hooks = find_all_hooks('systems', 'ecommerce');
    $products = array();
    foreach (array_keys($_hooks) as $hook) {
        require_code('hooks/systems/ecommerce/' . filter_naughty_harsh($hook));
        $object = object_factory('Hook_ecommerce_' . filter_naughty_harsh($hook), true);
        if (is_null($object)) {
            continue;
        }
        $_products = $object->get_products($site_lang);
        foreach ($_products as $type_code => $details) {
            if (!array_key_exists(4, $details)) {
                $details[4] = do_lang('CUSTOM_PRODUCT_' . $type_code, null, null, null, $site_lang ? get_site_default_lang() : null);
            }
            $details[] = $object;
            $products[$type_code] = $details;
        }
    }
    return $products;
}

/**
 * Find product.
 *
 * @param  ID_TEXT $search The item name/product_id
 * @param  boolean $site_lang Whether to make sure the language for item_name is the site default language (crucial for when we read/go to third-party sales systems and use the item_name as a key).
 * @param  boolean $search_item_names Whether $search refers to the item name rather than the product codename
 * @return ?object The product-class object (null: not found).
 */
function find_product($search, $site_lang = false, $search_item_names = false)
{
    $_hooks = find_all_hooks('systems', 'ecommerce');
    foreach (array_keys($_hooks) as $hook) {
        require_code('hooks/systems/ecommerce/' . filter_naughty_harsh($hook));
        $object = object_factory('Hook_ecommerce_' . filter_naughty_harsh($hook), true);
        if (is_null($object)) {
            continue;
        }

        $_products = $object->get_products($site_lang, $search, $search_item_names);

        $type_code = mixed();
        foreach ($_products as $type_code => $product_row) {
            if (is_integer($type_code)) {
                $type_code = strval($type_code);
            }

            if ($search_item_names) {
                if (($product_row[4] == $search) || ('_' . $type_code == $search)) {
                    return $object;
                }
            } else {
                if ($type_code == $search) {
                    return $object;
                }
            }
        }
    }
    return null;
}

/**
 * Find product info row.
 *
 * @param  ID_TEXT $search The product codename/item name
 * @param  boolean $site_lang Whether to make sure the language for item_name is the site default language (crucial for when we read/go to third-party sales systems and use the item_name as a key).
 * @param  boolean $search_item_names Whether $search refers to the item name rather than the product codename
 * @return array A pair: The product-class map, and the formal product name (both will be NULL if not found).
 */
function find_product_row($search, $site_lang = false, $search_item_names = false)
{
    $_hooks = find_all_hooks('systems', 'ecommerce');
    foreach (array_keys($_hooks) as $hook) {
        require_code('hooks/systems/ecommerce/' . filter_naughty_harsh($hook));
        $object = object_factory('Hook_ecommerce_' . filter_naughty_harsh($hook), true);
        if (is_null($object)) {
            continue;
        }

        $_products = $object->get_products($site_lang, $search, $search_item_names);

        $type_code = mixed();
        foreach ($_products as $type_code => $product_row) {
            if (is_integer($type_code)) {
                $type_code = strval($type_code);
            }

            if ($search_item_names) {
                if (($product_row[4] == $search) || ('_' . $type_code == $search)) {
                    return array($product_row, $type_code);
                }
            } else {
                if ($type_code == $search) {
                    return array($product_row, $type_code);
                }
            }
        }
    }
    return array(null, null);
}

/**
 * Find whether local payment will be performed.
 *
 * @return boolean Whether local payment will be performed.
 */
function perform_local_payment()
{
    $via = get_option('payment_gateway');
    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
    $object = object_factory('Hook_' . $via);
    return ((get_option('use_local_payment') == '1') && (method_exists($object, 'do_transaction')));
}

/**
 * Send an IPN call to a remote host for debugging purposes.
 * Useful for making one Composr site (caller site) pretend to be PayPal, when talking to another (target site).
 * Make sure the target site has the caller site listed as the backdoor_ip in the base config, or the verification will happen and fail.
 *
 * @param   URLPATH $ipn_target URL to send IPN to
 * @param   string $ipn_message Post parameters to send, in query string format
 * @return  string   Output
 */
function dev__ipn_debug($ipn_target, $ipn_message)
{
    require_code('ecommerce');
    $post_params = array();
    parse_str($ipn_message, $post_params);

    return http_download_file($ipn_target, null, false, false, 'Composr-IPN-debug', $post_params) . "\n" . $GLOBALS['HTTP_MESSAGE'];
}

/**
 * Handle IPN's.
 *
 * @return ID_TEXT The ID of the purchase-type (meaning depends on item_name)
 */
function handle_transaction_script()
{
    if ((file_exists(get_file_base() . '/data_custom/ecommerce.log')) && (is_writable_wrap(get_file_base() . '/data_custom/ecommerce.log'))) {
        $myfile = fopen(get_file_base() . '/data_custom/ecommerce.log', 'at');
        fwrite($myfile, serialize($_POST) . "\n");
        fwrite($myfile, serialize($_GET) . "\n");
        fwrite($myfile, "\n\n");
        fclose($myfile);
    }

    $via = get_param_string('from', get_option('payment_gateway'));
    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));
    $object = object_factory('Hook_' . $via);

    ob_start();

    list($purchase_id, $item_name, $payment_status, $reason_code, $pending_reason, $memo, $mc_gross, $mc_currency, $txn_id, $parent_txn_id, $period) = $object->handle_transaction();

    $type_code = handle_confirmed_transaction($purchase_id, $item_name, $payment_status, $reason_code, $pending_reason, $memo, $mc_gross, $mc_currency, $txn_id, $parent_txn_id, $period, $via);

    if (method_exists($object, 'show_payment_response')) {
        echo $object->show_payment_response($type_code, $purchase_id);
    }

    return $purchase_id;
}

/**
 * Handle IPN's that have been confirmed as backed up by real money.
 *
 * @param  ID_TEXT $purchase_id The ID of the purchase-type (meaning depends on item_name)
 * @param  SHORT_TEXT $item_name The item being purchased (aka the product) (blank: subscription, so we need to look it up). One might wonder why we use $item_name instead of $type_code. This is because we pass human-readable-names (hopefully unique!!!) through payment gateways because they are visually shown to the user. (blank: it's a subscription, so look up via a key map across the subscriptions table)
 * @param  ID_TEXT $payment_status The status this transaction is telling of
 * @set    Pending Completed SModified SCancelled
 * @param  SHORT_TEXT $reason_code The code that gives reason to the status
 * @param  SHORT_TEXT $pending_reason The reason it is in pending status (if it is)
 * @param  SHORT_TEXT $memo A note attached to the transaction
 * @param  SHORT_TEXT $mc_gross The amount of money
 * @param  SHORT_TEXT $mc_currency The currency the amount is in
 * @param  SHORT_TEXT $txn_id The transaction ID
 * @param  SHORT_TEXT $parent_txn_id The ID of the parent transaction
 * @param  string $period The subscription period (blank: N/A / unknown: trust is correct on the gateway)
 * @param  ID_TEXT $via The payment gateway
 * @return ID_TEXT The product purchased
 */
function handle_confirmed_transaction($purchase_id, $item_name, $payment_status, $reason_code, $pending_reason, $memo, $mc_gross, $mc_currency, $txn_id, $parent_txn_id, $period, $via)
{
    $is_subscription = ($item_name == '');

    // Try and locate the product
    if ($is_subscription) { // Subscription
        $type_code = $GLOBALS['SITE_DB']->query_select_value_if_there('subscriptions', 's_type_code', array('id' => intval($purchase_id)));
        if (is_null($type_code)) {
            fatal_ipn_exit(do_lang('NO_SUCH_SUBSCRIPTION', strval($purchase_id)));
        }
        $item_name = '_' . $type_code;

        // Find what we sold
        list($found,) = find_product_row($type_code, true, false);
        if (!is_null($found)) {
            $item_name = $found[4];
        }

        // Check subscription length
        if ($period != '') {
            $length = array_key_exists('length', $found[3]) ? strval($found[3]['length']) : '1';
            $length_units = array_key_exists('length_units', $found[3]) ? $found[3]['length_units'] : 'm';
            if (strtolower($period) != strtolower($length . ' ' . $length_units)) {
                fatal_ipn_exit(do_lang('IPN_SUB_PERIOD_WRONG'));
            }
        }
    } else {
        // Find what we sold
        list($found, $type_code) = find_product_row($item_name, true, true);

        if ($found[0] == PRODUCT_SUBSCRIPTION) {
            exit(); // We ignore separate payment signal for subscriptions (for Paypal it is web_accept)
        }
    }
    if (is_null($found)) {
        fatal_ipn_exit(do_lang('PRODUCT_NO_SUCH') . ' - ' . $item_name, true);
    }

    // Check price, if one defined
    if (($mc_gross != $found[1]) && ($found[1] != '?')) {
        if (($payment_status == 'Completed') && ($via != 'manual')) {
            fatal_ipn_exit(do_lang('PURCHASE_WRONG_PRICE', $item_name), $is_subscription);
        }
    }
    if ($mc_currency != get_option('currency')) {
        if (($payment_status != 'SCancelled') && ($via != 'manual')) {
            fatal_ipn_exit(do_lang('PURCHASE_WRONG_CURRENCY'));
        }
    }

    // Store
    $GLOBALS['SITE_DB']->query_insert('transactions', array(
        'id' => $txn_id,
        't_memo' => $memo,
        't_type_code' => $type_code,
        't_purchase_id' => $purchase_id,
        't_status' => $payment_status,
        't_pending_reason' => $pending_reason,
        't_reason' => $reason_code,
        't_amount' => $mc_gross,
        't_currency' => $mc_currency,
        't_parent_txn_id' => $parent_txn_id,
        't_time' => time(),
        't_via' => $via,
    ));

    $found['txn_id'] = $txn_id;

    // Pending
    if ($payment_status == 'Pending') {
        if ($found[0] == PRODUCT_INVOICE) { // Invoices have special support for tracking the order status
            $GLOBALS['SITE_DB']->query_update('invoices', array('i_state' => 'pending'), array('id' => intval($purchase_id)), '', 1);
        } elseif ($found[0] == PRODUCT_SUBSCRIPTION) { // Subscriptions have special support for tracking the order status
            $GLOBALS['SITE_DB']->query_update('subscriptions', array('s_state' => 'pending'), array('id' => intval($purchase_id)), '', 1);
            if ($found[2] != '') {
                call_user_func_array($found[2], array($purchase_id, $found, $type_code, true)); // Run cancel code
            }
        } elseif ($item_name == do_lang('shopping:CART_ORDER', $purchase_id)) { // Cart orders have special support for tracking the order status
            $found['ORDER_STATUS'] = 'ORDER_STATUS_awaiting_payment';

            if ($found[2] != '') {
                call_user_func_array($found[2], array($purchase_id, $found, $type_code, $payment_status, $txn_id)); // Set order status
            }
        }

        // Pending transactions stop here
        fatal_ipn_exit(do_lang('TRANSACTION_NOT_COMPLETE', $type_code . ':' . strval($purchase_id), $payment_status), true);
    }

    // Invoice: Check price
    if ($found[0] == PRODUCT_INVOICE) {
        $price = $GLOBALS['SITE_DB']->query_select_value('invoices', 'i_amount', array('id' => intval($purchase_id)));
        if ($price != $mc_gross) {
            if ($via != 'manual') {
                fatal_ipn_exit(do_lang('PURCHASE_WRONG_PRICE', $item_name));
            }
        }
    }

    /*
    At this point we know our transaction is good -- or a subscription cancellation.
    Possible statuses: Completed|SModified|SCancelled
    */

    // Subscription: Completed (Made active)
    if (($payment_status == 'Completed') && ($found[0] == PRODUCT_SUBSCRIPTION)) {
        $GLOBALS['SITE_DB']->query_update('subscriptions', array('s_auto_fund_source' => $via, 's_auto_fund_key' => $txn_id, 's_state' => 'active'), array('id' => intval($purchase_id)), '', 1);
    }

    // Subscription: Modified
    if (($payment_status == 'SModified') && ($found[0] == PRODUCT_SUBSCRIPTION)) {
        // No special action needed
    }

    // Subscription: Cancelled
    if (($payment_status == 'SCancelled') && ($found[0] == PRODUCT_SUBSCRIPTION)) {
        $GLOBALS['SITE_DB']->query_update('subscriptions', array('s_auto_fund_source' => $via, 's_auto_fund_key' => $txn_id, 's_state' => 'cancelled'), array('id' => intval($purchase_id)), '', 1);
    }

    // Invoice handling
    if (($payment_status == 'Completed') && ($found[0] == PRODUCT_INVOICE)) {
        $GLOBALS['SITE_DB']->query_update('invoices', array('i_state' => 'paid'), array('id' => intval($purchase_id)), '', 1);
    }

    // Set order dispatch status
    if ($payment_status == 'Completed') {
        $object = find_product($type_code, true);

        if ((is_object($object)) && (!method_exists($object, 'get_product_dispatch_type'))) { // If hook does not have dispatch method setting take dispatch method as automatic
            $found['ORDER_STATUS'] = 'ORDER_STATUS_dispatched';
        } elseif (is_object($object) && $object->get_product_dispatch_type($purchase_id) == 'automatic') {
            $found['ORDER_STATUS'] = 'ORDER_STATUS_dispatched';
        } else {
            $found['ORDER_STATUS'] = 'ORDER_STATUS_payment_received'; // Dispatch has to happen manually still
        }
    }

    // Dispatch (all product types)
    if ($payment_status != 'SModified') {
        // Call completion/cancellation code
        if ($found[2] != '') {
            call_user_func_array($found[2], array($purchase_id, $found, $type_code, $payment_status, $txn_id));
        }

        // Send out notification to staff for completion/cancellation
        if ($found[0] == PRODUCT_SUBSCRIPTION) {
            require_code('notifications');
            $member_id = $GLOBALS['SITE_DB']->query_select_value_if_there('subscriptions', 's_member_id', array('id' => intval($purchase_id)));
            if (!is_null($member_id)) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
                if (is_null($username)) {
                    $username = do_lang('GUEST');
                }
                if ($payment_status == 'Completed') { // Completed
                    $subject = do_lang('SERVICE_PAID_FOR', $item_name, $username, get_site_name(), get_site_default_lang());
                    $body = do_lang('_SERVICE_PAID_FOR', $item_name, $username, get_site_name(), get_site_default_lang());
                    dispatch_notification('service_paid_for_staff', null, $subject, $body);
                } else { // Must be SCancelled
                    $subject = do_lang('SERVICE_CANCELLED', $item_name, $username, get_site_name(), get_site_default_lang());
                    $body = do_lang('_SERVICE_CANCELLED', $item_name, $username, get_site_name(), get_site_default_lang());
                    dispatch_notification('service_cancelled_staff', null, $subject, $body);
                }
            }
        }
    }

    return $type_code;
}

/**
 * Exit Composr and write to the error log file.
 *
 * @param  string $error The message.
 * @param  boolean $dont_trigger Dont trigger an error
 * @return mixed Never returns (i.e. exits)
 */
function fatal_ipn_exit($error, $dont_trigger = false)
{
    echo $error . "\n";
    if (!$dont_trigger) {
        trigger_error($error, E_USER_NOTICE);
    }
    exit();
}

/**
 * Make a shopping cart payment button.
 *
 * @param  AUTO_LINK $order_id Order ID
 * @param  ID_TEXT $currency The currency to use.
 * @return tempcode The button
 */
function make_cart_payment_button($order_id, $currency)
{
    $_items = $GLOBALS['SITE_DB']->query_select('shopping_order_details', array('p_name', 'p_price', 'p_quantity'), array('order_id' => $order_id));
    $items = array();
    foreach ($_items as $item) {
        $items[] = array(
            'PRODUCT_NAME' => $item['p_name'],
            'PRICE' => float_to_raw_string($item['p_price']),
            'QUANTITY' => strval($item['p_quantity']),
        );
    }

    $via = get_option('payment_gateway');

    require_code('hooks/systems/ecommerce_via/' . filter_naughty_harsh($via));

    $object = object_factory('Hook_' . $via);

    if (!method_exists($object, 'make_cart_transaction_button')) {
        $amount = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'tot_price', array('id' => $order_id));
        return $object->make_transaction_button($order_id, do_lang('CART_ORDER', $order_id), $order_id, $amount, $currency);
    }

    return $object->make_cart_transaction_button($items, $currency, $order_id);
}
