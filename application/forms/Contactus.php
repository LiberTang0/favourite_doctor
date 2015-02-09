<?php

class Application_Form_Contactus extends Zend_Form {

    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );
    public $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    public function init() {

        $this->setMethod('post');
        $this->addElementPrefixPath('Base_Validate', 'Base/Validate/', 'validate');



        // Add an first name element
        $this->addElement('text', 'first_name', array(
            'label' => '',
            'class' => 'inputbox',
            'TABINDEX' => '1',
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[379])))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
		
		$this->addElement('text', 'last_name', array(
            'label' => '',
            'class' => 'inputbox',
            'TABINDEX' => '1',
            'size' => '30',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[386])))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'email', array(
            'label' => '',
            'TABINDEX' => '3',
            'size' => '30',
            'required' => true,
            'class' => 'inputbox',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim')
            
        ));


        // Add an last name element
        $this->addElement('text', 'subject', array(
            'label' => '',
            'class' => 'inputbox',
            'size' => '30',
            'TABINDEX' => '4',
            'required' => true,
            'decorators' => $this->elementDecorators,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[382])))
            ),
            'filters' => array('StringTrim'),
        ));
        // Add an last name element
        $this->addElement('textarea', 'enquiry', array(
            'label' => '',
            'rows' => 4,
            'cols' => 50,
            'class' => 'inputbox',
            'TABINDEX' => '5',
            'required' => true,
            'decorators' => $this->elementDecorators,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[380])))
            ),
            'filters' => array('StringTrim'),
        ));

        $this->addElement('checkbox', 'email_copy', array(
            'label' => '',
            'class' => 'inputbox',
            'value' => '0',
            'TABINDEX' => '6',
            'required' => true,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('submit', 'submit', array(
            'required' => false,
            'class' => 'send-btn',
            'TABINDEX' => '7',
            'ignore' => true,
            'label' => '',
            'decorators' => $this->buttonDecorators,
        ));
    }

    public function loadDefaultDecorators() {
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));
    }

}