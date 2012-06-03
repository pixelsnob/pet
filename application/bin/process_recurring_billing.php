<?php
/**
 * Sends unsent order emails
 * 
 */
require_once 'zf_init.php';

$application->getBootstrap()->bootstrap(
    array('db', 'config', 'logger', 'autoload', 'registryView'));

// Get command line opts
$opts = getopt('e:');

$exp = (isset($opts['e']) ? $opts['e'] : null);
try {
    $exp_obj = new DateTime($exp);
} catch (Exception $e) {
    echo "Expiration date passed to -e is not valid\n";
    exit(1);
}
$orders_svc = new Service_Orders;
$orders_svc->processRecurringBilling($exp_obj);

exit(0);
