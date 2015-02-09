<?php

class Admin_Form_Doctor extends Zend_Form {

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
        $category = new Application_Model_Category();
        $arrcategory = $category->getCategories();



        $category_element = $this->addElement('Multiselect', 'category_id', array(
                    'label' => 'Select Category:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => true,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                ));

        $category_element = $this->addElement('Multiselect', 'category_id2', array(
                    'label' => 'Select Category:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                    'MultiOptions' => $arrcategory
                ));

        $category_element = $this->addElement('Multiselect', 'extra_category_id', array(
                    'label' => 'Select Extra Category:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                ));

        $category_element = $this->addElement('Multiselect', 'extra_category_id2', array(
                    'label' => 'Select Extra Category:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                    'MultiOptions' => $arrcategory
                ));
		
		$this->addElement('text', 'theUrl', array(
            'label' => 'Url Γιατρού:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '50',
            'maxlength' => '255',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
		
        $this->addElement('text', 'member_number', array(
            'label' => 'Member Number:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '50',
            'maxlength' => '255',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'fname', array(
            'label' => 'Title:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '50',
            'maxlength' => '255',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter Title')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

		
       $this->addElement('text', 'email', array(
            'label' => 'Title:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '50',
            'maxlength' => '255',
            'validators' => array(
                'EmailAddress'
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'username', array(
            'label' => 'Title:',
            'required' => true,
            'TABINDEX' => '1',
            // 'readonly' =>true,
            'class' => 'form',
            'size' => '50',
            'maxlength' => '255',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter Username')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'specialty_title', array(
            'label' => 'Title:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '100',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'company', array(
            'label' => 'Office Name:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '100',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('file', 'company_logo', array(
            'label' => 'Company Logo [400px/234px]:',
            'class' => 'form',
            'decorators' => $this->fileDecorators
        ))->getElement('company_logo')->addValidator('Extension', false, 'jpg,gif,jpeg,png');


        $this->addElement('textarea', 'associates', array(
            'label' => 'Associates:',
            'cols' => 50,
            'rows' => 15,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'assign_phone', array(
            'label' => 'Phone:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '20',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'actual_phone', array(
            'label' => 'Actual Phone:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'street', array(
            'label' => 'Street:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '40',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter street')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
		$this->addElement('text', 'area', array(
            'label' => 'Area',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '40',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'city', array(
            'label' => 'City:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '40',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter city')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'state', array(
            'label' => 'State:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'zipcode', array(
            'label' => 'Zip:',
            'required' => true,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('checkbox', 'use_zip', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'zipcode1', array(
            'label' => 'Zip:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('checkbox', 'use_zip1', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'zipcode3', array(
            'label' => 'Zip:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('checkbox', 'use_zip3', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'zipcode2', array(
            'label' => 'Zip:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('checkbox', 'use_zip2', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'zipcode4', array(
            'label' => 'Zip:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('checkbox', 'use_zip4', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'zipcode5', array(
            'label' => 'Zip:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must enter zip')))
            ),
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $this->addElement('checkbox', 'use_zip5', array(
            'label' => 'Featured:',
            //'TABINDEX'=>'20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'geocode', array(
            'label' => 'Zoom map (+) Click on your location:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '50',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'office_hour', array(
            'label' => 'Office Hours:',
            'cols' => 40,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('textarea', 'text_award', array(
            'label' => 'Text Award:',
            'cols' => 40,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('textarea', 'language', array(
            'label' => 'Language:',
            'required' => false,
            'TABINDEX' => '1',
            'cols' => 40,
            'rows' => 10,
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'education', array(
            'label' => 'Education/Training:',
            'cols' => 50,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'amenities', array(
            'label' => 'Amenities:',
            'cols' => 50,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('MultiCheckbox', 'payment_options', array(
            'multiOptions' => array('Visa' => 'Visa', 'MasterCard' => 'MasterCard', 'AmericanExpress' => 'AmericanExpress', 'Check' => 'Check', 'Cash' => 'Cash', 'Credit Card'=> 'Credit Card'),
            'label' => 'Payment Options:',
            'class' => 'form',
            'separator' => "&nbsp;&nbsp;",
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('textarea', 'discounts', array(
            'label' => 'Discounts:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => 40,
            'rows' => 10,
            'size' => '30',
            'maxlength' => '500',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'awards', array(
            'label' => 'Awards:',
            'cols' => 25,
            'rows' => 5,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'association', array(
            'label' => 'Association:',
            'cols' => 40,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('checkbox', 'featured', array(
            'label' => 'Featured:',
            'TABINDEX' => '20',
            'class' => 'form',
            'decorators' => $this->elementDecorators,
            'uncheckedValue' => '',
            'filters' => array('StringTrim'),
        ));
		$this->addElement('textarea', 'discounts', array(
            'label' => 'Discounts:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => 40,
            'rows' => 10,
            'size' => '30',
            'maxlength' => '500',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $membership = new Application_Model_Membership();
        $arrMembership = $membership->getMembership(null, "Select Membership");

        $this->addElement('select', 'membership_level', array(
            'label' => 'Membership Level:',
            'class' => 'select',
            'TABINDEX' => '6',
            'style' => 'width:150px;',
            'required' => true,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrMembership
        ));
        $this->addElement('text', 'years_practicing', array(
            'label' => 'Years practicing:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'years_at_practice', array(
            'label' => 'Years at practice:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'community_involvement', array(
            'label' => 'Community Involvement:',
            'cols' => 25,
            'rows' => 5,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'hobbies', array(
            'label' => 'Hobbies:',
            'cols' => 25,
            'rows' => 5,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'staff', array(
            'label' => 'Staff:',
            'cols' => 60,
            'rows' => 10,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'services', array(
            'label' => 'Services:',
            'cols' => 40,
            'rows' => 8,
            'class' => 'form',
            'TABINDEX' => '2',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'technology', array(
            'label' => 'Technology:',
            'required' => false,
            'TABINDEX' => '1',
            'cols' => 40,
            'rows' => 10,
            'class' => 'form',
            'size' => '100',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'brands', array(
            'label' => 'Brands:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'video', array(
            'label' => 'Video ID:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => '30',
            'rows' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
		$this->addElement('textarea', 'photos', array(
            'label' => 'Photos Embed Code:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => '30',
            'rows' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'special_needs', array(
            'label' => 'Special Needs:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '60',
            'maxlength' => '500',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'about', array(
            'label' => 'About:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => '60',
            'rows' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('textarea', 'testimonials', array(
            'label' => 'Testimonials:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'cols' => '30',
            'rows' => '5',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('text', 'clicktotalkurl', array(
            'label' => 'Click To Talk URL:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'county', array(
            'label' => 'County:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'gallery', array(
            'label' => 'Gallery:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '10',
            'maxlength' => '10',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $this->addElement('text', 'website', array(
            'label' => 'Website:',
            'required' => false,
            'TABINDEX' => '1',
            'class' => 'form',
            'size' => '30',
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));
        $insurancecompany = new Application_Model_InsuranceCompany();
        $arrInsurancecompany = $insurancecompany->getInsurancecompanies();

        $this->addElement('Multiselect', 'doctor_insurance', array(
            'label' => 'Select Insurance:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:250px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('Multiselect', 'doctor_insurance2', array(
            'label' => 'Select Insurance:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:250px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrInsurancecompany
        ));
      
        $ReasonForVisit = new Application_Model_ReasonForVisit();
        $arrReasonForVisit = $ReasonForVisit->getReasonForVisit();

        $this->addElement('Multiselect', 'doctor_reason_for_visit', array(
            'label' => 'Select Reason to Visit:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:250px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('Multiselect', 'doctor_reason_for_visit2', array(
            'label' => 'Select Reason to Visit:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:250px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));


        $Associations = new Application_Model_Association();
        $arrAssociations = $Associations->getAssociations();

        $this->addElement('Multiselect', 'doctor_association', array(
            'label' => 'Select Association:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:300px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim'),
        ));



        $this->addElement('Multiselect', 'doctor_association2', array(
            'label' => 'Select Association:',
            'class' => 'select',
            'TABINDEX' => '6',
            'multiple' => 'true',
            'style' => 'width:300px;',
            'size' => '10',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'filters' => array('StringTrim')
        ));

        $Awards = new Application_Model_Award();
        $arrAwards = $Awards->getAwards();


        $award_element = $this->addElement('Multiselect', 'doctor_award', array(
                    'label' => 'Select Award:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                        )
        );

        $award_element = $this->addElement('Multiselect', 'doctor_award2', array(
                    'label' => 'Select Award:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                    'MultiOptions' => $arrAwards
                        )
        );


        $Affiliation = new Application_Model_HospitalAffiliation();


        $arrAffiliation = array();
        $arrAffiliationstates = $Affiliation->GetAllStates();

        $this->addElement('select', 'state_for_affiliate', array(
            'label' => 'State:',
            'class' => 'select',
            'onchange' => 'getaffiliation(this.value)',
            'TABINDEX' => '6',
            'value' => 'AL',
            'style' => 'width:150px;',
            'required' => false,
            'decorators' => $this->elementDecorators,
            'validators' => array(
                array('NotEmpty', true, array('messages' => array('isEmpty' => 'You must select State')))
            ),
            'filters' => array('StringTrim'),
            'MultiOptions' => $arrAffiliationstates
        ));


        $award_element = $this->addElement('Multiselect', 'doctor_affiliation', array(
                    'label' => 'Select Affiliation:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
                    'MultiOptions' => $arrAffiliation
                        )
        );

        $assistants_element = $this->addElement('Multiselect', 'doctor_assistant', array(
                    'label' => 'Select Assistant:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
					'registerInArrayValidator' => false
					)
        );

        $assistants_element2 = $this->addElement('Multiselect', 'doctor_assistant2', array(
                    'label' => 'Select Assistant:',
                    'class' => 'select',
                    'TABINDEX' => '6',
                    'multiple' => 'true',
                    'style' => 'width:300px;',
                    'size' => '10',
                    'required' => false,
                    'decorators' => $this->elementDecorators,
                    'filters' => array('StringTrim'),
					'registerInArrayValidator' => false
				)
        );
		
		
		
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'class' => 'signup',
            'onclick' => 'submitdoctoredit_admin();selectAll(document.getElementById("doctor_assistant"));',
            'TABINDEX' => '20',
            'ignore' => true,
            'label' => 'Save',
            'decorators' => $this->buttonDecorators,
        ));

        $this->addElement('submit', 'apply', array(
            'required' => false,
            'class' => 'signup',
            'onclick' => 'submitdoctoredit_admin();selectAll(document.getElementById("doctor_assistant"));',
            'ignore' => true,
            'label' => 'Apply',
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