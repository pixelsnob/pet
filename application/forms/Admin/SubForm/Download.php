<?php
/**
 * Admin download subform
 * 
 */
class Form_Admin_SubForm_Download extends Pet_Form_SubForm {
    
    /**
     * @var array
     * 
     */
    protected $_download_formats;

    /**
     * @param array $download_formats
     * @return void
     */
    public function setDownloadFormats($download_formats) {
        $this->_download_formats = $download_formats;
    }

    /** 
     * @return void
     * 
     */
    public function init() {
        parent::init();
        $df = array('' => 'Please select...');
        foreach ($this->_download_formats as $format) {
            $df[$format->id] = $format->name;
        }
        $this->addElement('select', 'download_format_id', array(
            'label'        => 'Download Format',
            'required'     => true,
            'multiOptions' => $df,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Download format is required'
                ))
            )
        ))->addElement('text', 'date', array(
            'label'        => 'Date',
            'required'     => true,
            'class'        => 'datepicker',
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Date is required'
                )),
                array('Date', true, array(
                    'messages' => 'Invalid date'
                ))
            )
        ))->addElement('text', 'path', array(
            'label'        => 'Path',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Path is required'
                ))
            )
        ))->addElement('text', 'size', array(
            'label'        => 'Size',
            'required'     => true,
            'validators'   => array(
                array('NotEmpty', true, array(
                    'messages' => 'Size is required'
                )),
                array('Digits', true, array(
                    'messages' => 'Size must be a number'
                ))
            )
        ))->addElement('checkbox', 'subscriber_only', array(
            'label'        => 'Subscriber Only',
            'required'     => false,
            'class'        => 'checkbox'
        ));
    }

}
