<?php
/**
 * @package Model_Mapper_UserProfiles
 * 
 */
class Model_Mapper_UserProfiles extends Pet_Model_Mapper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_profiles = new Model_DbTable_UserProfiles;
    }

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
     * @return int user_id
     * 
     */
    public function insert(array $data) {
        $profile = new Model_UserProfile($data);
        $profile_array = $profile->toArray();
        unset($profile_array['id']);
        return $this->_profiles->insert($profile_array);
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

