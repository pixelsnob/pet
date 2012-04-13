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
    public function flashMessenger($namespace, $current = false) {
        $fm = new Pet_FlashMessenger;
        $fm->setNamespace($namespace);
        //$messages = $fm->getMessages();
        //$messages = ($current ? ($messages + $fm->getCurrentMessages()) :
        //    $messages);
        $messages = ($current ? $fm->getCurrentMessages() : $fm->getMessages());
        //$messages = array_unique($messages);
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
