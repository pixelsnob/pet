<?php

class Pet_View_Helper_AdminTable extends Zend_View_Helper_Abstract {
    
    /**
     * Builds an admin table from a config array of fields and an array
     * of objects
     * 
     * @param array $fields
     * @param array $data
     * 
     */
    public function adminTable(array $fields, array $data) {
        $out = "<table class=\"admin-table\">\n<tr>\n";
        foreach ($fields as $field) {
            $title = (isset($field['title']) ?
                $this->view->escape($field['title']) : '');
            $out .= "<th>$title</th>\n";
        }
        $out .= "</tr>\n";
        foreach ($data as $row) {
            $out .= "<tr>\n";
            foreach ($fields as $k => $field) {
                $type = (isset($field['type']) ? $field['type'] : null);
                switch ($type) {
                    case 'dollar':
                        $value = $this->view->escape(
                            $this->view->dollarFormat($row->$k));
                        break;
                    case 'date':
                        $date = new DateTime($row->$k);
                        $value = $date->format('M j, Y h:i a');
                        break;
                    // Pass url as /orders/detail/%id%
                    case 'edit':
                    case 'view':
                    case 'delete':
                        preg_match('/%([^%]*)%/', $field['url'], $m);
                        if (isset($m[1]) && isset($row->{$m[1]})) {
                            $id = $row->{$m[1]};
                            $url = str_replace($m[0], $id, $field['url']);
                            $url = $this->view->escape($url);
                            $label = (isset($field['label']) ?
                                $field['label'] : null);
                            $label = $this->view->escape($label);
                            $value = "<a href=\"$url\">$label</a>\n";
                        }
                        break;
                    default:
                        $value = $this->view->escape($row->$k);
                        break;
                }
                $class = ($type ? " class=\"$type\"" : '');
                $out .= "<td{$class}>" . $value . "</td>\n";
            }
            $out .= "</tr>\n";
        }
        $out .= '</table>';
        return $out;
    }
}

