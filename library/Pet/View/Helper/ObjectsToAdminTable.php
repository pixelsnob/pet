<?php

class Pet_View_Helper_ObjectsToAdminTable extends Zend_View_Helper_Abstract {
    
    /**
     * Builds an admin table from a config array of fields and an array
     * of objects
     * 
     * @param array $fields An array of config arrays used for display: format, title, etc.
     *                      If nothing is passed, all fields from $data are shown
     * @param array $data An array of data objects
     * @param array $params Request params to pass through in links, etc.
     * 
     */
    public function objectsToAdminTable(array $fields, array $data,
                                        array $params = array(),
                                        array $options = array()) {
        if (empty($data)) {
            return;
        }
        $view = $this->view;
        // If no fields were passed, use every field in $data
        if (empty($fields)) {
            $row = $data[0]->toArray();
            $fields = array_keys($row);
        }
        $out = "<table class=\"admin-table\">\n<tr>\n";
        foreach ($fields as $k => $field) {
            $qs = $params;
            $th_class = '';
            $i = (!is_array($field) ? $field : $k);
            // Sort header stuff
            if (isset($options['sortable']) && $options['sortable']) {
                if (isset($params['sort']) && strlen(trim($params['sort']))) {
                    $qs['sort_dir'] = 'asc';
                    $qs['sort'] = $i;
                    if (isset($params['sort_dir']) && $params['sort'] == $i) {
                        $qs['sort_dir'] = ($params['sort_dir'] == 'asc' ?
                            'desc' : 'asc');
                        $th_class = ' class="sort-by"';
                    }
                } else {
                    $qs['sort_dir'] = 'asc';
                    $qs['sort'] = $i;
                }
            }
            // Header titles/links
            if (is_array($field) && isset($field['format']) &&
                    $field['format'] == 'link') {
                $title = '';
            } elseif (!is_array($field) || !isset($field['title'])) {
                $title = str_replace('_', ' ', $i);
                $title = $view->escape(ucwords($title));
            } else {
                $title = $view->escape($field['title']);
            }
            if (isset($options['sortable']) && $options['sortable']) {
                $out .= "<th{$th_class}><a href=\"?" . $view->escape(
                    http_build_query($qs)) . "\">$title</a></th>\n";
            } else {
                $out .= "<th{$th_class}>" . $view->escape($title) . "</th>\n";

            }
        }
        $out .= "</tr>\n";
        // Table body
        foreach ($data as $row) {
            $out .= "<tr>\n";
            foreach ($fields as $k => $field) {
                $value = '';
                $format = (isset($field['format']) ? $field['format'] : null);
                // $k can be a string or array
                $i = (!is_array($field) ? $field : $k);
                // See if there's a callback to process
                if (is_array($field) && isset($field['callback'])) {
                    $value = $field['callback']($row);
                } elseif ($row->$i) {
                    $value = $row->$i;
                }
                // Value formatting
                if (is_array($field) && isset($field['format'])) {
                    switch ($format) {
                        case 'dollar':
                            $value = $view->escape(
                                $view->dollarFormat($row->$k));
                            break;
                        case 'datetime':
                            $date = new DateTime($row->$k);
                            $value = $date->format('M j, Y h:i a');
                            break;
                        case 'date':
                            $date = new DateTime($row->$k);
                            $value = $date->format('M j, Y');
                            break;
                        // Pass url as /orders/detail/%id%
                        case 'link':
                            if (isset($row->id) && $row->id) {
                                $url = $field['url'] . '/'. $row->id;
                                $url = $view->escape($url);
                                $label = (isset($field['label']) ?
                                    $field['label'] : null);
                                $label = $view->escape($label);
                                $value = "<a href=\"$url\">$label</a>\n";
                            }
                            break;
                        default:
                            $value = $view->escape($row->$k);
                            break;
                    }
                }
                $out .= "<td>" . $value . "</td>\n";
            }
            $out .= "</tr>\n";
        }
        $out .= '</table>';
        return $out;
    }
}

