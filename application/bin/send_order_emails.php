<?php
/**
 * Sends unsent order emails
 * 
 */
require_once 'zf_init.php';

$application->getBootstrap()->bootstrap(
    array('registryView', 'db', 'config', 'logger', 'autoload'));

$orders_svc = new Service_Orders;
$orders_svc->sendOrderEmails();

exit(0);
