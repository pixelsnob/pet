<?php
/**
 * Sends unsent order emails
 * 
 */
require_once 'zf_init.php';

$application->getBootstrap()->bootstrap(
    array('router', 'config', 'registryView', 'db', 'logger', 'autoload'));

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

exit(0);
