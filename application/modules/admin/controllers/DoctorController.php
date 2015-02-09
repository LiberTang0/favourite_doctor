<?php

class Admin_DoctorController extends Base_Controller_Action {

    /*public function preDispatch() {
        parent::preDispatch();
        ini_set('display_errors', 1);
    }*/
    public function indexAction() {
        
        
        $this->view->title = "Admin Panel- List Doctors";
        $this->view->headTitle("Admin Panel");

        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_Doctor();

		$this->view->aam = $settings->settingValue('aam_plugin');

        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $doctor_name = $this->_getParam("doctor_name");
        $doctor_name =  addslashes($doctor_name);
        $category_id = $this->_getParam("catid");
        $state = $this->_getParam("state");
        $scriteria = $this->_getParam("scriteria");
        $sorder = $this->_getParam("sorder");
        $mlevel = $this->_getParam("mlevel");
        
        $zip = $this->_getParam("zip");
        $strwhere_condition = '1=1';
        $where_condition = array();
		
		$usersNs = new Zend_Session_Namespace("members"); 
		$Usern = new Application_Model_User();
		$Usern = $Usern->fetchRow("id=$usersNs->userId");
		$Assistant = new Application_Model_Assistant();
		$Assistant = $Assistant->fetchRow("userid=$usersNs->userId");
		
        if (!empty($doctor_name))
            $where_condition[] = "d.fname LIKE '%" . $doctor_name . "%'";
        

        //if (empty($mlevel))$mlevel = 'Platinum';
        if (!empty($mlevel))
            $where_condition[] = "d. membership_level= '".$mlevel."'";

        if (!empty($category_id)){
            $where_condition[] = "dc.category_id =" . $category_id . "";
        }
        if(!empty($state))
            $where_condition[] = "d.state ='". $state . "'";

         if(!empty($zip))
            $where_condition[] = "d.zipcode ='". $zip . "'";
         
        if (count($where_condition) > 0)
            $strwhere_condition = implode(" and ", $where_condition);

        

        $db = Zend_Registry::get('db');
        if(empty($scriteria)) $scriteria="fname";
        if(empty($sorder)) 
            $sorder="asc";
        else
        {
            switch(strtolower($sorder))
            {
                case 'asc':
                   $sorder="desc";
                    break;
                case 'desc':
                    $sorder="asc";
                    break;
                default:
                    $sorder="asc";


            }
        }
        
        if($Usern->getUserLevelId() == 4){
            $select = $db->select()
                 ->distinct()
                 ->from(array('d' => 'doctors', 'a' => 'doctor_assistant'),
                        array('d.id', 'd.fname', 'd.user_id', 'd.state', 'd.zipcode', 'd.category_id', 'd.status', 'd.membership_level', 'd.member_number'))
                 ->join(array('dc' => 'doctor_categories'),
                        'd.id = dc.doctor_id',
                         array('doctor_id'))
                 ->join(array('a' => 'doctor_assistant'),
                        'a.assistant_id='.$Assistant->getId().' AND d.id = a.doctor_id ',
                         array('doctor_id as doc_id'))
                 ->where("$strwhere_condition")
                 ->order ("$scriteria $sorder");
        } else {
            $select = $db->select()
                 ->distinct()
                 ->from(array('d' => 'doctors', 'a' => 'doctor_assistant'),
                        array('d.id', 'd.fname', 'd.user_id', 'd.state', 'd.zipcode', 'd.category_id', 'd.status', 'd.membership_level', 'd.member_number'))
                 ->join(array('dc' => 'doctor_categories'),
                        'd.id = dc.doctor_id',
                         array('doctor_id'))
                 ->where("$strwhere_condition")
                 ->order ("$scriteria $sorder");
        }
		
        $pageObj = new Base_Paginator();

        $paginator = $pageObj->DbSelectPaginator($select, $page, $page_size);
		
        //prexit($paginator);


        $count = $pageObj->fetchNumRows();


        $this->view->total = $paginator->getCurrentItemCount();
        $this->view->paginator = $paginator;
        $this->view->sorder =$sorder;
        $this->view->scriteria = $scriteria;
        

        //echo "<pre>";print_r($paginator);exit;

        $this->view->search_text = $doctor_name;
        $this->view->state_text = $state;
        $this->view->zip_text = $zip;
        $this->view->mlevel = $mlevel;
        

        $objCategory = new Application_Model_Category();

        $all_cats = $objCategory->getCategories("status=1","--Select Speciality--");

        $this->view->all_cats = $all_cats;
        $this->view->category_id = $category_id;

        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function deleteimageAction()
    {
       
        $id = $this->_getParam("doctor_id");
     
        if(0<(int)$id)
        {
       
        
        $model = new Application_Model_Doctor();
        $object = $model->find($id);
        $path = "images/doctor_image/";
        $del_image = $path . $object->getCompanylogo();
        if (file_exists($del_image))unlink($del_image);
        $small_del_image = $path ."thumb1_". $object->getCompanylogo();;
        if (file_exists($small_del_image))unlink($small_del_image);

        $object->setCompanylogo("");
        $object->save();
        die("pz wait");
        }

    }
    public function currentassociationAction()
    {
        $this->_helper->layout->disableLayout();
        $ids= $this->_getParam("ids");
        if(empty($ids)) $ids=0;
        
       
        $this->view->ids = $ids;
    }
    public function currenthomeaffiliationAction()
    {
        $this->_helper->layout->disableLayout();
        $ids= $this->_getParam("ids");
        $stateid= $this->_getParam("stateid");
        if(empty($ids)) $ids=0;


        $this->view->ids = $ids;
        $this->view->stateid =$stateid;
    }
    public function currentreasontovisitAction()
    {
        $this->_helper->layout->disableLayout();
        $ids= $this->_getParam("ids");
        if(empty($ids)) $ids=0;


        $this->view->ids = $ids;
    }
    public function deleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $doctor_name = $this->_getParam("doctor_name");
        $state = $this->_getParam("state");
        $scriteria = $this->_getParam("scriteria");

        $sorder = $this->_getParam("sorder");
        $zip = $this->_getParam("zip");
        $mlevel = $this->_getParam("mlevel");
        $idArray = explode(',', $ids);
        $objModelDoctor = new Application_Model_Doctor();
        foreach ($idArray as $id) {
            $object = $objModelDoctor->find($id);
            $object->delete("id={$id}");
        }
        // delete after article delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'mlevel'=>$mlevel,'sorder'=>$sorder, 'msg' => $msg, 'page' => $page));
    }

