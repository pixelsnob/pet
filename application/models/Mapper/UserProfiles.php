<?php
/**
 * @package Model_Mapper_UserProfiles
 * 
 */
class Model_Mapper_UserProfiles extends Pet_Model_Mapper_Abstract {
    
    /**
     * @param int $user_id
     * @return void|Model_UserProfile
     * 
     */
    public function getByUserId($user_id) {
        $profiles = new Model_DbTable_UserProfiles;
        $profile = $profiles->getByUserId($user_id);
        if ($profile) {
            return new Model_UserProfile($profile->toArray());
        }
    }
    
    /**
     * @param array $data
     * @param int $user_id
     * @return Num rows updated
     * 
     */
    public function updateByUserId(array $data, $user_id) {
        $profiles = new Model_DbTable_UserProfiles;
        $profile = new Model_UserProfile($data);
        $profile = $profile->toArray();
        unset($profile['user_id']);
        unset($profile['id']);
        return $profiles->updateByUserId($profile, $user_id);
    }
}

