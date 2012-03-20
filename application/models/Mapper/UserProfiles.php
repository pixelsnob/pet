<?php
/**
 * @package Model_Mapper_UserProfiles
 * 
 */
class Model_Mapper_UserProfiles extends Pet_Model_Mapper_Abstract {

    public function getByUserId($id) {
        $profiles = new Model_DbTable_UserProfiles;
        $profile = $profiles->getByUserId($id);
        if ($profile) {
            return new Model_UserProfile($profile->toArray());
        }
    }
}

