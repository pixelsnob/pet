<?php
/**
 * @package Model_Mapper_Courses
 * 
 */
class Model_Mapper_Courses extends Pet_Model_Mapper_Abstract {

    /**
     * @return void 
     * 
     */
    public function __construct() {
        $this->_courses = new Model_DbTable_Courses;
    }

    /**
     * @param array $data
     * @param int $product_id
     * @return void
     * 
     */
    public function updateByProductId($data, $product_id) {
        $course_model = new Model_Course($data);
        $course = $course_model->toArray();
        unset($course['id']);
        unset($course['product_id']);
        $this->_courses->updateByProductId($course, $product_id); 
    }

    /**
     * @param array $data
     * @return int product_id
     * 
     */
    function insert(array $data) {
        $course = new Model_Course($data);
        $course_array = $course->toArray();
        unset($course_array['id']);
        return $this->_courses->insert($course_array);
    }
}

