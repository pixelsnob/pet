<?php

class Pet_Form_Decorator_Label extends Zend_Form_Decorator_Label {

    /**
     * @param string $content
     * @return string
     * 
     */
    public function render($content) {
        $this->setTag(null);
        return parent::render($content);
    }
}
