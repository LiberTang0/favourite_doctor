<?php

class Application_Form_Forgot extends Zend_Form {

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


        $this->addElement('text', 'email', array(
            'label' => 'Your email address',
            'TABINDEX' => '1',
            'required' => true,
            'class' => 'inputbox',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[383]))),
                array('EmailAddress', true),
                array('Db_RecordExists', true, array(
                        'table' => 'user',
                        'field' => 'email',
                        'messages' => $this->view->lang[387]
                ))
            )
        ));

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'TABINDEX' => '20',
            'ignore' => true,
            'label' => 'submit',
            'class' => 'email-my-password',
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