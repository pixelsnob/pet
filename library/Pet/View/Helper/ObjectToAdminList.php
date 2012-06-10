<?php

class Pet_View_Helper_ObjectToAdminList extends Zend_View_Helper_Abstract {
    
    /**
     * Turns an object into a <dl>
     * 
     * @param array $fields
     * @param array $data
     * 
     */
    public function objectToAdminList(array $fields, $data) {
        $out = "<dl class=\"admin-list\">\n";
        foreach ($fields as $k => $field) {
            $i = (!is_array($field) ? $field : $k);
            if (strlen(trim($data->$i))) {
                if (!is_array($field) || !isset($field['title'])) {
                    $title = str_replace('_', ' ', $i);
                    $title = ucwords($title);
                } else {
                    $title = $field['title'];
                }
                $format = (isset($field['format']) ? $field['format'] : null);
                switch ($format) {
                    case 'dollar':
                        $value = $this->view->escape(
                            $this->view->dollarFormat($data->$i));
                        break;
                    case 'datetime':
                        $date = new DateTime($data->$i);
                        $value = $date->format('M j, Y h:i:s a');
                        break;
                    case 'date':
                        $date = new DateTime($data->$i);
                        $value = $date->format('M j, Y');
                        break;
                    default:
                        $value = $data->$i;
                        if (is_array($field) && isset($field['callback'])) {
                            $value = $field['callback']($value);
                        }
                        break;
                }
                $out .= '<dt>' . $this->view->escape($title) . ":</dt>\n";
                $out .= "<dd>$value</dd>\n";
            }
        }
        $out .= "</dl>\n";
        return $out;
    }
}

