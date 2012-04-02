<?php
/**
 * Outputs HTML formatted flash messages
 * 
 * @package Pet_View_Helper_FlashMessenger
 * 
 */
class Pet_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract {
    
    /**
     * @return void
     * 
     */
    public function flashMessenger($current = false) {
        $fc = Zend_Controller_Front::getInstance();
        $fm = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'FlashMessenger');
        $messages = ($current ? $fm->getCurrentMessages() : $fm->getMessages());
        if (count($messages) > 1) {
            $out = '<ul class="flash-message">';
            foreach ($messages as $message) {
                $out .= sprintf('<li>%s<li>', $this->view->escape($message));
            }
            $out .= '</ul>';
            return $out;
        } elseif (count($messages) == 1) {
            return sprintf('<p class="flash-message">%s</p>',
                $this->view->escape($messages[0]));
        }
    }
}