    public function publishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $idArray = explode(',', $ids);
        $model = new Application_Model_Doctor();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus('1');
            $object->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id, 'page' => $page, 'msg' => $publish));
    }

     public function changelevelAction() {
        $ids = $this->_getParam('ids');
        $newlevel = $this->_getParam('member_level');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $membershipLevelNumber = array('Platinum' => 1, 'Gold' => 2, 'Silver' => 3, 'Bronze Premium' => 4, 'Bronze' => 5);
        $idArray = explode(',', $ids);
        $model = new Application_Model_Doctor();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setMembershipLevel($newlevel);
            $object->setMembershipLevelNo($membershipLevelNumber[$newlevel]);
            $object->save();
        }

        $publish = base64_encode("Membership Level changed successfully");
        $this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id, 'page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");

        $idArray = explode(',', $ids);
        $model = new Application_Model_Doctor();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus(0);
            $object->save();
        }

        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id, 'page' => $page, 'msg' => $publish));
    }

    public function hospitalaffiliateAction() {
        $this->_helper->layout->disableLayout();
        $state = $this->_getParam('val');
        $doctor_id = $this->_getParam('doctor_id');
        $model = new Application_Model_DoctorHospitalAffiliation();
        $arr_docAffiliate = $model->getMyHospitalAffiliate("doctor_id={$doctor_id}");
        if ($arr_docAffiliate) {
            $arkeys = array_keys($arr_docAffiliate);
            $str_affiliated_id = implode(",", $arkeys);
        }
        if (empty($str_affiliated_id))
            $str_affiliated_id = 0;

        $model = new Application_Model_HospitalAffiliation();
        $state_affiliation = $model->getAllAffiliation("state='{$state}' AND id not in($str_affiliated_id)");
        $this->view->affiliations = $state_affiliation;
    }
    public function LoadDocRegistrationData($id)
    {
        $options = array();
       if (0 < (int) $id) {
          $DoctorEnquiry = new Application_Model_DoctorEnquiry();
          $objDocEnquiry = $DoctorEnquiry->find($id);
          if(!empty($objDocEnquiry))
          {
              $_GET['doctor_name'] = $objDocEnquiry->getName();
              $options['zipcode'] = $objDocEnquiry->getZipcode();
              $options['email'] = "test.deve@gmail.com";
              $options['email'] = "test.deve@gmail.com";
              
              
          }
       }
       return $options;
    }
    public function addEditAction() {
    	  	
    	$id = $this->_getParam('id');
        $fromdocregistration = $this->_getParam('fromdocreg');
        if(!empty($fromdocregistration) && $fromdocregistration>0)
        {
          
           $id =0;

        }
        $membershipLevelNumber = array('Standard' => 1, 'Gold' => 2, 'Silver' => 3);

        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $state = $this->_getParam("state");
        $scriteria = $this->_getParam("scriteria");
        
        $sorder = $this->_getParam("sorder");
        $zip = $this->_getParam("zip");
        $mlevel = $this->_getParam("mlevel");
        $this->view->defaultAffiliateState = "IL";
        $form = new Admin_Form_Doctor();
        $form->setAttrib('enctype', 'multipart/form-data');
        $path = "images/doctor_image/";
       
        /*Searched parameteres for view page*/
        $this->view->doctor_name = $doctor_name;
        $this->view->category_id = $category_id;
        $this->view->state = $state;
        $this->view->scriteria = $scriteria;
        $this->view->sorder = $sorder;
        $this->view->mlevel = $mlevel;
        $this->view->zip = $zip;
       
        
        /*Searched parameter passing ends over here*/
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }

        $form->getElement('doctor_affiliation')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_award')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_association')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_reason_for_visit')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_insurance')->setRegisterInArrayValidator(false);
        $form->getElement('category_id')->setRegisterInArrayValidator(false);
        $form->getElement('extra_category_id')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_association2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_award2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_reason_for_visit2')->setRegisterInArrayValidator(false);

        $model = new Application_Model_Doctor();
        $category = new Application_Model_Category();
        $modelDoctorInsurance = new Application_Model_DoctorInsurance();
        $modelInsuranceCompany = new Application_Model_InsuranceCompany();
        $modelDoctorReasonForVisit = new Application_Model_DoctorReasonForVisit();
        $modelReasonForVisit = new Application_Model_ReasonForVisit();
        $modeldoctor_association = new Application_Model_DoctorAssociation();
        $modAssoc = new Application_Model_Association();
        $Awards = new Application_Model_Award();
        
        if (0 < (int) $id) {
           
           
            $object = $model->find($id);
            
            $company_logo= $object->getCompanyLogo();
            if(!empty($company_logo) && file_exists($path.$company_logo))
                $this->view->doctor_headshot = "/".$path.$company_logo;
            else
                $this->view->doctor_headshot = "";
           

            $user_id=$object->getUserId();
           
            if(!empty($user_id))
            {
                $objUser = new Application_Model_User();
                $user_info =$objUser->find($object->getUserId());
                if($user_info)
                {
                $username = $user_info->getUsername();
                $email = $user_info->getEmail();
                }
                
                if(!empty($username))
                        $form->getElement("username")->setValue($user_info->getUsername());

                if(!empty($email))
                    $form->getElement("email")->setValue($user_info->getEmail());
            }

            // added by Developer
            //if(!in_array($object->getMembershipLevel(), array('Platinum','Gold','Silver'))){
                //$form->getElement('email')->clearValidators();
                $form->getElement('email')->setRequired(false);
                $form->getElement('username')->setRequired(false);
            //}
           

            //Section Starts for Category
           
            
            $docCategory = new Application_Model_DoctorCategory();
            $selectedcategory = $docCategory->getDoctorCategories("doctor_id={$id}", null, 1);
            if (empty($selectedcategory))
                $selectedcategory = 0;
           
            
            

			$seoUrl = new Application_Model_SeoUrl();
          
            $arrallcategory = $category->getCategories("id not in ({$selectedcategory})");
            $form->getElement('category_id2')->setMultiOptions($arrallcategory);
            $onjdocCategory = $docCategory->getDoctorCategories("doctor_id={$id}");
            $form->getElement('category_id')->setMultiOptions($onjdocCategory);
            // Section ends for Category
          		  
            // for extra category
            $docExtraCategory = new Application_Model_DoctorExtraCategory();
            $selectedExtraCategory = $docExtraCategory->getDoctorCategories("doctor_id={$id}", null, 1);
            if (empty($selectedExtraCategory))
                $selectedExtraCategory = 0;
            $arrallextracategory = $category->getCategories("id not in ({$selectedExtraCategory})");
            $form->getElement('extra_category_id2')->setMultiOptions($arrallextracategory);
            $onjdocExtraCategory = $docExtraCategory->getDoctorCategories("doctor_id={$id}");
            $form->getElement('extra_category_id')->setMultiOptions($onjdocExtraCategory);
            
            $ArrDoctorInsurance = $modelDoctorInsurance->getDoctorinsuranceForDoctorEdit("doctor_id={$id}");
            
            
            $selectedinsureance = $modelDoctorInsurance->getDoctorinsuranceForDoctorEdit("doctor_id={$id}", null, 1);
            
            $form->getElement('doctor_insurance')->setMultiOptions($ArrDoctorInsurance);
            if (empty($selectedinsureance)){
                $selectedinsureance = 0;
            }
            $ArrallInsurance = $modelInsuranceCompany->getInsurancecompanies("id not in({$selectedinsureance})");
            $form->getElement('doctor_insurance2')->setMultiOptions($ArrallInsurance);
            
            
            $ArrDoctorReasonForVisit = $modelDoctorReasonForVisit->getDoctorReasonForVisitForDoctorEdit("doctor_id={$id}");
            $form->getElement('doctor_reason_for_visit')->setMultiOptions($ArrDoctorReasonForVisit);
            $selectedreason = $modelDoctorReasonForVisit->getDoctorReasonForVisitForDoctorEdit("doctor_id={$id}", null, 1);
            if (empty($selectedreason))
                $selectedreason = 0;

           

            //For all association it should not conatain that is alredy selected
            
            $ArrallDoctorReasonForVisit = $modelReasonForVisit->getReasonForVisit("id not in({$selectedreason}) and category_id in ({$selectedcategory})");
            $form->getElement('doctor_reason_for_visit2')->setMultiOptions($ArrallDoctorReasonForVisit);
           //Association section starts over here
          
            
            $ArrDoctorAssociation = $modeldoctor_association->getDoctorAssociationForDoctorEdit("doctor_id={$id} ");
            $selectedassoc = $modeldoctor_association->getDoctorAssociationForDoctorEdit("doctor_id={$id}", null, 1);
            if (empty($selectedassoc))
                $selectedassoc = 0;
           
            //On Edit page we should only those association that is for same category that is selected by doctor
            
          
            $ArrallDoctorAssociation = $modAssoc ->getAssociations("id not in ({$selectedassoc}) AND category_id in ({$selectedcategory})");
            
            
            //For all association it should not conatain that is alredy selecte
            
            $form->getElement('doctor_association')->setMultiOptions($ArrDoctorAssociation);
            $form->getElement('doctor_association2')->setMultiOptions($ArrallDoctorAssociation);

            
            $modelDoctorAward = new Application_Model_DoctorAward();
            

            $arrall_awards = $modelDoctorAward->getMyAwardsForDoctorEdit("doctor_id={$id}");
            $strall_awards = $modelDoctorAward->getMyAwardsForDoctorEdit("doctor_id={$id}", 1);
            if (empty($strall_awards))
                $strall_awards = 0;

          
            $arrAwards = $Awards->getAwards("id not in ({$strall_awards})");

            $form->getElement('doctor_award2')->setMultiOptions($arrAwards);
            $form->getElement('doctor_award')->setMultiOptions($arrall_awards);

            $modelDoctorHospitalAffiliation = new Application_Model_DoctorHospitalAffiliation();
            $arrall_affiliation = $modelDoctorHospitalAffiliation->getMyHospitalAffiliate("doctor_id={$id}");
            $form->getElement('doctor_affiliation')->setMultiOptions($arrall_affiliation);
           
		   
			/* Assistants */
		/* 	//find all assistants for this doctor
			$docAssist = new Application_Model_DoctorAssistant();
            $selectedassist = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$id}", null);
            if (empty($selectedassist))
                $selectedassist = 0;
		  
			//find all non selected assistants
			$assistant = new Application_Model_Assistant();
			$arrallassist = $assistant->getAssistants("id not in ({$selectedassist})");
            $form->getElement('doctor_assistant2')->setMultiOptions($arrallassist);
            $onjdocAssist = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$id}", null);
            $form->getElement('doctor_assistant')->setMultiOptions($onjdocAssist); */
			
			$docAssist = new Application_Model_DoctorAssistant();
			$ArrDoctorAssistant = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$id} ");
            $selectedassist = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$id}", null, 1);
            if (empty($selectedassist))
                $selectedassist = 0;
           
            //On Edit page we should only those association that is for same category that is selected by doctor
            
			$Assistant = new Application_Model_Assistant();
            $ArrallDoctorAssist = $Assistant ->getAssistants("id not in ({$selectedassist})");
            
            
            //For all association it should not conatain that is alredy selecte
            
            $form->getElement('doctor_assistant')->setMultiOptions($ArrDoctorAssistant);
            $form->getElement('doctor_assistant2')->setMultiOptions($ArrallDoctorAssist);
			
			
            //$form->getElement('company_icon')->setRegisterInArrayValidator(false);


            $options['id'] = $id;
            $options['user_id'] = stripcslashes($object->getUserId());
            $options['member_number'] = stripcslashes($object->getMemberNumber());
            $options['category_id'] = stripcslashes($object->getCategoryId());
            $options['fname'] = stripcslashes($object->getFname());
			$options['theUrl'] = substr(stripcslashes($seoUrl->retrieveSeoUrl('/profile/index/id/' . $object->getId())), 1);
            $options['company'] = stripcslashes($object->getCompany());
            $options['street'] = stripcslashes($object->getStreet());
            $options['zipcode'] = stripcslashes($object->getZipcode());
            $options['zipcode1'] = stripcslashes($object->getZipcode1());
            $options['zipcode2'] = stripcslashes($object->getZipcode2());
            $options['zipcode3'] = stripcslashes($object->getZipcode3());
            $options['zipcode4'] = stripcslashes($object->getZipcode4());
            $options['zipcode5'] = stripcslashes($object->getZipcode5());
            //$options['assistantid'] = stripcslashes($object->getAssistantid());
            $options['city'] = stripcslashes($object->getCity());
            $options['country'] = stripcslashes($object->getCountry());
			$options['area'] = stripcslashes($object->getArea());
            $options['office_hour'] = stripcslashes($object->getOfficeHours());
            $options['education'] = stripcslashes($object->getEducation());
            $options['creditlines'] = stripcslashes($object->getCreditlines());
            $options['associates'] = stripcslashes($object->getAssociates());
            $options['assign_phone'] = stripcslashes($object->getAssignPhone());
            $options['actual_phone'] = stripcslashes($object->getActualPhone());
			$options['awards'] = stripcslashes($object->getAwards());
            $options['about'] = stripslashes($object->getAbout());
            $options['amenities'] = stripcslashes($object->getAmenities());
            $options['text_award'] = stripcslashes($object->getTextAward());
           
            if($object->getState()!="")
            $form->getElement('state_for_affiliate')->setValue($object->getState());
            if (count($ArrDoctorInsurance) > 0) {
                $form->getElement('doctor_insurance')->setValue($ArrDoctorInsurance);
            }
            if (count($ArrDoctorReasonForVisit) > 0) {
                $form->getElement('doctor_reason_for_visit')->setValue($ArrDoctorReasonForVisit);
            }
           
           // die();
            $options['payment_options'] = stripcslashes($object->getPaymentOptions());
            $options['insurance_accepted'] = stripcslashes($object->getInsuranceAccepted());
            $options['office'] = stripcslashes($object->getOffice());
            $options['language'] = stripcslashes($object->getLanguage());
            $options['association'] = stripcslashes($object->getAssociation());
            $options['featured'] = stripcslashes($object->getFeatured());
            $options['geocode'] = stripcslashes($object->getGeocode());
            $options['membership_level'] = stripcslashes($object->getMembershipLevel());
            $options['years_at_practice'] = stripcslashes($object->getYearsAtPractice());
            $options['years_practicing'] = stripcslashes($object->getYearsPractice());
            $options['community_involvement'] = stripcslashes($object->getCommunityInvolvement());
            $options['hobbies'] = stripcslashes($object->getHobbies());
            $options['staff'] = stripcslashes($object->getStaff());
            $options['services'] = stripcslashes($object->getServices());
            $options['technology'] = stripcslashes($object->getTechnology());
            $options['brands'] = stripcslashes($object->getBrands());
            $options['video'] = stripslashes($object->getVideo());
			$options['photos'] = stripslashes($object->getPhotos());
			$options['specialty_title'] = stripslashes($object->getSpecialtyTitle());
			$options['area'] = stripslashes($object->getArea());
            $options['special_needs'] = stripcslashes($object->getSpecialNeeds());
            $options['testimonials'] = stripcslashes($object->getTestimonials());

            $options['state'] = stripcslashes($object->getState());
            $options['gallery'] = stripcslashes($object->getGallery());
            $options['clicktotalkurl'] = stripcslashes($object->getClicktotalkurl());
            $options['county'] = stripcslashes($object->getCounty());
            $options['website'] = str_replace('http://', '', stripcslashes($object->getWebsite()));
            
            $options['use_zip'] = stripcslashes($object->getUseZip());
            $options['use_zip1'] = stripcslashes($object->getUseZip1());
            $options['use_zip2'] = stripcslashes($object->getUseZip2());
            $options['use_zip3'] = stripcslashes($object->getUseZip3());
            $options['use_zip4'] = stripcslashes($object->getUseZip4());
            $options['use_zip5'] = stripcslashes($object->getUseZip5());
            
            if($options['use_zip'] == 1){
          		$options['use_zip'] = 0;
          	}else{
          		$options['use_zip'] = 1;
          	}

          	if($options['use_zip1'] == 1){
          		$options['use_zip1'] = 0;
          	}else{
          		$options['use_zip1'] = 1;
          	}
          	
        	if($options['use_zip2'] == 1){
          		$options['use_zip2'] = 0;
          	}else{
          		$options['use_zip2'] = 1;
          	}
          	
        	if($options['use_zip3'] == 1){
          		$options['use_zip3'] = 0;
          	}else{
          		$options['use_zip3'] = 1;
          	}
        	
          	if($options['use_zip4'] == 1){
          		$options['use_zip4'] = 0;
          	}else{
          		$options['use_zip4'] = 1;
          	}          	
          	
        	if($options['use_zip5'] == 1){
          		$options['use_zip5'] = 0;
          	}else{
          		$options['use_zip5'] = 1;
          	}   
            

            $form->populate($options);
           	
            if ($object->getPaymentOptions() != '') {
                $paymentOptionArray = explode(",", $object->getPaymentOptions());
				$form->getElement('payment_options')->setValue(explode(',',$object->getPaymentOptions()));
            }
             
        }
        $request = $this->getRequest();
		$options = $request->getPost();

		if(!empty($fromdocregistration) && $fromdocregistration>0) {
           $options = $this->LoadDocRegistrationData($id);
           $form->populate($options);
           $id =0;
        }
        
                   
        if ($request->isPost()) {
            
          	if($options['use_zip'] == 1){
          		$options['use_zip'] = 0;
          	}else{
          		$options['use_zip'] = 1;
          	}

          	if($options['use_zip1'] == 1){
          		$options['use_zip1'] = 0;
          	}else{
          		$options['use_zip1'] = 1;
          	}
          	
        	if($options['use_zip2'] == 1){
          		$options['use_zip2'] = 0;
          	}else{
          		$options['use_zip2'] = 1;
          	}
          	
        	if($options['use_zip3'] == 1){
          		$options['use_zip3'] = 0;
          	}else{
          		$options['use_zip3'] = 1;
          	}
        	
          	if($options['use_zip4'] == 1){
          		$options['use_zip4'] = 0;
          	}else{
          		$options['use_zip4'] = 1;
          	}          	
          	
        	if($options['use_zip5'] == 1){
          		$options['use_zip5'] = 0;
          	}else{
          		$options['use_zip5'] = 1;
          	}           	
           
           //echo "<pre>";print_r($options);exit;
        
			$emailvalid=0;
			$model_User =  new Application_Model_User();
			//validating before updation of username and email
			if(empty($id)) $id=0;
            
            $objDoctorUser = $model->find($id);
            if(!empty($objDoctorUser))
				$user_id = $objDoctorUser->getUserId();
             
            if(empty($user_id))
                $user_id=0;
            //Make sure this entry is also available in user table
			$objUser =  $model_User->find($user_id);
			if(!$objUser)
               $user_id=0;
            $Erremail= array();
            $emailoptions = $options['email'];
            $usernameoptions =$options['username'];
            if(!empty($options['email'])) {
				$users = $model_User->fetchAll("email='".$options['email']."' AND id NOT in (".$user_id.")");
				if(count($users)>0) {
					$Erremail[]="This email already exists.";
					$form->getElement('email')->addErrorMessages($Erremail);
					$emailvalid=1;
                    $options['email'] = '';
               } else {
                   $emailvalid=0;
               }
            } else {
				//if user is already registered properly with us then we email address should not be blank
                if($user_id>0 && empty($options['email'])) {
                    $emailoptions = $options['email'];
                    //$form->setErrorMessages(array("email"=>"Please Enter Email address"));
                    $Erremail[]="Please Enter Email address";
					$form->getElement('email')->addErrorMessages($Erremail);
					$options['email'] = '';
					$emailvalid=1;
                } else
                    $emailvalid=0;
            }
            //Now validation starts for username
            if(!empty($options['username']))
            {

               $users_uname = $model_User->fetchAll("username='".$options['username']."' AND id NOT in (".$user_id.")");
               if(count($users_uname)>0)
               {

                  
                   $Erruname[]="This username already exists.";

                   $form->getElement('username')->addErrorMessages($Erruname);
                   
                    $options['username'] = '';

               }
            }
            else
            {
            //if user is already registered properly with us then we email address should not be blank
                if($user_id>0 && empty($options['username']))
                {
                    
                    
                    $Erruname[]="Please Enter Username";
                     $form->getElement('username')->addErrorMessages($Erruname);
                     $options['username'] = '';


                   
                }
                
            }
      
            if ($form->isValid($options)) {
               

                if(!empty($user_id)){

                
                $User = new Application_Model_User();
                $userObject = $User->find($user_id);
                    if($userObject){
                        if((isset($options['email']) && $options['email']!='') && (isset($options['username'])&&$options['username']!='')){
                            $userObject->setEmail($options['email']);
                            $userObject->setUsername($options['username']);
                            $userObject->save();
                        }
                    }
                }else{
                    
                    if((isset($options['email']) && $options['email']!='') && (isset($options['username'])&&$options['username']!='')){
                        $User1 = new Application_Model_User();
                        $User1->setFirstName($options['fname']);
                        $User1->SetLastName("");
                        $User1->setPassword(md5('dihnew'));
                        $User1->setUserLevelId(2);
                        $User1->setLastVisitDate(time());
                        $User1->setStatus("active");
                        $User1->setEmail($options['email']);
                        $User1->setUsername($options['username']);
                        $user_id = $User1->save();
                    }
                }
                

                $msg = base64_encode("Record has been saved successfully!");
                //if (0 < (int) $id) {
                    if(!is_object($object)) //If doctor is not already available then create new doctor. It comes only here for new doctor case
                    {
                        //$object =$model;
                        $object =new Application_Model_Doctor();
                        // Developer commented $object->setUserId($user_id);
                    }
                    $options['id'] = $id;
                    if($user_id > 0)$object->setUserId($user_id); // Developer added
                    $object->setCategoryId($options['category_id']);
                   
                    $object->setMemberNumber($options['member_number']); // add Developer
                    $object->setFname($options['fname']);
                    $object->setCompany($options['company']);
                    /* ------------------END COMPANY LOGO ------------------ */

                    $upload = new Zend_File_Transfer_Adapter_Http();
                   
                    $upload->setDestination($path);
                    try {
                        $upload->receive();
                    } catch (Zend_File_Transfer_Exception $e) {
                        $e->getMessage();
                    }
                    //        echo "<pre>";print_r($upload->getFileName('logo'));exit;
                    $upload->setOptions(array('useByteString' => false));
                    $file_name = $upload->getFileName('company_logo');
                    if (!empty($file_name)) {
                        $imageArray = explode(".", $file_name);
                        $ext = strtolower($imageArray[count($imageArray) - 1]);
                        $target_file_name = "doc_" . time() . ".{$ext}";
                        
                        $targetPath = $path . $target_file_name;
                       

                        $filterFileRename = new Zend_Filter_File_Rename(array('target' => $targetPath, 'overwrite' => true));
                        

                        $filterFileRename->filter($file_name);
                        /* ------------------ THUMB --------------------------- */
                        $image_name = $target_file_name;
                        $newImage = $path . $image_name;

                        $thumb = Base_Image_PhpThumbFactory ::create($targetPath);
                        $thumb->resize(400, 234);
                        $thumb->save($newImage);

                        

                       
                        if (0 < (int) $id) {
                            $del_image = $path . $object->getCompanylogo();
                            
                            if (file_exists($del_image))unlink($del_image);
                            $small_del_image = $path ."thumb1_". $object->getCompanylogo();;
                            if (file_exists($small_del_image))unlink($small_del_image);
                            
                            $object->setCompanylogo($image_name);
                        }else {
                            $options['company_logo'] = $image_name;
                        }
                      



                        /* ------------------ END THUMB ------------------------ */
                    }
                    /* ------------------END COMPANY LOGO ------------------ */


                    $object->setTextAward($options['text_award']);
                    $object->setStreet($options['street']);
                    $object->setZipcode($options['zipcode']);
                    $object->setCity($options['city']);
                    
                    $object->setOfficeHours($options['office_hour']);
                    $object->setEducation($options['education']);
                    
                    $object->setAssociates($options['associates']);
                    $object->setAssignPhone($options['assign_phone']);
                    $object->setActualPhone($options['actual_phone']);
                    $object->setAwards($options['awards']);
                    $object->setAbout($options['about']);
                    $object->setAmenities($options['amenities']);
              
                    if (count($options['payment_options']) > 0) {
                        $object->setPaymentOptions(implode(",", $options['payment_options']));
                    } else {
                        $object->setPaymentOptions('');
                    }
                    $object->setInsuranceAccepted($options['insurance_accepted']);
                    
                    $object->setLanguage($options['language']);
                    $object->setAssociation($options['association']);
                    $object->setFeatured($options['featured']);
                    $object->setGeocode($options['geocode']);
                    $object->setMembershipLevel($options['membership_level']);
                    $object->setMembershipLevelNo($membershipLevelNumber[$options['membership_level']]);
                   
                    $object->setCommunityInvolvement($options['community_involvement']);
                    $object->setHobbies($options['hobbies']);
                    $object->setStaff($options['staff']);
                    $object->setServices($options['services']);
                    $object->setTechnology($options['technology']);
                    $object->setZipcode1($options['zipcode1']);
                    $object->setZipcode2($options['zipcode2']);
                    $object->setZipcode3($options['zipcode3']);
                    $object->setZipcode4($options['zipcode4']);
                    $object->setZipcode5($options['zipcode5']);
                   
                    $object->setVideo($options['video']);
					$object->setPhotos($options['photos']);
					$object->setSpecialtyTitle($options['specialty_title']);
					$object->setArea($options['area']);
                    $object->setSpecialNeeds($options['special_needs']);
                    $object->setTestimonials($options['testimonials']);
                    $object->setState($options['state']);
                    $object->setGallery($options['gallery']);
                    $object->setClicktotalkurl($options['clicktotalkurl']);
                    $object->setCounty($options['county']);
					$hasHttp = strstr($options['website'], 'http://');
					if(!$hasHttp){$object->setWebsite('http://'.$options['website']);}
					else {$object->setWebsite($options['website']);}
                   
                    $object->setUseZip($options['use_zip']);
                    $object->setUseZip1($options['use_zip1']);
                    $object->setUseZip2($options['use_zip2']);
                    $object->setUseZip3($options['use_zip3']);
                    $object->setUseZip4($options['use_zip4']);
                    $object->setUseZip5($options['use_zip5']);
					//$object->setAssistantid($options['assistantid']);
					//save area info to autocomplete table
					$db = Zend_Registry::get("db");
					$sql = "SELECT name FROM autocomplete WHERE name='".$options['state']."'";
					$result = $db->fetchAll($sql);
					if(empty($result)) {
						$result = $db->insert('autocomplete', array('name'=>$options['state']));
					}
					$sql = "SELECT name FROM autocomplete WHERE name='".$options['city']."'";
					$result = $db->fetchAll($sql);
					if(empty($result)) {
						$result = $db->insert('autocomplete', array('name'=>$options['city']));
					}
					$sql = "SELECT name FROM autocomplete WHERE name='".$options['area']."'";
					$result = $db->fetchAll($sql);
					if(empty($result)) {
						$result = $db->insert('autocomplete', array('name'=>$options['area']));
					}
					// end of autocomplete insertion				
                    
                    $doctor_id = $object->save();
                    if(!empty($doctor_id))//New doctor inserted
                    {
                        //Make sure its new doctor
                        if($doctor_id>1)//In case of edit save function gives 1.In case of edit we don't need to update id
                        $id = $doctor_id;
                    }

                    // update meta title and descriptions for the doctor profile
                    $SeoUrl = new Application_Model_SeoUrl();
                    $SeoObject = $SeoUrl->fetchRow("actual_url='/profile/index/id/{$object->getId()}'");
                    if($SeoObject){
                        $BaseMeta = new Base_MetaTags();
                        $BaseMeta->setDoctorProfileMeta($SeoObject, $object);
						if(trim($options['theUrl']) != ''){
							$SeoObject->setSeoUrl('/'.trim($options['theUrl']));
							$SeoObject->save();
						}
                    }
                    
                 $modelDoctorInsurance = new Application_Model_DoctorInsurance();
                 $modelDoctorInsurance->delete("doctor_id={$id}");
                    if (@count($options['doctor_insurance']) > 0) {
                        
                        foreach ($options['doctor_insurance'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorInsurance->setDoctorId($id);
                                $modelDoctorInsurance->setInsuranceId($value);
                                $modelDoctorInsurance->save();
                            }
                        }
                    }

               

                    $modelDoctorAward = new Application_Model_DoctorAward();
                    $modelDoctorAward->delete("doctor_id={$id}");
                    if (@count($options['doctor_award']) > 0) {
                      foreach ($options['doctor_award'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorAward->setDoctorId($id);
                                $modelDoctorAward->setAwardId($value);
                                $modelDoctorAward->save();
                            }
                             
                        }
                        
                    }
					
					//assistants
					$modelDoctorAssistant = new Application_Model_DoctorAssistant();
                    $modelDoctorAssistant->delete("doctor_id={$id}");
                    if (@count($options['doctor_assistant']) > 0) {
                      foreach ($options['doctor_assistant'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorAssistant->setDoctorId($id);
                                $modelDoctorAssistant->setAssistantId($value);
                                $modelDoctorAssistant->save();
                            }
                        }
                    }

                    if (@count($options['category_id']) > 0) {
                        $modelDoctorCat = new Application_Model_DoctorCategory();
                        $modelDoctorCat->delete("doctor_id={$id}");
                        
                        foreach ($options['category_id'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorCat->setDoctorId($id);
                                $modelDoctorCat->setCategoryId($value);
                                $modelDoctorCat->save();
                            }
                        }
                        
                    }

                    $modelDoctorExtraCat = new Application_Model_DoctorExtraCategory();
                    $modelDoctorExtraCat->delete("doctor_id={$id}");
                    if (isset($options['extra_category_id']) && @count($options['extra_category_id']) > 0) {
                        foreach ($options['extra_category_id'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorExtraCat->setDoctorId($id);
                                $modelDoctorExtraCat->setCategoryId($value);
                                $modelDoctorExtraCat->save();
                            }
                        }

                    }

                    $modelDoctorHospitalAffiliation = new Application_Model_DoctorHospitalAffiliation();
                    $modelDoctorHospitalAffiliation->delete("doctor_id={$id}");
                    if (@count($options['doctor_affiliation']) > 0) {
                        
                        foreach ($options['doctor_affiliation'] as $key => $value) {

                            if ($value != 0) {
                                $modelDoctorHospitalAffiliation->setDoctorId($id);
                                $modelDoctorHospitalAffiliation->setAffiliationId($value);
                                $modelDoctorHospitalAffiliation->save();
                            }
                        }
                    }


                    $modelDoctorReasonForVisit = new Application_Model_DoctorReasonForVisit();
                    $modelDoctorReasonForVisit->delete("doctor_id={$id}");
                    if (@count($options['doctor_reason_for_visit']) > 0) {
                    foreach ($options['doctor_reason_for_visit'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorReasonForVisit->setDoctorId($id);
                                $modelDoctorReasonForVisit->setReasonId($value);
                                $modelDoctorReasonForVisit->save();
                            }
                        }
                    }
                    $modeldoctor_association->delete("doctor_id={$id}");
                    if (count($options['doctor_association']) > 0) {
                        
                        foreach ($options['doctor_association'] as $key => $value) {
                            if ($value != 0) {
                                $modeldoctor_association->setDoctorId($id);
                                $modeldoctor_association->setAssociationId($value);
                                $modeldoctor_association->save();
                            }
                        }
                    }

                   
                    if(isset($options['submit'])){
                    	$this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'mlevel'=>$mlevel,'sorder'=>$sorder, 'msg' => $msg, 'page' => $page));
                    }else{
                    	$this->_helper->redirector('add-edit', 'doctor', "admin", Array('id' => $id, 'state' =>$state,'scriteria'=>$scriteria,'sorder'=>$sorder,'mlevel'=>$mlevel, 'msg' => $msg, 'page' => $page));
                    }
                /*} else {
                    $model = new Application_Model_Doctor($options);
                    $model->save();
                }*/
                if(isset($options['submit'])){
               		$this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'sorder'=>$sorder,'mlevel'=>$mlevel, 'msg' => $msg, 'page' => $page));
                }else{
                	$this->_helper->redirector('add-edit', 'doctor', "admin", Array('id' => $id, 'state' =>$state,'scriteria'=>$scriteria,'sorder'=>$sorder,'mlevel'=>$mlevel, 'msg' => $msg, 'page' => $page));
                }
            } else {
                $options['email'] = $emailoptions;
                $options['username'] = $usernameoptions;
                $options['video'] = stripslashes($options['video']);
				$options['area'] = stripslashes($options['area']);
				$options['photos'] = stripslashes($options['photos']);
				$options['specialty_title'] = stripslashes($options['specialty_title']);
				$options['specialty'] = stripslashes($options['specialty']);
                $options['about'] = stripslashes($options['about']);

               //Lets populate all the categories
                if(isset($options['category_id']))
                $catStr = implode(",", $options['category_id']);
                else  $catStr="";
                if($catStr=='')$catStr = '0';
                $arrCat = $category->getCategories("id IN ({$catStr})");
                $form->getElement('category_id')->setMultiOptions($arrCat);

               //Lets populate all Extra categories
                if(isset($options['extra_category_id']))
                $catStr = implode(",", $options['extra_category_id']);
                else  $catStr="";
                if($catStr=='')$catStr = '0';
                $arrCat = $category->getCategories("id IN ({$catStr})");
                $form->getElement('extra_category_id')->setMultiOptions($arrCat);

                //Lets populate all the doctor insurance
                if(isset($options['doctor_insurance']))
                $insuranceStr = implode(",", $options['doctor_insurance']);
                else $insuranceStr="";
                if($insuranceStr=='')$insuranceStr = '0';
                $arrInsurance = $modelInsuranceCompany->getInsurancecompanies("id IN ({$insuranceStr})");
                $form->getElement('doctor_insurance')->setMultiOptions($arrInsurance);

                //Lets populate all the reason for visit
                if(isset($options['doctor_reason_for_visit']))
                $ReasonForVisitStr = implode(",", $options['doctor_reason_for_visit']);
                else $ReasonForVisitStr="";
                if($ReasonForVisitStr=='')$ReasonForVisitStr = '0';
                $arrReasonForVisit = $modelReasonForVisit->getReasonForVisit("id IN ({$ReasonForVisitStr})");
                $form->getElement('doctor_reason_for_visit')->setMultiOptions($arrReasonForVisit);

                 //Lets populate all the Doctor Association
                 if(isset($options['doctor_association']))
                $DoctorAssociationStr = implode(",", $options['doctor_association']);
                 else $DoctorAssociationStr="";
                if($DoctorAssociationStr=='')$DoctorAssociationStr = '0';
                $arrDoctorAssociation = $modAssoc->getAssociations("id IN ({$DoctorAssociationStr})");
                $form->getElement('doctor_association')->setMultiOptions($arrDoctorAssociation);
                $form->reset();
                $form->populate($options);

                //Lets populate all the Doctor Award
                if(isset($options['doctor_award']))
                $DoctorAwardStr = implode(",", $options['doctor_award']);
                else $DoctorAwardStr='';

                if($DoctorAwardStr=='')$DoctorAwardStr = '0';
                $arrDoctorAward = $Awards->getAwards("id IN ({$DoctorAwardStr})");
                $form->getElement('doctor_award')->setMultiOptions($arrDoctorAward);
                $form->reset();
                $form->populate($options);

                //Lets populate all the Hospital Affiliation
                if(isset($options['doctor_affiliation']))
                $DoctorHospitalAffiliationStr = implode(",", $options['doctor_affiliation']);
                else $DoctorHospitalAffiliationStr='';
                if($DoctorHospitalAffiliationStr=='')$DoctorHospitalAffiliationStr = '0';
                $modelHA = new Application_Model_HospitalAffiliation();
                $arrDoctorHospitalAffiliation = $modelHA->getAllAffiliation("id IN ({$DoctorHospitalAffiliationStr})");
                $form->getElement('doctor_affiliation')->setMultiOptions($arrDoctorHospitalAffiliation);
                $form->reset();
                $form->populate($options);

                
            }
        }
        else
        {
            
        }
        $this->view->id = $id;
        
        $this->view->form = $form;
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function sendemailAction() {

        $id = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $form = new Admin_Form_Doctoremail();
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }


        $emailTemplate1 = new Admin_Model_EmailTemplate();

        $emailTemplate = $emailTemplate1->fetchRow("identifire='phone_appointment_mail_doctor'");

