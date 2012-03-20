<?php
/**
 * Generates a Db_Table and Model file with code from an
 * existing db
 * 
 */
require_once 'zf_init.php';

// Get command line opts
$opts = getopt('t:s:p:');

// We need a table name
if (!isset($opts['t']) || !strlen($opts['t'])) {
    echo 'Please pass the table name as option -t' . PHP_EOL;
    exit(1);
}
// We also need a singular class name
if (!isset($opts['s']) || !strlen($opts['s'])) {
    echo 'Please pass the singular model class name as option -s' . PHP_EOL;
    exit(1);
}
// And a plural class name 
if (!isset($opts['p']) || !strlen($opts['p'])) {
    echo 'Please pass the plural model class name as option -p' . PHP_EOL;
    exit(1);
}

$application->getBootstrap()->bootstrap(
    array('db', 'config', 'logger', 'autoload'));

// Create Zend_DbTable class
$db_table_class = new Zend_CodeGenerator_Php_Class;
$db_table_class_name = 'Model_DbTable_' . $opts['p'];
echo "Creating $db_table_class_name" . PHP_EOL;
$db_table_docblock = new Zend_CodeGenerator_Php_Docblock(array(
    'tags' => array(array(
        'name'        => 'package',
        'description' => $db_table_class_name
    ))
));
$db_table_class->setName('Model_DbTable_' . ucfirst($opts['p']))
    ->setExtendedClass('Zend_Db_Table_Abstract')
    ->setDocblock($db_table_docblock)
    ->setProperties(array(
        array(
            'name'          => '_name',
            //'extends'       => 'Pet_Db_Table_Abstract',
            'visibility'    => 'protected',
            'defaultValue'  => $opts['t']
        )
));

// Create Zend_DbTable file
$db_table_file = new Zend_CodeGenerator_Php_File;
$db_table_file->setClass($db_table_class);
$db_table_path = APPLICATION_PATH . '/models/DbTable/' . $opts['p'] . '.php';
echo "Creating $db_table_path" . PHP_EOL;
if (false === file_put_contents($db_table_path, $db_table_file->generate())) {
    echo "$db_table_path not created" . PHP_EOL;
    exit(1);
}

// Create an instance of the DbTable class we just created, so we can get 
// table meta data
$db_table = new $db_table_class_name;
$table_info = $db_table->info();
// We want the columns
if (!isset($table_info['cols']) && !is_array($table_info['cols'])) {
    echo 'Table info cols not defined' . PHP_EOL;
    exit(1);
}

// Create model class
$model = new Zend_CodeGenerator_Php_Class;
$model_name = 'Model_' . ucfirst($opts['s']);
echo "Creating $model_name" . PHP_EOL;

$cols = array();
foreach ($table_info['cols'] as $col) {
    $cols[$col] = null;
}
$model_docblock = new Zend_CodeGenerator_Php_Docblock(array(
    'tags' => array(array(
        'name'        => 'package',
        'description' => $model_name
    ))
));
$model->setName($model_name)
    ->setExtendedClass('Pet_Model_Abstract')
    ->setDocblock($model_docblock)
    ->setProperties(array(array(
        'name'         => '_data',
        'visibility'   => 'public',
        'defaultValue' => $cols
    )));

// Create model file
$model_file = new Zend_CodeGenerator_Php_File;
$model_file->setClass($model);
$model_path = APPLICATION_PATH . '/models/' . $opts['s'] . '.php';

echo "Creating $model_path" . PHP_EOL;
if (false === file_put_contents($model_path, $model_file->generate())) {
    echo "$model_path not created" . PHP_EOL;
    exit(1);
}

// Create Mapper class
$mapper_class = new Zend_CodeGenerator_Php_Class;
$mapper_name = 'Model_Mapper_' . $opts['p'];
echo "Creating $mapper_name" . PHP_EOL;

$mapper_docblock = new Zend_CodeGenerator_Php_Docblock(array(
    'tags' => array(array(
        'name'        => 'package',
        'description' => $mapper_name
    ))
));
$mapper_class->setName($mapper_name)
    ->setExtendedClass('Pet_Model_Mapper_Abstract')
    ->setDocblock($mapper_docblock);

// Create model file
$mapper_file = new Zend_CodeGenerator_Php_File;
$mapper_file->setClass($mapper_class);
$mapper_path = APPLICATION_PATH . '/models/Mapper/' .
    $opts['p'] . '.php';

echo "Creating $mapper_path" . PHP_EOL;
if (false === file_put_contents($mapper_path, $mapper_file->generate())) {
    echo "$mapper_path not created" . PHP_EOL;
    exit(1);
}

echo "Done!" . PHP_EOL;
exit(0);


