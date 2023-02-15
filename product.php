<?php
// require __DIR__ . '/vendor/autoload.php';


use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://localhost/onlinecourse',
    'ck_23ebf32a1dab0619fac7e85480d850a8a640d450',
    'cs_b490915e4158cf6882d5b5a5d861f026a39a7232',
    [
        'wp_api' => true,
        'version' => 'wc/v3'
    ]
);
?>

<?php print_r($woocommerce->get('orders')); ?>