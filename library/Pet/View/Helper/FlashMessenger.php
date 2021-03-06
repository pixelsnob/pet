<?php
/**
 * Outputs HTML formatted flash messages
 * 
 * @package Pet_View_Helper_FlashMessenger
 * 
 */
class Pet_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract {
    
    /**
     * @param array|null $messages
     * @return string
     * 
     */
    public function flashMessenger($messages = null) {
        $messages = (array) $messages;
        if (count($messages) > 1) {
            $out = '<ul class="flash-message">';
            foreach ($messages as $message) {
                $message = $this->view->escape($message);;
                $out .= "<li>$message</li>";
            }
            $out .= '</ul>';
            return $out;
        } elseif (count($messages) == 1) {
            return sprintf('<p class="flash-message">%s</p>',
                $this->view->escape($messages[0]));
        }
    }
}
