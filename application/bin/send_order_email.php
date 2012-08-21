<?php
/**
 * Sends an order email
 * 
 */
require_once 'zf_init.php';

//try {
    $application->getBootstrap()->bootstrap(
        array('router', 'config', 'registryView', 'db', 'autoload', 'mail'));

    // Get command line opts
    $opts = getopt('o:d');
    if (!isset($opts['o']) || !strlen($opts['o'])) {
        echo "Please pass the order_id as option -o\n";
        exit(1);
    }

    $order_id = $opts['o'];
    $debug = isset($opts['d']);
    $orders_svc = new Service_Orders;
    $orders_svc->sendOrderEmail($order_id, $debug);
    echo "Mail sent!\n";
/*} catch (Exception $e){ 
    echo 'Mail not sent. Error: ' . $e->getMessage() . "\n";
    exit(1);
}*/

exit(0);
