<?php

class Application_Form_Patientregistration extends Zend_Form {

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


        $this->addElement('text', 'first_name', array(
            'label' => 'First Name:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'preg-txt',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->lang[383])))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

         $this->addElement('text', 'last_name', array(
          'label' => 'Last Name:',
          'required' => true,
          'TABINDEX' => '2',
          'class' => 'preg-txt',
          'validators' => array(
          array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[386])))
          ),
          'decorators' => $this->elementDecorators,
          'filters' => array('StringTrim'),
          )); 

        /* $this->addElement('text', 'birth_date', array(
          'label' => 'Date of Birth:',
          'required' => true,
          'TABINDEX' => '5',
          'class' => 'preg-txt',
          'readonly' => 'readonly',
          'validators' => array(
          array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter Date of Birth')))
          ),
          'decorators' => $this->elementDecorators,
          'filters' => array('StringTrim'),
          )); */
        $User = new Application_Model_User();
        $arrMonth = $User->listAllMonths();
        $arrDay = $User->listAllDates();
        $arrYear = $User->listAllYear();

        $this->addElement('select', 'month_dob', array(
            'label' => '',
            'class' => 'inputbox',
            'TABINDEX' => '4',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrMonth,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[388])))
            )
        ));

        $this->addElement('select', 'date_dob', array(
            'label' => '',
            'class' => 'inputbox',
            'TABINDEX' => '5',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrDay,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[389])))
            )
        ));
        $this->addElement('select', 'year_dob', array(
            'label' => '',
            'class' => 'inputbox',
            'TABINDEX' => '6',
            'required' => true,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrYear,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[390])))
            )
        ));


        /*$this->addElement('text', 'zipcode', array(
            'label' => 'Zipcode:',
            'required' => true,
            'TABINDEX' => '3',
            'class' => 'preg-txt',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter Zipcode')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));*/


        $this->addElement('text', 'phone', array(
            'label' => 'Phone:',
            'required' => true,
            'TABINDEX' => '7',
            'class' => 'preg-txt',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[382])))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $arrgender = Array('m' => "Male", 'f' => "Female");
        $this->addElement('select', 'gender', array(
            'label' => 'Gender:',
            'class' => 'form',
            'TABINDEX' => '3',
            'separator' => '&nbsp;',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'value' => 'm',
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrgender
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email:',
            'required' => true,
            'TABINDEX' => '8',
            'class' => 'preg-txt',
            'validators' => array(
                'EmailAddress',
                array('Db_NoRecordExists', true, array(
                        'table' => 'user',
                        'field' => 'email',
                        'messages' => $this->view->$lang[391]
                ))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('password', 'password', array(
                    'label' => '',
                    'autocomplete' => "off",
                    'required' => true,
                    'TABINDEX' => '9',
                    'class' => 'preg-txt',
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                        array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[392]))),
                        array('validator' => 'StringLength', 'options' => array(6, 20))
                    ),
                ))
                ->getElement('password')
                ->addValidator('IdenticalField', false, array('confirmPassword', 'Confirm Password'));
        ;



        // Add an password element
        $this->addElement('password', 'confirmPassword', array(
            'label' => '',
            'required' => true,
            'TABINDEX' => '10',
            'class' => 'preg-txt',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(6, 20))
            )
        ));

        $this->addElement('checkbox', 'terms', array(
            'label' => '',
            'class' => 'form',
            'TABINDEX' => '11',
            'separator' => '&nbsp;',
            'required' => false,
            'uncheckedValue' => '',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->$lang[393])))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));



        $this->addElement('submit', 'submit', array(
            'required' => false,
            'class' => 'preg-btn',
            'TABINDEX' => '12',
            'ignore' => true,
            'onclick'=> "return submitAppointmentForm()",
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

?>