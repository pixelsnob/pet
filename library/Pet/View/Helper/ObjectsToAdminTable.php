<?php

class Pet_View_Helper_ObjectsToAdminTable extends Zend_View_Helper_Abstract {
    
    /**
     * Builds an admin table from a config array of fields and an array
     * of objects
     * 
     * @param array $fields An array of config arrays used for display: format, title, etc. 
     * @param array $data An array of data objects
     * @param array $params Request params to pass through in links, etc.
     * 
     */
    public function objectsToAdminTable(array $fields, array $data, array $params) {
        $out = "<table class=\"admin-table\">\n<tr>\n";
        foreach ($fields as $k => $field) {
            $qs = $params;
            $th_class = '';
            // Sort header stuff
            if (isset($params['sort']) && strlen(trim($params['sort']))) {
                $qs['sort_dir'] = 'asc';
                $qs['sort'] = $k;
                if (isset($params['sort_dir'])) {
                    if ($params['sort'] == $k) {
                        if ($params['sort_dir'] == 'asc') {
                            $qs['sort_dir'] = 'desc';
                        } else {
                            $qs['sort_dir'] = 'asc';
                        }
                        $th_class = ' class="sort-by"';
                    }
                }
            } else {
                $qs['sort_dir'] = 'asc';
                $qs['sort'] = $k;
            }
            $title = (isset($field['title']) ?
                $this->view->escape($field['title']) : '');
            $out .= "<th{$th_class}><a href=\"?" . $this->view->escape(
                http_build_query($qs)) . "\">$title</a></th>\n";
        }
        $out .= "</tr>\n";
        foreach ($data as $row) {
            $out .= "<tr>\n";
            foreach ($fields as $k => $field) {
                $format = (isset($field['format']) ? $field['format'] : null);
                switch ($format) {
                    case 'dollar':
                        $value = $this->view->escape(
                            $this->view->dollarFormat($row->$k));
                        break;
                    case 'datetime':
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
                $class = ($format ? " class=\"$format\"" : '');
                $out .= "<td{$class}>" . $value . "</td>\n";
            }
            $out .= "</tr>\n";
        }
        $out .= '</table>';
        return $out;
    }
}

