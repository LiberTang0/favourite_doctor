<?php

class User_Form_Payment extends Zend_Form {

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
    public $fileDecorators = array(
        array('File'),
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    public function init() {

        $this->setMethod('post');
 
       $this->addElement('MultiCheckbox','payment_options',array(
			'multiOptions' => array('Visa' => 'Visa','MasterCard' => 'MasterCard','AmericanExpress' => 'AmericanExpress', 'Check'=>'Check', 'Cash'=>'Cash', 'Credit Card'=>'Credit Card'),
			'label' => 'Payment Options:',
			'class'=>'formcheck',
				'separator'=>"&nbsp;&nbsp;",
			'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),

		));

          $this->addElement('textarea', 'discounts', array(
            'label' => 'Discounts:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
             'cols' => 75,
            'rows' => 5,
	    'size' => '30',
	    'maxlength' => '500',
           'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

       $insurancecompany=new Application_Model_InsuranceCompany();
        $arrInsurancecompany=$insurancecompany->getInsurancecompanies();

        $this->addElement('Multiselect', 'doctor_insurance',array(
            'label'      => 'Select Insurance:',
        	'class' =>'select',
        	'TABINDEX'=>'6',
			'multiple'=>'true',
			 'style' => 'width:250px;',
			'size' => '10',
		   	'required'   => false,
        	'decorators' => $this->elementDecorators,
            'filters'    => array('StringTrim'),


        ));

        $this->addElement('Multiselect', 'doctor_insurance2',array(
            'label'      => 'Select Insurance:',
        	'class' =>'select',
        	'TABINDEX'=>'6',
			'multiple'=>'true',
			 'style' => 'width:250px;',
			'size' => '10',
		   	'required'   => false,
        	'decorators' => $this->elementDecorators,
            'filters'    => array('StringTrim'),
        	'MultiOptions'=>$arrInsurancecompany

        ));

		
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'class' => 'save',
            'TABINDEX' => '20',
            'ignore' => true,
            'onclick'=>"selectAll(document.getElementById('doctor_insurance'));",
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