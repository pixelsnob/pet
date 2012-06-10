<?php

class Pet_View_Helper_ObjectsToAdminTable extends Zend_View_Helper_Abstract {
    
    /**
     * Builds an admin table from a config array of fields and an array
     * of objects
     * 
     * @param array $fields
     * @param array $data
     * 
     */
    public function objectsToAdminTable(array $fields, array $data, array $params) {
        $out = "<table class=\"admin-table\">\n<tr>\n";
        $sort_params = $params;
        unset($sort_params['sort']);
        foreach ($fields as $k => $field) {
            $title = (isset($field['title']) ?
                $this->view->escape($field['title']) : '');
            $out .= '<th><a href="?' . $this->view->escape(http_build_query($sort_params)) .
                "&sort=$k\">$title</a></th>\n";
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
                        if (isset($row->id) && $row->id) {
                            $url = $field['url'] . '/'. $row->id;
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

