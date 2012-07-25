<?php
/**
 * @package Model_Mapper_UserNotes
 * 
 * For logging user activity
 * 
 */
class Model_Mapper_UserNotes extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_user_notes = new Model_DbTable_UserNotes;
    }
    
    /**
     * @param int $user_id
     * @return array
     * 
     */
    public function getByUserId($user_id) {
        $user_notes = $this->_user_notes->getByUserId($user_id); 
        $out = array();
        if ($user_notes) {
            foreach ($user_notes as $user_note) {
                $out[] = new Model_UserNote($user_note->toArray());
            }
        }
        return $out;
    }

    /**
     * @param array $data
     * @return int New user_note id
     * 
     */
    public function insert(array $data) {
        $user_note_model = new Model_UserNote($data);
        $user_note_model->date_created = date('Y-m-d H:i:s');
        $user_note = $user_note_model->toArray();
        return $this->_user_notes->insert($user_note);
    }
    
    /**
     * @param int $id
     * @return void
     * 
     */
    public function delete($id) {
        $where = $this->_user_notes->getAdapter()->quoteInto('id = ?', $id);
        //$this->_user_notes->delete($where);
    }
}

