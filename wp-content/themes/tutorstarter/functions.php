<?php
/**
 * Handles loading all the necessary files
 *
 * @package Tutor_Starter
 */

defined( 'ABSPATH' ) || exit;

// Content width.
if ( ! isset( $content_width ) ) 
	$content_width = apply_filters( 'tutorstarter_content_width', get_theme_mod( 'content_width_value', 1140 ) );

// Theme GLOBALS.
$theme = wp_get_theme();
define( 'TUTOR_STARTER_VERSION', $theme->get( 'Version' ) );

// Load autoloader.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) :
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
endif;

// Include TGMPA class.
if ( file_exists( dirname( __FILE__ ) . '/inc/Custom/class-tgm-plugin-activation.php' ) ) :
	require_once dirname( __FILE__ ) . '/inc/Custom/class-tgm-plugin-activation.php';
endif;

// Register services.
if ( class_exists( 'Tutor_Starter\\Init' ) ) :
	Tutor_Starter\Init::register_services();
endif;




//Send Order Details to an External System


/* after an order has been processed, we will use the  'woocommerce_thankyou' hook, to add our function, to send the data */
add_action('woocommerce_thankyou', 'wdm_send_order_to_ext'); 
function wdm_send_order_to_ext( $order_id ){
    // get order object and order details
    $order = new WC_Order( $order_id ); 
    $email = $order->billing_email;
    $phone = $order->billing_phone;
    $shipping_type = $order->get_shipping_method();
    $shipping_cost = $order->get_total_shipping();

    // set the address fields
    $user_id = $order->user_id;
    $address_fields = array('country',
        'title',
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'address_3',
        'address_4',
        'city',
        'state',
        'postcode');

    $address = array();
    if(is_array($address_fields)){
        foreach($address_fields as $field){
            $address['billing_'.$field] = get_user_meta( $user_id, 'billing_'.$field, true );
            $address['shipping_'.$field] = get_user_meta( $user_id, 'shipping_'.$field, true );
        }
    }
    
    // get coupon information (if applicable)
    $cps = array();
    $cps = $order->get_items( 'coupon' );
    
    $coupon = array();
    foreach($cps as $cp){
            // get coupon titles (and additional details if accepted by the API)
            $coupon[] = $cp['name'];
    }
    
    // get product details
    $items = $order->get_items();
    
    $item_name = array();
    $item_qty = array();
    $item_price = array();
    $item_sku = array();
        
    foreach( $items as $key => $item){
        $item_name[] = $item['name'];
        $item_qty[] = $item['qty'];
        $item_price[] = $item['line_total'];
        
        $item_id = $item['product_id'];
        $product = new WC_Product($item_id);
        $item_sku[] = $product->get_sku();
    }
    
    /* for online payments, send across the transaction ID/key. If the payment is handled offline, you could send across the order key instead */
    $transaction_key = get_post_meta( $order_id, '_transaction_id', true );
    $transaction_key = empty($transaction_key) ? $_GET['key'] : $transaction_key;   
    
    // set the username and password
    $api_username = 'testuser';
    $api_password = 'testpass';

    // to test out the API, set $api_mode as ‘sandbox’
    $api_mode = 'sandbox';
    if($api_mode == 'sandbox'){
        // sandbox URL example
        $endpoint = "http://sandbox.example.com/"; 
    }
    else{
        // production URL example
        $endpoint = "http://example.com/"; 
    }

        // setup the data which has to be sent
    $data = array(
            'apiuser' => $api_username,
            'apipass' => $api_password,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'bill_firstname' => $address['billing_first_name'],
            'bill_surname' => $address['billing_last_name'],
            'bill_address1' => $address['billing_address_1'],
            'bill_address2' => $address['billing_address_2'],
            'bill_city' => $address['billing_city'],
            'bill_state' => $address['billing_state'],
            'bill_zip' => $address['billing_postcode'],
            'ship_firstname' => $address['shipping_first_name'],
            'ship_surname' => $address['shipping_last_name'],
            'ship_address1' => $address['shipping_address_1'],
            'ship_address2' => $address['shipping_address_2'],
            'ship_city' => $address['shipping_city'],
            'ship_state' => $address['shipping_state'],
            'ship_zip' => $address['shipping_postcode'],
            'shipping_type' => $shipping_type,
            'shipping_cost' => $shipping_cost,
            'item_sku' => implode(',', $item_sku), 
            'item_price' => implode(',', $item_price), 
            'quantity' => implode(',', $item_qty), 
            'transaction_key' => $transaction_key,
            'coupon_code' => implode( ",", $coupon )
        );

            // send API request via cURL
        $ch = curl_init();

        /* set the complete URL, to process the order on the external system. Let’s consider http://example.com/buyitem.php is the URL, which invokes the API */
        curl_setopt($ch, CURLOPT_URL, $endpoint."buyitem.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec ($ch);
    
        curl_close ($ch);
        
        // the handle response    
        if (strpos($response,'ERROR') !== false) {
                print_r($response);
        } else {
                // success
        }
 }


// Add the custom meta field 

//  woocommerce_checkout_create_order
add_action('woocommerce_checkout_create_order', 'before_checkout_create_order', 20, 2);
function before_checkout_create_order( $order, $data ) {
    if(!empty($data)) {
        $order->update_meta_data( '_order_json_data', json_encode($data) );
        // $response = wp_remote_post( 'https://www.google.com/send_data', $data );
        $order->update_meta_data( 'send_api_request', 0 );
    }
    
}

// display custom field on order edit pages

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_custom_field_on_order_edit_pages', 10, 1 );
function display_custom_field_on_order_edit_pages( $order ){
    $send_api_request_value = get_post_meta( $order->get_id(), 'send_api_request', true );
    echo '<input type="hidden"   id="send_api_request_field"  value="'. $send_api_request_value.'" />';
    echo '<input type="hidden"   id="request_order_id"  value="'. $order->get_id().'" />';
    echo '<input type="hidden"   id="admin_ajax_url"  value="'. admin_url('admin-ajax.php').'" />';
    woocommerce_form_field( 'send_api_request', array(
        'type'      => 'checkbox',
        'class'     => array('input-checkbox'),
        'id'     => "send_api_request",
        'label'     => __('Send API request')
    ),  $send_api_request_value );

}

//display custom field on order edit pages via Ajax


function my_enqueue($hook) {
    wp_enqueue_script('my_custom_script',get_template_directory_uri() . '/assets/dist/js/custom.js');
}

add_action('admin_enqueue_scripts', 'my_enqueue');


add_action( 'wp_ajax_api_request_ajax_function', 'api_request_ajax_function' );
function api_request_ajax_function() {
	$order_id = $_POST['order_id'];
    $order_json_data = get_post_meta( $order_id, '_order_json_data', true );
    if(!empty($order_json_data)) {
         // $response = wp_remote_post( 'https://www.google.com/send_data', $data );
        //  if($response['success']) {
        //     update_post_meta($order_id, 'send_api_request', 1);
        //  }
        update_post_meta($order_id, 'send_api_request', 1);
        
    }
    
    //wp_send_json($order_json_data) ;
}




// // Add a column to the edit post list p
// add_filter( 'manage_edit-shop_order_columns', 'add_new_columns');
// /**
//  * Add new columns to the post table
//  *
//  * @param Array $columns - Current columns on the list post
//  */
// function add_new_columns( $columns ) {
//  	$column_meta = array( 'meta' => 'Send API Request' );
// 	$columns = array_slice( $columns, 0, 2, true ) + $column_meta + array_slice( $columns, 2, NULL, true );
// 	return $columns; 
// }   


// Add column to admin Orders summary page with custom field data
function custom_api_request_column($columns)
{
    $new_meta_column = array();
    foreach ($columns as $column_name => $column_info) {
        $new_meta_column[$column_name] = $column_info;
        if ('order_total' === $column_name) {
            $new_meta_column['send_request'] = __('Api Request', 'my-textdomain');
        }
    }
    return $new_meta_column;
}
add_filter('manage_edit-shop_order_columns', 'custom_api_request_column');


// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'send_request' :
            // Get custom post meta data
            $send_api_request = get_post_meta( $post_id, 'send_api_request', true );
            if(isset($send_api_request) && $send_api_request > 0)
                echo "TRUE";
            else
                echo "FALSE";
            break;
    }
}



//Arvind Adding custom fields meta data for each new column


// // ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
// add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
// function custom_shop_order_column($columns)
// {
//     $column_meta = array();

//     // Inserting columns to a specific location
//     foreach( $columns as $key => $column){
//         $column_meta[$key] = $column;
//         if( $key ==  'order_status' ){
//             // Inserting after "Status" column
//             $column_meta['my-column1'] = __( 'Send API Request','theme_domain');
//            // $column_meta['my-column2'] = __( 'Title2','theme_domain');
//         }
//     }
//     return $column_meta;
// }

// // Adding custom fields meta data for each new column (example)
// add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
// function custom_orders_list_column_content( $column, $post_id )
// {
//     switch ( $column )
//     {
//         case 'my-column1' :
//             // Get custom post meta data
//             $send_api_request = get_post_meta( $post_id, 'send_api_request', true );
//             if(isset($send_api_request) && $send_api_request > 0)
//                 echo "TRUE";
//             else
//                 echo "FALSE";
//             break;
//     }
// }