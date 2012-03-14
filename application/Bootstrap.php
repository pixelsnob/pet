<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    protected function _initAutoload() {
        $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
        // Override default mappers resource, which wants to use models/mappers:
        // using models/Mapper for consistency with others like models/DbTable, etc.
        $autoloader->addResourceType('mappers', 'models/Mapper', 'Model_Mapper');
        return $autoloader;
    }


}