//print_r($emailTemplate);exit;

        $request = $this->getRequest();
        $options = $request->getPost();
        
        $model = new Application_Model_Doctor();
        $User = new Application_Model_User();

        $object = $model->find($id);
        $doctor_name = $object->getFname();
        $objuser = $User->find($object->getUserId());

        if ($request->isPost()) {
            if ($form->isValid($options)) {
                if ($objuser) {
                    $options['doctor_email'] = $objuser->getEmail();
                    $options['doctor_name'] = $object->getFname();
                    $msg = base64_encode("Mail has been sent successfully!");
                    if (0 < (int) $id) {
                        $options['call_date'] = $options['date_of_call'];
                        $options['doctor_id'] = $id;
                        $options['assign_phone'] = $object->getAssignPhone();
                        $options['actual_phone'] = $object->getActualPhone();
                        $PhoneApp = new Application_Model_DoctorPhoneAppointment();
                        $PhoneApp->setDoctorId($options['doctor_id']);
                        $PhoneApp->setPatientName($options['patient_name']);
                        $PhoneApp->setCallDate($options['call_date']);	
                        $PhoneApp->setPhone($options['phone']);

                        $PhoneApp->save();
                        $Mail = new Base_Mail('UTF-8');
                       //$Mail->sendEnquiryMailCopy($options);
                       	$Mail->sendTelephoneMailEnquiry($options);
                        $this->_helper->redirector('index', 'doctor', "admin", Array('msg' => $msg, 'page' => $page));
                    }
                } else {
                    $msg = base64_encode("Unable to send mail.Doctors Email not found");
                    /* $options['doctor_name']=$object->getFname();
                      $Mail = new Base_Mail('UTF-8');
                      $Mail->sendDoctorPhoneAppointmentBookingMail($options); */
                    $this->_helper->redirector('index', 'doctor', "admin", Array('msg' => $msg, 'page' => $page));
                }
            } else {
                //Form is invalid
                $form->populate($options);
            }
        } else {
            $options = array();
            $form->populate($options);
        }

        $this->view->doctor_name = $doctor_name;
        $this->view->form = $form;
        $this->view->mail_content = $emailTemplate->getBody();
    }

    function previewAction() {
        
    }

    
    

    public function copyProfileAction(){
        $id = $this->_getParam('id', 0);
        if($id<1)$this->_redirect('/admin/doctor/');
        $Doctor = new Application_Model_Doctor();
        $object = $Doctor->find($id);
        if($object){
            $path = "images/doctor_image/";
            $copy_image = $path . $object->getCompanylogo();
            $target_file_name = '';
            if (file_exists($copy_image)){
                $ext = array_pop(explode('.',$object->getCompanylogo()));
                $target_file_name = "doc_" . time() . ".{$ext}";
                copy($copy_image, $path.$target_file_name);
            }


            $Doctor->setFname($object->getFname().' (Copy)');
            $Doctor->setCompany($object->getCompany());
            $Doctor->setStreet($object->getStreet());
            $Doctor->setZipcode($object->getZipcode());
            $Doctor->setCompanylogo($target_file_name);
            $Doctor->setCity($object->getCity());
            $Doctor->setCountry($object->getCountry());
            $Doctor->setOfficeHours($object->getOfficeHours());
            $Doctor->setEducation($object->getEducation());
            $Doctor->setCreditlines($object->getCreditlines());
            $Doctor->setAssociates($object->getAssociates());
            $Doctor->setAssignPhone($object->getAssignPhone());
            $Doctor->setActualPhone($object->getActualPhone());
            $Doctor->setAwards($object->getAwards());
            $Doctor->setAbout($object->getAbout());
            $Doctor->setAmenities($object->getAmenities());
            $Doctor->setPaymentOptions($object->getPaymentOptions());
            $Doctor->setLanguage($object->getLanguage());
            $Doctor->setAssociation($object->getAssociation());
            $Doctor->setGeocode($object->getGeocode());
            $Doctor->setMembershipLevel($object->getMembershipLevel());
            $Doctor->setMembershipLevelNo($object->getMembershipLevelNo());
            $Doctor->setCommunityInvolvement($object->getCommunityInvolvement());
            $Doctor->setHobbies($object->getHobbies());
            $Doctor->setStaff($object->getStaff());
            $Doctor->setServices($object->getServices());
            $Doctor->setTechnology($object->getTechnology());
            $Doctor->setBrands($object->getBrands());
            $Doctor->setVideo($object->getVideo());
			$Doctor->setPhotos($object->getPhotos());
			$Doctor->setSpecialtyTitle($object->getSpecialtyTitle());
			$Doctor->setArea($object->getArea());
            $Doctor->setSpecialNeeds($object->getSpecialNeeds());
            $Doctor->setState($object->getState());
            $Doctor->setGallery($object->getGallery());
            $Doctor->setCounty($object->getCounty());
            $Doctor->setWebsite($object->getWebsite());
            $Doctor->setStatus(1);
            $Doctor->setTextAward($object->getTextAward());
            $Doctor->setZipcode1($object->getZipcode1());
            $Doctor->setZipcode2($object->getZipcode2());
            $Doctor->setZipcode3($object->getZipcode3());
            $Doctor->setZipcode4($object->getZipcode4());
            $Doctor->setZipcode5($object->getZipcode5());
            $Doctor->setUseZip($object->getUseZip());
            $Doctor->setUseZip1($object->getUseZip1());
            $Doctor->setUseZip2($object->getUseZip2());
            $Doctor->setUseZip3($object->getUseZip3());
            $Doctor->setUseZip4($object->getUseZip4());
            $Doctor->setUseZip5($object->getUseZip5());
            $doctor_id = $Doctor->save();
            if($doctor_id > 0){
                $newDoc = $Doctor->find($doctor_id);
                $newDoc->setMemberNumber($doctor_id);
                $newDoc->save();

                //Category
                $Category = new Application_Model_DoctorCategory();
                $catObject = $Category->fetchAll('doctor_id='.$id);
                
                if($catObject){
                    foreach($catObject as $c){
                        $Category->setDoctorId($doctor_id);
                        $Category->setCategoryId($c->getCategoryId());
                        $Category->save();
                    }
                }

                //insurance company
                $Company = new Application_Model_DoctorInsurance();
                $comObject = $Company->fetchAll('doctor_id='.$id);
                if($comObject){
                    foreach($comObject as $i){
                        $Company->setDoctorId($doctor_id);
                        $Company->setInsuranceId($i->getInsuranceId());
                        $Company->save();
                    }
                }

               

                //Reason for visit
                $Reason = new Application_Model_DoctorReasonForVisit();
                $resonObject = $Reason->fetchAll('doctor_id='.$id);
                if($resonObject){
                    foreach($resonObject as $r){
                        $Reason->setDoctorId($doctor_id);
                        $Reason->setReasonId($r->getReasonId());
                        $Reason->save();
                    }
                }

                //Association
                $Association = new Application_Model_DoctorAssociation();
                $assObject = $Association->fetchAll('doctor_id='.$id);
                if($assObject){
                    foreach($assObject as $a){
                        $Association->setDoctorId($doctor_id);
                        $Association->setAssociationId($a->getAssociationId());
                        $Association->save();
                    }
                }
                
                //Award
                $Award = new Application_Model_DoctorAward();
                $awardObject = $Award->fetchAll('doctor_id='.$id);
                if($awardObject){
                    foreach($awardObject as $aw){
                        $Award->setDoctorId($doctor_id);
                        $Award->setAwardId($aw->getAwardId());
                        $Award->save();
                    }
                }
                //Hospital Affiliation
                $Affiliation = new Application_Model_DoctorHospitalAffiliation();
                $affObject = $Affiliation->fetchAll('doctor_id='.$id);
                if($affObject){
                    foreach($affObject as $af){
                    $Affiliation->setDoctorId($doctor_id);
                    $Affiliation->setAffiliationId($af->getAffiliationId());
                    $Affiliation->save();
                    }
                }

                $this->_redirect('/admin/doctor/add-edit/id/'.$doctor_id);
            }
        }else{
            $this->_redirect('/admin/doctor/');
        }
    }
	
	public function appointmentAction() {
        $tab = $this->_getParam('tab');
        $today = $this->_getParam('today');
		$docid = $this->_getParam('docid');

        $Calendar = new Zend_Session_Namespace("calendar");

        if ($today != '') {
            $Calendar->TODAY = $today;
        } else {
            $Calendar->TODAY = time();
        }

        $this->view->tab = $tab;
		$this->view->docid = $docid;
    }

	 public function viewAppointmentAction() {
		$docid = $this->_getParam('docid');
        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');
		$page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $state = $this->_getParam("state");
        $scriteria = $this->_getParam("scriteria");

        $sorder = $this->_getParam("sorder");
        $zip = $this->_getParam("zip");
        $mlevel = $this->_getParam("mlevel");

        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->fetchRow("id='{$docid}'");

        $Appointment = new Application_Model_Appointment();
        $object = $Appointment->fetchRow("id={$appid} AND doctor_id={$docObject->getId()} AND deleted!=1");

        $this->view->tab = $tab;
		$this->view->docid = $docid;
        $this->view->object = $object;
    }

	public function newAppointmentAction(){
		$ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $state = $this->_getParam("state");
        $scriteria = $this->_getParam("scriteria");

        $sorder = $this->_getParam("sorder");
        $zip = $this->_getParam("zip");
        $mlevel = $this->_getParam("mlevel");
	
	
		$docid = $this->_getParam('docid');
		$Doctor = new Application_Model_Doctor();
		$docObject = $Doctor->fetchRow("id='{$docid}'");
		$drid = $docObject->getId();

		$this->view->reasonforvisit = $Doctor->getReasonForVisit($drid);
		$this->view->app_time = $this->_getParam('time');
		$this->view->app_date = $this->_getParam('date');
		$User = new Application_Model_User();
		$this->view->months = $User->listAllMonths();
        $this->view->days = $User->listAllDates();
        $this->view->years = $User->listAllYear();
		$this->view->insurancedataArr = $Doctor->getInsuranceCompany();

        $this->view->drid = $drid;
        $this->view->date = $date;
        $this->view->time = $time;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name = $this->_getParam('name');
			$surname = $this->_getParam('lastname');
			$zipcode = $this->_getParam('zipcode');
			$phone = $this->_getParam('phone');
			$email = $this->_getParam('email');
			$notes = $this->_getParam('notes');
			$year = $this->_getParam('year');
			$month = $this->_getParam('month');
			$day = $this->_getParam('day');
			$age = $this->birthday($year.'-'.$month.'-'.$day);
			$gender = $this->_getParam('gender');
			$status = 'n';
			$appointmentTime = $this->_getParam('appointment_time');
			$appointmentDate = $this->_getParam('appointment_date');
			$needs = $this->_getParam('needs');
			$reason = $this->_getParam('reason_to_visit');
			$insuranceCompany = $this->_getParam('insurance_company');
			$paying = $this->_getParam('paying');
			$send_email = $this->_getParam('send_email');
			
			/******Validation *************/
			$this->view->name = $name;
			$this->view->surname = $surname;
			$this->view->zipcode = $zipcode;
			$this->view->phone = $phone;
			$this->view->email = $email;
			$this->view->notes = $notes;
			$this->view->year = $year;
			$this->view->month = $month;
			$this->view->day = $day;
			$this->view->gender = $gender;
			$this->view->needs = $needs;
			$this->view->reason_to_visit = $reason;
			$this->view->insuranceCompany = $insuranceCompany;
			$this->view->paying = $paying;
			$this->view->send_email = $send_email;
			
			if(trim($name) == '' || trim($surname) == ''){
				$return['err'] = 1;
				$return['msg'] = "Name and surname are required fields";
				$this->view->return = $return;
				return;
			}
			
			/*******Validation *********/
			
			

			$Auth = new Base_Auth_Auth();
			$password = $Auth->passwordGenerator();

			$Appointment = new Application_Model_Appointment();
			$appObject = $Appointment->fetchRow("appointment_date='$appointmentDate' AND appointment_time='$appointmentTime' AND doctor_id='{$drid}'");
			if (!empty($appObject)) {
				$return['err'] = 1;
				$return['msg'] = "Appointment already booked for this time slot. \n Please book for another time slot";
				$this->view->return = $return;
				return;
			}


			$userId = 0;
			$User = new Application_Model_User();
			$userObject = $User->fetchRow("email='{$email}'");
			if ($userObject
				)$userId = $userObject->getId();

			if (!$userId) {

				$User->setEmail($email);
				$User->setUsername($email);
				$User->setFirstName($name);
				$User->setLastName($surname);
				$User->setUserLevelId(3); // for patient
				$User->setSendEmail(1);
				$User->setLastVisitDate(time());
				$User->setStatus('active');
				$User->setPassword(md5($password));
				$userId = $User->save();

				if (!$userId) {
					$return['err'] = 1;
					$return['msg'] = "There is some error, you can't register yet.";
				} else {
					$Patient = new Application_Model_Patient();
					$Patient->setUserId($userId);
					$Patient->setName($name);
					$Patient->setZipcode($zipcode);
					$Patient->setAge($age);
					$Patient->setMonthDob($month);
					$Patient->setDateDob($day);
					$Patient->setYearDob($year);
					$Patient->setGender($gender);
					$Patient->setPhone($phone);
					$Patient->setLastUpdated(time());
					$patientId = $Patient->save();
					if (!$patientId) {
						$return['err'] = 1;
						$return['msg'] = "You are not registered as patient, please contact to site administratot.";
					}
				}
			}
			if ($return['err'] == 1) {
				$this->view->return = $return;
				return;
			}

			/* ------------------------Start Insert Appointment ------------------------------ */

			$Appointment->setUserId($userId);
			$Appointment->setFname($name);
			$Appointment->setLname($surname);
			$Appointment->setZipcode($zipcode);
			$Appointment->setPhone($phone);
			$Appointment->setEmail($email);
			$Appointment->setAge($age);
			$Appointment->setGender($gender);
			$Appointment->setPatientStatus($status);
			$Appointment->setAppointmentDate($appointmentDate);
			$Appointment->setAppointmentTime($appointmentTime);
			$Appointment->setBookingDate(time());
			$Appointment->setDoctorId($drid);
			$Appointment->setReasonForVisit($reason);
			$Appointment->setNeeds($needs);
			$Appointment->setFirstVisit(1);
			$Appointment->setInsurance($insuranceCompany);
			$Appointment->setAppointmentType('0');
			$Appointment->setMonthDob($month);
			$Appointment->setDateDob($day);
			$Appointment->setYearDob($year);
			$Appointment->setNotes($notes);
			$appointmentId = $Appointment->save();
			$Appointment1 = new Application_Model_Appointment();
			$appObject = $Appointment1->fetchRow("id='{$appointmentId}'");
			$appObject->setApprove(1);
			$appObject->save();
			/* ------------------------End Insert Appointment ------------------------------ */

			if (!$appointmentId) {
				$return['err'] = 1;
				$return['msg'] = "You are registered for this site, but your appointment is not posted on the site, Please contact to site administrator.";
			}
			/* ------------------------Start Appointment Email ------------------------------ */
			
			$options = array();
			$options['email'] = $email;
			$options['password'] = $password;
			$options['name'] = $name." ".$lastname;
			$options['date'] = $appointmentDate;
			$options['time'] = $appointmentTime;
			$options['address1'] = $docObject->getStreet(). "<br>" . $docObject->getCity() . ", " . $docObject->getCountry() . " " . $docObject->getZipcode();
			$options['address2'] = "";

			$options['doctor'] = $docObject->getFname();

			$Mail = new Base_Mail('UTF-8');

			if ($status == 'n') {
				if($this->_getParam('send_email') == '1'){
					$Mail->sendPatientAppointmentBookingRegistrationMail($options);
				}
			} else {
				if($this->_getParam('send_email') == '1'){
					$Mail->sendPatientAppointmentBookingMail($options);
				}
			}
			$AdminMail = new Base_Mail('UTF-8');
			$AdminMail->sendAdministratorAppointmentBookingMail($options); // email to site administrator
			/* ------------------------End Appointment Email ------------------------------ */

			$return['app_id'] = $appointmentId;
			$this->view->return = $return;
			$this->view->docid = $docid;
			$this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'mlevel'=>$mlevel,'sorder'=>$sorder, 'msg' => $msg, 'page' => $page));
		}
	}
	
	public function confirmDeclineCancelAction() {
		$docid = $this->_getParam('docid');
        $this->_helper->viewRenderer->setNoRender(true);
        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');
        $type = $this->_getParam('type'); // 1- approve, 2-decline and 3-delete

        $Calendar = new Zend_Session_Namespace("calendar");
        if ($Calendar->TODAY

            )$today = $Calendar->TODAY;
        else
            $today = time();

        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();
        $docObject = $Doctor->fetchRow("id='{$docid}'");

        $Appointment = new Application_Model_Appointment();

        $object = $Appointment->fetchRow("id={$appid} AND doctor_id={$docObject->getId()}");
        if ($object) {
            switch ($type) {

                //case 2://Now Cancelling the appointment from doctors end
                
                case 1:
                case -1:
                case 2:
				case 3:


                    $objDoctor = $Doctor->find($object->getDoctorId());
                    $objUser = $User->find($objDoctor->getUserId());

                    if (!empty($docObject)) {
                        $options ['doctor'] = $docObject->getFname();
                        $options ['office'] = $docObject->getCompany();
                        $options ['phone'] = $docObject->getActualPhone();
                        $options['address1'] = $docObject->getStreet() . "<br>" . $docObject->getCity() . ", " . $docObject->getCountry() . " " . $docObject->getZipcode();
                        $options['address2'] = "";
                    }
                    if (!empty($objUser)) {
						
                        $options['name'] = $object->getFname()." ".$object->getLname();
                        $options['email'] = $objUser->getEmail();
                        
                        $options['page'] = $object->getAge();
                        $options['pemail'] = $object->getEmail();                        
                        if ($object->getGender() == "m") {
				            $options['pgender'] = "Male";
				        } else {
				            $options['pgender'] = "Female";
				        }
				        if ($object->getPatientStatus() == "e") {
				            $options['pStatus'] = "Existing";
				        } else {
				            $options['pStatus'] = "New";
				        }                        
                        $options['reason_for_visit'] = $object->getReasonForVisit();                        

                        $options ['time'] = $object->getAppointmentTime();
                        $options ['date'] = $object->getAppointmentDate();
					 
       
                        $options ['PTPhone'] = $object->getPhone();
                    }
                    $Mail = new Base_Mail('UTF-8');
                    if ($type == 1) {
                        $Mail->sendAdministratorAppointmentApprovalDoctorMail($options, "");
                        $Mail1 = new Base_Mail('UTF-8');
                        $Mail1->sendAdministratorAppointmentApprovalDoctorMail($options, "1");
                    } elseif ($type == -1) {
                        $Mail->sendAdministratorAppointmentDeclineDoctorMail($options, "");
                    } elseif ($type == 2) {
                        $object->setCancelledBy(2);// 2 for doctor cancelled
                        $Mail->sendAdministratorAppointmentDeclineDoctorMail($options, "");

                        $Mail1 = new Base_Mail('UTF-8');
                        $Mail1->sendAdministratorAppointmentDeclineDoctorMail($options, 1);
                    } else {
						$object->setCancelledBy(2);// 2 for doctor cancelled
                        $Mail->sendAdministratorAppointmentCancelDoctorMail($options, 1);

                        $Mail1 = new Base_Mail('UTF-8');
                        $Mail1->sendCancelAppointmentAdminMailEnquiry($options);
					}
                    break;
            }
            $object->setApprove($type);
            $object->save();
        }
		$this->view->docid = $docid;
		$this->_helper->redirector('appointment', 'doctor', "admin", Array('today' => $today, 'tab' => $tab, 'docid' => $docid));
		//$this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'mlevel'=>$mlevel,'sorder'=>$sorder, 'msg' => $msg, 'page' => $page));
    }
	
	public function deleteAppointmentAction() {
		$docid = $this->_getParam('docid');
        $this->_helper->viewRenderer->setNoRender(true);
        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');
        $type = $this->_getParam('type'); // 1- approve, 2-decline and 3-delete



        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();
        $docObject = $Doctor->fetchRow("id='{$docid}'");

        $Appointment = new Application_Model_Appointment();

        $object = $Appointment->fetchRow("id={$appid} AND doctor_id={$docObject->getId()}");
        if ($object) {
            $object->setDeleted(1);
            $object->save();
        
			$objDoctor = $Doctor->find($object->getDoctorId());
			$objUser = $User->find($objDoctor->getUserId());

			if (!empty($docObject)) {
				$options ['doctor'] = $docObject->getFname();
				$options ['office'] = $docObject->getCompany();
				$options ['phone'] = $docObject->getActualPhone();
				$options['address1'] = $docObject->getStreet() . "<br>" . $docObject->getCity() . ", " . $docObject->getCountry() . " " . $docObject->getZipcode();
				$options['address2'] = "";
			}
			$options['name'] = $object->getFname()." ".$object->getLname();
            $options['email'] = $objUser->getEmail();
            $options['page'] = $object->getAge();
            $options['pemail'] = $object->getEmail();                        
            $options['pStatus'] = $object->getPatientStatus();                        
            if ($object->getGender() == "m") {
	            $options['pgender'] = "Male";
	        } else {
	            $options['pgender'] = "Female";
	        }
	        if ($object->getPatientStatus() == "e") {
	            $options['pStatus'] = "Existing";
	        } else {
	            $options['pStatus'] = "New";
	        }
					
       
			$options ['PTPhone'] = $object->getPhone();
			$Mail = new Base_Mail('UTF-8');
			
			$object->setCancelledBy(2);// 2 for doctor cancelled
			$Mail->sendAdministratorAppointmentCancelDoctorMail($options, 1);

			$Mail1 = new Base_Mail('UTF-8');
			$Mail1->sendCancelAppointmentAdminMailEnquiry($options);
		}
		
		$this->view->docid = $docid;
		$this->_helper->redirector('appointment', 'doctor', "admin", Array('today' => $today, 'tab' => $tab, 'docid' => $docid) );
	}
	
	public function ajaxAppointmentAction() {

        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $tab = $this->_getParam('tab');
        $today = $this->_getParam('today');
		$docid = $this->_getParam('docid');

        $Calendar = new Zend_Session_Namespace("calendar");

        if ($today != '') {
            $Calendar->TODAY = $today;
        } else {
            $Calendar->TODAY = time();
        }
		
		$this->view->docid = $docid;
        $this->view->tab = $tab;
        $return['daily'] = $this->view->render('/doctor/daily.phtml');
        $return['weekly'] = $this->view->render('/doctor/weekly.phtml');

        echo Zend_Json::encode($return);
        exit();
    }
	
	//calculate years of age (input string: YYYY-MM-DD)
	private function birthday ($birthday){
		list($year,$month,$day) = explode("-",$birthday);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
			$year_diff--;
		return $year_diff;
	}

}

?>