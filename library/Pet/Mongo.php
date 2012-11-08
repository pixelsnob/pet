<?php
/**
 * Very simple singleton class for connecting to Mongo
 * 
 * @package Pet_Mongo
 * 
 */
class Pet_Mongo {
    
    /**
     * @var Mongo A Mongo instance
     * 
     */
    private static $_instance;

    /**
     * @var string A Mongo connection URI
     * 
     */
    private static $_connection_uri;
    
    /**
     * @var string A DB to connect to
     * 
     */
    private static $_db = 'pet';

    /**
     * @param string $uri A mongo connection URI, such as mongodb://localhost
     * 
     */
    public static function setConnectionUri($uri) {
        self::$_connection_uri = $uri;    
    }

    /**
     * @param string $uri A DB to connect to
     * 
     */
    public static function setDb($db) {
        self::$_db = $db;    
    }

    /**
     * @return Mongo|void A mongo instance, or void if exception
     * @throws MongoConnectionException
     * 
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            try {
                self::$_instance = new Mongo(self::$_connection_uri);
                self::$_instance->connect();
                return self::$_instance->{self::$_db};
            } catch (MongoConnectionException $e) {
                $msg = 'Mongo connection failed!';
                $logger = Zend_Registry::get('log');
                $logger->log($msg . ' ' . __CLASS__ . '::' .
                    __FUNCTION__ . '()', Zend_Log::EMERG);
                throw new MongoConnectionException($msg);
            }
        } else {
            return self::$_instance->{self::$_db};
        }
    }
    
    /**
     * Prevent cloning
     * 
     */
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    
    /**
     * Prevent unserialize
     * 
     */
    public function __wakeup() {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
}
