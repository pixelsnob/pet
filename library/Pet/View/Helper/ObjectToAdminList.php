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
        $view = $this->view;
        $out = "<dl class=\"admin-list\">\n";
        foreach ($fields as $k => $field) {
            $value = '&nbsp;';
            $i = (!is_array($field) ? $field : $k);
            if (is_array($field) && isset($field['callback'])) {
                $value = $field['callback']($data);
            } elseif (isset($data->$i) && $data->$i) {
                $value = $data->$i;
            }
            if (!is_array($field) || !isset($field['title'])) {
                $title = str_replace('_', ' ', $i);
                $title = $view->escape(ucwords($title));
            } else {
                $title = $view->escape($field['title']);
            }
            if (is_array($field) && isset($field['format'])) {
                switch ($field['format']) {
                    case 'dollar':
                        $value = $view->dollarFormat($value);
                        break;
                    case 'datetime':
                        $date = new DateTime($data->$i);
                        $value = $date->format('M j, Y h:i:s a');
                        break;
                    case 'date':
                        $date = new DateTime($data->$i);
                        $value = $date->format('M j, Y');
                        break;
                }
            }
            $out .= "<dt>{$title}:</dt>\n";
            $out .= "<dd>{$value}</dd>\n";
        }
        $out .= "</dl>\n";
        return $out;
    }
}

