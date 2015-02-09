<?php

/**
 * IndexController
 * 
 * @author
 * @version 
 */
class User_IndexController extends Base_Controller_Action {

    /**
     * The default action - show the home page
     */
    public function preDispatch() {
        parent::preDispatch();
        $usersNs = new Zend_Session_Namespace("members");
        if ($usersNs->userType == 'doctor') {
            $this->_helper->layout->setLayout('user');
        } else {
            $this->_helper->layout->setLayout('dih_wide');
        }
    }

    public function indexAction() {
        $usersNs = new Zend_Session_Namespace("members");

		$settings = new Admin_Model_GlobalSettings();
		$this->view->dateFormat = $settings->settingValue('date_format');
		$hours = $settings->settingValue('hours');
		if($hours) {
			$this->view->timeformat = "%I:%M %P";
		} else {
			$this->view->timeformat = "%H:%M";
		}
		
        if (isset($usersNs->userType) && $usersNs->userType == 'doctor') {
            $this->_forward('doctor-dashboard', 'index', 'user');
        } elseif (isset($usersNs->userType) && $usersNs->userType = 'patient') {
            $this->_forward('patient-dashboard', 'index', 'user');
        } else {
            $this->_helper->redirector('index', 'index', "default");
            exit();
        }
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function cancelAction() {
        $options = array();
        $id = $this->_getParam('id');
		
        $Appointment = new Application_Model_Appointment();
        $appObject = $Appointment->fetchRow("id='{$id}'");
		
        $Doctor = new Application_Model_Doctor();
        $docobj = $Doctor->fetchRow("id='{$appObject->getDoctorId()}'");

        $User = new Application_Model_User();
        $userobj = $User->find($docobj->getUserId());

        /* -----------cancel Appointent Patient/Doctor Email ------------ */
        $options['ptname'] = $appObject->getFname()." ".$appObject->getLname();
        $options['dname'] = $docobj->getFname();
        $the_date = $appObject->getAppointmentDate();
        $the_date = explode('-', $the_date);
		$new_date = $the_date[2] . '/' . $the_date[1] . '/' . $the_date[0];
		$options['datetime'] = $new_date . " " . $appObject->getAppointmentTime();
        $options['daddress'] = $docobj->getStreet() . "<br>" . $docobj->getCity() . ", " . $docobj->getCountry() . " " . $docobj->getZipcode();
        $options['site_url'] = "http://" . $_SERVER['HTTP_HOST'];
        $options['pemail'] = $appObject->getEmail();
        $options['demail'] = $userobj->getEmail();
        $options['pphone'] = $appObject->getPhone();
        $options['pzip'] = $appObject->getZipcode();
        $options['page'] = $appObject->getAge();

        if ($appObject->getGender() == "m") {
            $options['pgender'] = $this->view->lang[117];
        } else {
            $options['pgender'] = $this->view->lang[118];
        }
        if ($appObject->getPatientStatus() == "e") {
			$options['pStatus'] = $this->view->lang[930];
		} else {
			$options['pStatus'] = $this->view->lang[931];
		}
		$reason_id = $appObject->getReasonForVisit();
		
        if($reason_id>0) {
			$Reason = new Application_Model_ReasonForVisit();
			$resobj = $Reason->fetchRow("id='{$appObject->getReasonForVisit()}'");
			$options['reason_for_visit'] = $resobj->getReason();
		} else {
			$options['reason_for_visit'] = $appObject->getNeeds();
		}


        /* -----------send Appointent patient/Doctor Email ------------ */


        $approval = $appObject->getApprove();

        $appObject->setApprove(2);
        $appObject->setCancelledBy(3); // 3 for patient cancelled

        $appObject->save();
        $Mail_New = new Base_Mail('UTF-8');
        $Mail_New1 = new Base_Mail('UTF-8');
        $Mail_New2 = new Base_Mail('UTF-8');
        if ($approval != 1 && $approval != 3) {
            $Mail_New->sendCancelAppointmentPatientMailEnquiry($options);
            $Mail_New2->sendCancelAppointmentAdminMailEnquiry($options);
        } else {
            $Mail_New->sendCancelAppointmentPatientMailEnquiry($options);
            $Mail_New1->sendCancelAppointmentDoctorMailEnquiry($options);
            $Mail_New2->sendCancelAppointmentAdminMailEnquiry($options);
        }

        $mag = base64_encode("Appointment cancelled");
        $this->_helper->redirector('index', 'index', "user", Array('msg' => $msg));
    }

    public function doctorDashboardAction() {
        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");

        $Appointment = new Application_Model_Appointment();
        $where = "doctor_id='{$docObject->getId()}' AND (MONTH(appointment_date)='" . date('n') . "' AND YEAR(appointment_date)='" . date('Y') . "' AND deleted!=1)";
        $object = $Appointment->fetchAll($where, "appointment_date ASC");

        $this->view->docObject = $docObject;
        $this->view->object = $object;
    }

    public function patientDashboardAction() {
        $usersNs = new zend_Session_Namespace("members");
        $Patient = new Application_Model_Patient();
        $docPatient = $Patient->fetchRow("user_id='{$usersNs->userId}'");
        $Appointment = new Application_Model_Appointment();
		$upcomingWhere = "deleted!=1 AND user_id={$usersNs->userId} AND DATEDIFF(NOW(),appointment_date)<=0 AND  approve!=2";
        $pastWhere = "deleted!=1 AND (user_id={$usersNs->userId} AND DATEDIFF(NOW(),appointment_date)>0) OR (user_id={$usersNs->userId} AND approve=2)";

        $upcomingObject = $Appointment->fetchAll($upcomingWhere, "appointment_date DESC");
        $pastObject = $Appointment->fetchAll($pastWhere, "appointment_date DESC");
        $this->view->upcomingObject = $upcomingObject;
        $this->view->pastObject = $pastObject;
        $this->view->Patient = $docPatient;
		
		$settings = new Admin_Model_GlobalSettings();
		$this->view->dateFormat = $settings->settingValue('date_format');
		
    }

    public function appointmentDetailAction() {
        $id = $this->_getParam('id');
        $Appointment = new Application_Model_Appointment();
        $Insurance = new Application_Model_InsuranceCompany();
        $appObject = $Appointment->fetchRow("id='{$id}' AND deleted!=1");
        if ($appObject) {
            $insuranceObject = $Insurance->fetchRow("id='{$appObject->getInsurance()}'");
            $appStatus = $Appointment->getAppointmentStatus("id='{$id}'");
            $Doctor = new Application_Model_Doctor();
            $docObject = $Doctor->fetchRow("id='{$appObject->getDoctorId()}'");
            $Patient = new Application_Model_Patient();
            $reasonForVisit = new Application_Model_ReasonForVisit();
            $visitObject = $reasonForVisit->getMyResonForVisit("id='{$appObject->getReasonForVisit()}'");
            $patObject = $Patient->fetchRow("user_id='{$appObject->userId}'");
            $appGender = $Appointment->getFullGender("id='{$id}'");
            $profileImage = "/images/doctor_image/" . $docObject->getCompanyLogo();
            if (!file_exists(getcwd() . $profileImage) || $docObject->getCompanylogo() == '')
                $profileImage = "/images/doctor_image/noimage.jpg";
            else
                $profileImage = "/images/doctor_image/" . $docObject->getCompanyLogo();
            $this->view->profileImage = $profileImage;

            $this->view->docObject = $docObject;
            $this->view->patObject = $patObject;
            $this->view->appGender = $appGender;
            $this->view->appStatus = $appStatus;
            $this->view->visitObject = $visitObject;
            $this->view->insuranceObject = $insuranceObject;
        }
		
		$settings = new Admin_Model_GlobalSettings();
		$this->view->dateFormat = $settings->settingValue('date_format');
		$hours = $settings->settingValue('hours');
		if($hours) {
			$this->view->timeformat = "%I:%M %P";
		} else {
			$this->view->timeformat = "%H:%M";
		}
        $this->view->appObject = $appObject;
    }

    public function patientEditAction() {
        $userNs = new Zend_Session_Namespace("members");
        $id = $userNs->userId;
        $form = new User_Form_Patient();
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }
        if (0 < (int) $id) {
            $Patient = new Application_Model_Patient();
            $User = new Application_Model_User();
            $patObject = $Patient->fetchRow("user_id='{$id}'");

            $userObject = $User->fetchRow("id='{$patObject->getuserId()}'");
            $options['id'] = $patObject->getId();
            $options['name'] = $patObject->getName();
            $options['zipcode'] = $patObject->getZipcode();
            $options['age'] = $patObject->getAge();
            $options['month_dob'] = $patObject->getMonthDob();
			$options['date_dob'] = $patObject->getDateDob();
			$options['year_dob'] = $patObject->getYearDob();
            $options['phone'] = $patObject->getPhone();
            $options['gender'] = $patObject->getGender();
            $options['insurance'] = $patObject->getInsuranceCompanyId();
            $options['user_id'] = $id;
            $options['email'] = $userObject->getEmail();
            $options['last_name'] = $userObject->getLastName();
            $insuranceCompId = $patObject->getInsuranceCompanyId();

            $Insurance = new Application_Model_InsuranceCompany();
            $insurances = $Insurance->fetchAll("status=1", "company ASC");
			
            $this->view->insurances = $insurances;
            
            $this->view->insuranceCompId = $insuranceCompId;
            $this->view->insuranceCompId = $insuranceCompId;

            $form->populate($options);
        }
        $request = $this->getRequest();
        $options = $request->getPost();
        if ($request->isPost()) {


            $email = trim($options['email']);
            $userObject = $User->fetchRow("id!='{$patObject->getuserId()}' AND email='{$email}'");
            if (is_object($userObject)) {
                $form->setErrorMessages(array($this->view->lang[391]));
                $emailerror = 1;
            } else {

                $emailerror = 0;
            }
			if ($emailerror < 1) {
                $msg = $this->view->lang[546];
                
                $patObject->setName($options['name']);
                $last_updated = strtotime("now");
                $patObject->setZipcode($options['zipcode']);
                $patObject->setLastUpdated($last_updated);


                $patObject->setMonthDob($options['month_dob']);
				$patObject->setDateDob($options['date_dob']);
				$patObject->setYearDob($options['year_dob']);                
                $dob['year'] = $options['year_dob'];
                $dob['month'] = $options['month_dob'];
                $dob['day'] = $options['date_dob'];
				$age = $patObject->getAge($dob);	                                
                $patObject->setAge($age);

                $patObject->setPhone($options['phone']);
                $patObject->setGender($options['gender']);
                $patObject->setInsuranceCompanyId($options['insurance']);
               
                $patObject->save();


                $userObjectsave = $User->fetchRow("id='{$patObject->getUserId()}'");
                $userObjectsave->setEmail($options['email']);
                $userObjectsave->setLastName($options['last_name']);
                if (trim($options['password']) != "")
                    $userObjectsave->setPassword(md5($options['password']));
                $userObjectsave->save();


                $form->populate($options);
                $this->view->msg = $msg;
            } else {
                $form->reset();
                $form->populate($options);
            }
        } else {

            $patObject = $Patient->fetchRow("user_id='{$id}'");
            $options['id'] = $patObject->getId();
            $options['name'] = $patObject->getName();
            $options['zipcode'] = $patObject->getZipcode();
            $options['age'] = $patObject->getAge();
            $options['phone'] = $patObject->getPhone();
            $options['gender'] = $patObject->getGender();
            $options['insurance'] = $patObject->getInsuranceCompanyId();
            $options['user_id'] = $id;
			
			$userObject = $User->fetchRow("id='{$patObject->getUserId()}'");
			$options['last_name'] = $userObject->getLastName();
        }
        $this->view->form = $form;
    }

    public function appointmentAction() {
        $tab = $this->_getParam('tab');
        $today = $this->_getParam('today');

        $Calendar = new Zend_Session_Namespace("calendar");

        if ($today != '') {
            $Calendar->TODAY = $today;
        } else {
            $Calendar->TODAY = time();
        }

        $this->view->tab = $tab;
    }

    public function ajaxAppointmentAction() {

        $this->_helper->layout->disableLayout();
        $tab = $this->_getParam('tab');
        $today = $this->_getParam('today');

        $Calendar = new Zend_Session_Namespace("calendar");

        if ($today != '') {
            $Calendar->TODAY = $today;
        } else {
            $Calendar->TODAY = time();
        }

        $this->view->tab = $tab;
        $return['daily'] = $this->view->render('index/daily.phtml');
        $return['weekly'] = $this->view->render('index/weekly.phtml');
        $return['monthly'] = $this->view->render('index/monthly.phtml');

		$settings = new Admin_Model_GlobalSettings();
		$this->view->hours = $settings->settingValue('hours');
        echo Zend_Json::encode($return);
        exit();
    }

    public function accountDetailsAction() {
        $usersNs = new Zend_Session_Namespace("members");
        $id = $usersNs->userId;
        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');
        $emailerror = 0;
        $form = new User_Form_AccountDetails();
        if (0 < (int) $id) {
            $model = new Application_Model_Doctor();
            $object = $model->fetchRow("user_id={$id}");

            $options['id'] = $id;

            $options['fname'] = $object->getFname();
            $modelUser = new Application_Model_User();
            $objectUser = $modelUser->find($id);
            $options['email'] = $objectUser->getEmail();
            $options['username'] = $objectUser->getUsername();
            $form->populate($options);
        }
        $request = $this->getRequest();
        $options = $request->getPost();
        if ($request->isPost()) {
            $modelUser = new Application_Model_User();
            if (!empty($options['email'])) {
                $objUser = $modelUser->fetchRow("email ='{$options['email']}' AND id !={$id}");
                if (!empty($objUser)) {
                    $form->getElement('email')->setErrors(array("email already exists"));
                    $emailerror = 1;
                    $this->view->emailerror = "";
                }
            }
            if ($form->isValid($options)) {
                $object->setFname($options['fname']);
                $object->save();
                if ($options ['password'] != '') {
                    $objectUser->setPassword(md5($options ['password']));
                }
                $objectUser->setEmail($options['email']);
                $objectUser->save();
                $this->view->msg = $this->view->lang[546];
            } else {
                if ($emailerror == 1) {
                    $this->view->emailerror = $this->view->lang[391];
                }
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }

    public function editAction() {
        $usersNs = new Zend_Session_Namespace("members");

        $id = $usersNs->userId;
        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');

        $StringObj = new Base_String();
        if (0 < (int) $id) {
            $model = new Application_Model_Doctor();
            $object = $model->find($id);

            $modelDoctorInsurance = new Application_Model_DoctorInsurance();
            $ArrDoctorInsurance = $modelDoctorInsurance->getDoctorinsurance("doctor_id={$id}");

            $modelDoctorReasonForVisit = new Application_Model_DoctorReasonForVisit();
            $ArrDoctorReasonForVisit = $modelDoctorReasonForVisit->getDoctorReasonForVisit("doctor_id={$id}");

            $modeldoctor_association = new Application_Model_DoctorAssociation();
            $ArrDoctorAssociation = $modeldoctor_association->getDoctorAssociation("doctor_id={$id}");

            
            $options['id'] = $id;
            $options['user_id'] = $object->getUserId();
            $options['category_id'] = $object->getCategoryId();
            $options['fname'] = $object->getFname();
            $options['company'] = $object->getCompany();
            $options['street'] = $object->getStreet();
            $options['zipcode'] = $object->getZipcode();
            $options['city'] = $object->getCity();
            $options['country'] = $object->getCountry();
            $options['office_hour'] = $object->getOfficeHours();
            $options['education'] = $object->getEducation();
            $options['creditlines'] = $object->getCreditlines();
            $options['associates'] = $object->getAssociates();
            $options['assign_phone'] = $object->getAssignPhone();
            $options['actual_phone'] = $object->getActualPhone();
            $options['awards'] = $object->getAwards();
            $options['about'] = $object->getAbout();
            $options['amenities'] = $object->getAmenities();
            
			
			$modelAffil = new Application_Model_HospitalAffiliation();
			$options['state_for_affiliate'] = $modelAffil->getAllAffiliation("doctor_id={$id}");
        }

        $request = $this->getRequest();
        $options = $request->getPost();
 
        if ($request->isPost()) {
         
        }

        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

     public function officeinfoAction() {
        $usersNs = new Zend_Session_Namespace("members");
        $id = $usersNs->userId;
        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');
        $form = new User_Form_Officeinfo();
        $form->setAttrib('enctype', 'multipart/form-data');
        //Hiding the auto population of forms
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }
        $form->getElement('doctor_reason_for_visit2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_reason_for_visit')->setRegisterInArrayValidator(false);
        $StringObj = new Base_String();
        if (0 < (int) $id) {
            $model = new Application_Model_Doctor();
            $object = $model->fetchRow("user_id = {$id}");
            $modelDoctorInsurance = new Application_Model_DoctorInsurance();
            $ArrDoctorInsurance = $modelDoctorInsurance->getDoctorinsurance("doctor_id={$object->getId()}");
            $modelDoctorReasonForVisit = new Application_Model_DoctorReasonForVisit();
            $ArrDoctorReasonForVisit = $modelDoctorReasonForVisit->getDoctorReasonForVisit("doctor_id={$object->getId()}");
            $modeldoctor_association = new Application_Model_DoctorAssociation();
            $ArrDoctorAssociation = $modeldoctor_association->getDoctorAssociation("doctor_id={$object->getId()}");
			$selectedreason = $modelDoctorReasonForVisit->getDoctorReasonForVisitForDoctorEdit("doctor_id={$object->getId()}", null, 1);
            if (empty($selectedreason))
                $selectedreason = 0;
            //For all association it should not conatain that is alredy selected
            $ReasonforvisitModel = new Application_Model_ReasonForVisit();
            $docCategory = new Application_Model_DoctorCategory();
            $selectedcategory = $docCategory->getDoctorCategories("doctor_id={$object->getId()}", null, 1);
            if (empty($selectedcategory))
                $selectedcategory = 0;
            $modelReasonForVisit = new Application_Model_ReasonForVisit();
            $ArrallDoctorReasonForVisit = $modelReasonForVisit->getReasonForVisit("id not in({$selectedreason}) and category_id in ({$selectedcategory})");
            $form->getElement('doctor_reason_for_visit2')->setMultiOptions($ArrallDoctorReasonForVisit);
            $options['id'] = $id;
            $options['user_id'] = $object->getUserId();
            $options['fname'] = stripslashes($object->getFname());
            $options['company'] = stripslashes($object->getCompany());
			$options['assign_phone'] = stripslashes($object->getAssignPhone());
			$options['area'] = stripslashes($object->getArea());
            $options['street'] = stripslashes($object->getStreet());
            $options['zipcode'] = stripslashes($object->getZipcode());
            $options['city'] = stripslashes($object->getCity());
            
            $options['amenities'] = stripslashes($object->getAmenities());
			$options['community_involvement'] = $object->getCommunityInvolvement();
            if (count($ArrDoctorReasonForVisit) > 0) {
                $ArrDoctorReasonForVisit = $modelDoctorReasonForVisit->getDoctorReasonForVisitForDoctorEdit("doctor_id={$object->getId()}");
                $form->getElement('doctor_reason_for_visit')->setMultiOptions($ArrDoctorReasonForVisit);
            }
            $options['office'] = stripslashes($object->getOffice());
            $options['language'] = stripslashes($object->getLanguage());
            $options['staff'] = stripslashes($object->getStaff());
            $options['services'] = stripslashes($object->getServices());
            $options['technology'] = stripslashes($object->getTechnology());
            $options['office_hours'] = stripslashes($object->getOfficeHours());
            $options['state'] = $object->getState();
			
			//assistants			
			$docAssist = new Application_Model_DoctorAssistant();
			$ArrDoctorAssistant = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$object->getId()} ");
            $selectedassist = $docAssist->getDoctorAssistantForDoctorEdit("doctor_id={$object->getId()}", null, 1);
            if (empty($selectedassist))
                $selectedassist = 0;
           
            //On Edit page we should only those association that is for same category that is selected by doctor
			$Assistant = new Application_Model_Assistant();
            $ArrallDoctorAssist = $Assistant ->getAssistants("id not in ({$selectedassist})");
            
            
            //For all association it should not contain that is alredy selecte
            $form->getElement('doctor_assistant')->setMultiOptions($ArrDoctorAssistant);
            $form->getElement('doctor_assistant2')->setMultiOptions($ArrallDoctorAssist);
			
            $form->populate($options);
        }

        $request = $this->getRequest();
        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {
                $msg = urlencode(base64_encode($this->view->lang[546]));
                if (0 < (int) $id) {
                    $options['id'] = $id;
                    //$object->setCategoryId($options['category_id']);
                    $object->setCompany(stripslashes($options['company']));
					$object->setArea(stripslashes($options['area']));
					$object->setAssignPhone(stripslashes($options['assign_phone']));
					$object->setCommunityInvolvement(stripslashes($options['community_involvement']));
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $path = "images/doctor_image/";
                    $upload->setDestination($path);
                    try {
                        $upload->receive();
                    } catch (Zend_File_Transfer_Exception $e) {
                        $e->getMessage();
                    }
                    //        echo "<pre>";print_r($upload->getFileName('logo'));exit;
                    $upload->setOptions(array('useByteString' => false));
					$object->setCompany(stripslashes($options['company']));
                    $object->setStreet(stripslashes($options['street']));
                    $object->setZipcode($options['zipcode']);
                    $object->setCity(stripslashes($options['city']));
					$object->setServices(stripslashes($options['services']));
                   
                    $object->setAmenities(stripslashes($options['amenities']));
                    $object->setLanguage($options['language']);
                    $object->setStaff(stripslashes($options['staff']));
                    $object->setTechnology(stripslashes($options['technology']));
                    $object->setOfficeHours(stripslashes($options['office_hours']));
                    $object->setState($options['state']);
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
                    $object->save();
                    $modelDoctorReasonForVisit->delete("doctor_id={$object->getId()}");
                    if (@count($options['doctor_reason_for_visit']) > 0) {
                        $modelDoctorReasonForVisit = new Application_Model_DoctorReasonForVisit();
                        foreach ($options['doctor_reason_for_visit'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorReasonForVisit->setDoctorId($object->getId());
                                $modelDoctorReasonForVisit->setReasonId($value);
                                $modelDoctorReasonForVisit->save();
                            }
                        }
                    }
					
					//assistants
					$modelDoctorAssistant = new Application_Model_DoctorAssistant();
                    $modelDoctorAssistant->delete("doctor_id={$object->getId()}");
                    if (@count($options['doctor_assistant']) > 0) {
                      foreach ($options['doctor_assistant'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorAssistant->setDoctorId($object->getId());
                                $modelDoctorAssistant->setAssistantId($value);
                                $modelDoctorAssistant->save();
                            }
                        }
                    }
                    //$this->_helper->redirector('officeinfo', 'index', "user", Array('msg' => $msg, 'page' => $page));
                } else {
                    $model = new Application_Model_Doctor($options);
                    $model->save();
                }
                $mailOptions ['doctor_name'] = $object->getFname();
                $seoUrl = $this->view->seoUrl('/profile/index/id/'.$object->getId());
                $mailOptions ['doctor_url'] = Zend_Registry::get('siteurl').substr($seoUrl, 1,  strlen($seoUrl));
                $Mail = new Base_Mail('UTF-8');
                $Mail->doctorUpdateProfileMail($mailOptions);
                $this->_helper->redirector('officeinfo', 'index', "user", Array('msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }
        $this->view->form = $form;
        $this->view->msg = base64_decode(urldecode($this->_getParam('msg', '')));
    }


    public function paymentAction() {

        $usersNs = new Zend_Session_Namespace("members");
        $id = $usersNs->userId;
        $form = new User_Form_Payment();
        $form->setAttrib("enctype", "multipart/form-data");
        $form->getElement('doctor_insurance2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_insurance')->setRegisterInArrayValidator(false);
		
		
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }

        if (0 < (int) $id) {
            $model = new Application_Model_Doctor();
            $object = $model->fetchRow("user_id = {$id}");

            $options['id'] = $id;
            $options['user_id'] = $object->getUserId();

            $options['payment_options'] = stripcslashes($object->getPaymentOptions());
       
            $form->populate($options);
            if ($object->getPaymentOptions() != '') {
                $paymentOptionArray = explode(",", $object->getPaymentOptions());
                $form->getElement('payment_options')->setValue(explode(',', $object->getPaymentOptions()));
                $modelDoctorInsurance = new Application_Model_DoctorInsurance();
                $modelInsuranceCompany = new Application_Model_InsuranceCompany();
                $ArrDoctorInsurance = $modelDoctorInsurance->getDoctorinsuranceForDoctorEdit("doctor_id={$object->getId()}");
                $selectedinsureance = $modelDoctorInsurance->getDoctorinsuranceForDoctorEdit("doctor_id={$object->getId()}", null, 1);


                $form->getElement('doctor_insurance')->setMultiOptions($ArrDoctorInsurance);
                if (empty($selectedinsureance))
                    $selectedinsureance = 0;
                $ArrallInsurance = $modelInsuranceCompany->getInsurancecompanies("id not in({$selectedinsureance})");
                $form->getElement('doctor_insurance2')->setMultiOptions($ArrallInsurance);

				
            }
        }
        $request = $this->getRequest();


        $options = $request->getPost();
        if ($request->isPost()) {

		  /*
          * This is done to remove the zend form error for doctor_plan input
          * the data actually selected and set from the javascript not from zend form object
          *
          */
            
            if ($form->isValid($options)) {
                $msg = urlencode(base64_encode($this->view->lang[546]));


                if (0 < (int) $id) {
                    $options['id'] = $id;

                    if (count($options['payment_options']) > 0) {
                        $object->setPaymentOptions(implode(",", $options['payment_options']));
                    } else {
                        $object->setPaymentOptions('');
                    }

                    $modelDoctorInsurance = new Application_Model_DoctorInsurance();
                    $modelDoctorInsurance->delete("doctor_id={$object->getId()}");

                    if (@count($options['doctor_insurance']) > 0) {

                        foreach ($options['doctor_insurance'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorInsurance->setDoctorId($object->getId());
                                $modelDoctorInsurance->setInsuranceId($value);
                                $modelDoctorInsurance->save();
                            }
                        }
                    }
                    $object->save();                    
                } else {
                    $model = new Application_Model_Doctor($options);
                    $model->save();
                }
                $mailOptions ['doctor_name'] = $object->getFname();
                $seoUrl = $this->view->seoUrl('/profile/index/id/'.$object->getId());
                $mailOptions ['doctor_url'] = Zend_Registry::get('siteurl').substr($seoUrl, 1,  strlen($seoUrl));
                $Mail = new Base_Mail('UTF-8');

                $this->_helper->redirector('payment', 'index', "user", Array('msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }


        $this->view->form = $form;
        $this->view->msg = base64_decode(urldecode($this->_getParam('msg', '')));
    }

    public function personalAction() {

        $usersNs = new Zend_Session_Namespace("members");
        $path = "images/doctor_image/";
        $id = $usersNs->userId;
        $form = new User_Form_Personal();
        $form->setAttrib("enctype", "multipart/form-data");
        $form->getElement('doctor_affiliation2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_affiliation')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_association2')->setRegisterInArrayValidator(false);
        $form->getElement('doctor_association')->setRegisterInArrayValidator(false);
        $form->getElement('category_id')->setRegisterInArrayValidator(false);

       
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }

        if (0 < (int) $id) {
            $model = new Application_Model_Doctor();
            $object = $model->fetchRow("user_id = {$id}");

            $docCategory = new Application_Model_DoctorCategory();
			$selectedcategory = $docCategory->getDoctorCategories("doctor_id={$object->getId()}", null, 1);
			if (empty($selectedcategory))
				$selectedcategory = 0;

			$onjdocCategory = $docCategory->getDoctorCategories("doctor_id={$object->getId()}");
			$form->getElement('category_id')->setMultiOptions($onjdocCategory);
			
			$category = new Application_Model_Category();
			$arrallcategory = $category->getCategories("id not in ({$selectedcategory})");
            $form->getElement('category_id2')->setMultiOptions($arrallcategory);

            $company_logo = $object->getCompanyLogo();
            if (!empty($company_logo) && file_exists($path . $company_logo))
                $this->view->doctor_headshot = "/" . $path . $company_logo;
            else
                $this->view->doctor_headshot = "";

            $this->view->id = $object->getId();
            $this->view->defaultAffiliateState = "AL";


            $options['id'] = $id;
            $options['user_id'] = $object->getUserId();
            $options['text_award'] = stripcslashes($object->getTextAward());
            $options['hobbies'] = stripcslashes($object->getHobbies());
			$options['fname'] = stripslashes($object->getFname());
			$options['specialty_title'] = stripslashes($object->getSpecialtyTitle());
			$options['about'] = stripslashes($object->getAbout());
			$options['associates'] = stripslashes($object->getAssociates());
			$options['text_award'] = stripslashes($object->getTextAward());
            $options['education'] = stripcslashes($object->getEducation());
            $options['category_id'] = stripcslashes($object->getCategoryId());
            $modeldoctor_association = new Application_Model_DoctorAssociation();
            $modAssoc = new Application_Model_Association();
            $ArrDoctorAssociation = $modeldoctor_association->getDoctorAssociationForDoctorEdit("doctor_id={$object->getId()} ");
            $selectedassoc = $modeldoctor_association->getDoctorAssociationForDoctorEdit("doctor_id={$object->getId()}", null, 1);
            if (empty($selectedassoc))
                $selectedassoc = 0;
            $docCategory = new Application_Model_DoctorCategory();
            $selectedcategory = $docCategory->getDoctorCategories("doctor_id={$object->getId()}", null, 1);
            if (empty($selectedcategory))
                $selectedcategory = 0;
            $ArrallDoctorAssociation = $modAssoc->getAssociations("id not in ({$selectedassoc}) AND category_id in ({$selectedcategory})");

            //For all association it should not conatain that is alredy selecte
            $form->getElement('doctor_association')->setMultiOptions($ArrDoctorAssociation);
            $form->getElement('doctor_association2')->setMultiOptions($ArrallDoctorAssociation);

            //Lets populate all the Hospital Affiliation
            if (isset($options['doctor_affiliation']))
                $DoctorHospitalAffiliationStr = implode(",", $options['doctor_affiliation']);
            else
                $DoctorHospitalAffiliationStr='';
            if ($DoctorHospitalAffiliationStr == ''
                )$DoctorHospitalAffiliationStr = '0';
            $modelHA = new Application_Model_HospitalAffiliation();
            $arrDoctorHospitalAffiliation = $modelHA->getAllAffiliation("id IN ({$DoctorHospitalAffiliationStr})");
            $form->getElement('doctor_affiliation')->setMultiOptions($arrDoctorHospitalAffiliation);
            $form->reset();
            $form->populate($options);
            $form->populate($options);
        }
        $request = $this->getRequest();

        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {
				$string = $this->view->lang[546];
                $msg = base64_encode($string);
				$msg = urlencode($msg);
                if (0 < (int) $id) {
                    $options['id'] = $id;
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $upload->setDestination($path);
                    try {
                        $upload->receive();
                    } catch (Zend_File_Transfer_Exception $e) {
                        $e->getMessage();
                    }
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

                            if (file_exists($del_image)
                                )unlink($del_image);
                            $small_del_image = $path . "thumb1_" . $object->getCompanylogo();
                            ;
                            if (file_exists($small_del_image)
                                )unlink($small_del_image);

                            $object->setCompanylogo($image_name);
                        }else {
                            $options['company_logo'] = $image_name;
                        }
                        /* ------------------ END THUMB ------------------------ */
                    }
                    /* ------------------END COMPANY LOGO ------------------ */

                    $object->setEducation(stripslashes($options['education']));
                    $object->setTextAward(stripslashes($options['text_award']));
                    $object->setHobbies(stripslashes($options['hobbies']));
					$object->setAssociates(stripslashes($options['associates']));
					$object->setSpecialtyTitle(stripslashes($options['specialty_title']));
					$object->setTextAward(stripslashes($options['text_award']));
					$object->setFname(stripslashes($options['fname']));
					$object->setAbout(stripslashes($options['about']));
					//$object->setCategoryId($options['category_id']);
                    $modeldoctor_association = new Application_Model_DoctorAssociation();
                    $modeldoctor_association->delete("doctor_id={$object->getId()}");
                    if (count($options['doctor_association']) > 0) {
                        foreach ($options['doctor_association'] as $key => $value) {
                            if ($value != 0) {
                                $modeldoctor_association->setDoctorId($object->getId());
                                $modeldoctor_association->setAssociationId($value);
                                $modeldoctor_association->save();
                            }
                        }
                    }

                    $modelDoctorHospitalAffiliation = new Application_Model_DoctorHospitalAffiliation();
                    $modelDoctorHospitalAffiliation->delete("doctor_id={$object->getId()}");
                    if (@count($options['doctor_affiliation']) > 0) {
                        foreach ($options['doctor_affiliation'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorHospitalAffiliation->setDoctorId($object->getId());
                                $modelDoctorHospitalAffiliation->setAffiliationId($value);
                                $modelDoctorHospitalAffiliation->save();
                            }
                        }
                    }

                    $category_id = $this->_getParam("catid");
                    $this->view->category_id = $category_id;

                    $object->save();

                    if (@count($options['category_id']) > 0) {
                        $modelDoctorCat = new Application_Model_DoctorCategory();
                        $modelDoctorCat->delete("doctor_id={$object->getId()}");
                        
                        foreach ($options['category_id'] as $key => $value) {
                            if ($value != 0) {
                                $modelDoctorCat->setDoctorId($object->getId());
                                $modelDoctorCat->setCategoryId($value);
                                $modelDoctorCat->save();
                            }
                        }
                        
                    }

                } else {
                    $model = new Application_Model_Doctor($options);
                    $model->save();
                }
                $mailOptions ['doctor_name'] = $object->getFname();
                $seoUrl = $this->view->seoUrl('/profile/index/id/'.$object->getId());
                $mailOptions ['doctor_url'] = Zend_Registry::get('siteurl').substr($seoUrl, 1,  strlen($seoUrl));
                $Mail = new Base_Mail('UTF-8');
                $Mail->doctorUpdateProfileMail($mailOptions);
                $this->_helper->redirector('personal', 'index', "user", Array('msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }
        $modelDoctorHospitalAffiliation = new Application_Model_DoctorHospitalAffiliation();
        $arrall_affiliation = $modelDoctorHospitalAffiliation->getMyHospitalAffiliate("doctor_id={$object->getId()}");


        $form->getElement('doctor_affiliation')->setMultiOptions($arrall_affiliation);


        $this->view->form = $form;
        $this->view->msg = base64_decode(urldecode($this->_getParam('msg', '')));
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

    public function timeslotAction() {
        
    }

    public function calendarMoveAction() {

        $calday = $this->_getParam('calday');

        $PHPCalendar = new Base_PHPCalendar();
        $PHPCalendar->initCalendar($calday);
        exit();
    }

    public function viewAppointmentAction() {
	
		$settings = new Admin_Model_GlobalSettings();
		$this->view->dateFormat = $settings->settingValue('date_format');
		$hours = $settings->settingValue('hours');
		if($hours) {
			$this->view->timeformat = "%I:%M %P";
		} else {
			$this->view->timeformat = "%H:%M";
		}

        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');

        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");

        $Appointment = new Application_Model_Appointment();
        $object = $Appointment->fetchRow("id={$appid} AND doctor_id={$docObject->getId()} AND deleted!=1");

        $this->view->tab = $tab;
        $this->view->object = $object;
    }

    public function deleteAppointmentAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');
        $type = $this->_getParam('type'); // 1- approve, 2-decline and 3-delete



        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");

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

			$options ['time'] = $object->getAppointmentTime();
			
			$options ['date'] = $object->getAppointmentDate();
					
       
			$options ['PTPhone'] = $object->getPhone();
			$Mail = new Base_Mail('UTF-8');
			
			$object->setCancelledBy(2);// 2 for doctor cancelled
			$Mail->sendAdministratorAppointmentCancelDoctorMail($options, 1);

			$Mail1 = new Base_Mail('UTF-8');
			$Mail1->sendCancelAppointmentAdminMailEnquiry($options);
		}
		
    	$this->_helper->redirector('index', 'index', "user");
    }

    public function confirmDeclineCancelAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $appid = $this->_getParam('appid');
        $tab = $this->_getParam('tab');
        $type = $this->_getParam('type'); // 1- approve, 2-decline and 3-delete

        $Calendar = new Zend_Session_Namespace("calendar");
        if ($Calendar->TODAY

            )$today = $Calendar->TODAY;
        else
            $today = time();

        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");

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
                        $object->setDeleted(1);
                        $Mail->sendAdministratorAppointmentDeclineDoctorMail($options, "");

                        $Mail1 = new Base_Mail('UTF-8');
                        $Mail1->sendAdministratorAppointmentDeclineDoctorMail($options, 1);
                    } else {
						$object->setCancelledBy(2);// 2 for doctor cancelled
						$object->setDeleted(1);
                        $Mail->sendAdministratorAppointmentCancelDoctorMail($options, 1);

                        $Mail1 = new Base_Mail('UTF-8');
                        $Mail1->sendCancelAppointmentAdminMailEnquiry($options);
					}
                    break;
            }
            $object->setApprove($type);
            $object->save();
        }


        $this->_helper->redirector('appointment', 'index', "user", Array('today' => $today, 'tab' => $tab));
    }


    public function createAppointmentAction() {



        $return = array();
        $return['err'] = 0;
        $name = $this->_getParam('name');
        $zipcode = $this->_getParam('zipcode');
        $phone = $this->_getParam('phone');
        $email = $this->_getParam('email');
        $age = $this->_getParam('age');
        $gender = $this->_getParam('gender');
        $status = $this->_getParam('status');
        $appointmentTime = $this->_getParam('appointment_time');
        $appointmentDate = $this->_getParam('appointment_date');
        $needs = $this->_getParam('needs');
        $reason = $this->_getParam('reason');
        $insuranceCompany = $this->_getParam('insurance_company');
        
        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");
        $drid = $docObject->getId();

        $Auth = new Base_Auth_Auth();
        $password = $Auth->passwordGenerator();

        $Appointment = new Application_Model_Appointment();
        $appObject = $Appointment->fetchRow("appointment_date='$appointmentDate' AND appointment_time='$appointmentTime' AND doctor_id='{$drid}'");
        if (!empty($appObject)) {
            $return['err'] = 1;
            $return['msg'] = "Appointment already booked for this time slot. \n Please book for another time slot";
            echo Zend_Json::encode($return);
            exit();
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
            $User->setLastName('');
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
                $Patient->setGender($gender);
                $Patient->setPhone($phone);
                $Patient->setLastUpdated(time());
                $patientId = $Patient->save();
                if (!$patientId) {
                    $return['err'] = 1;
                    $return['msg'] = "You are not registered as patient, please contact to site administratot.";
                }
            }
        } else {
			$password = $this->view->lang[673];
		}
        if ($return['err'] == 1) {
            echo Zend_Json::encode($return);
            exit();
        }

        /* ------------------------Start Insert Appointment ------------------------------ */

        $Appointment->setUserId($userId);
        $Appointment->setFname($name);
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
        $Appointment->setInsurance($insuranceCompany);
        $Appointment->setAppointmentType('0');
        $appointmentId = $Appointment->save();
        /* ------------------------End Insert Appointment ------------------------------ */

        if (!$appointmentId) {
            $return['err'] = 1;

            $return['msg'] = "You are registered for this site, but your appointment is not posted on the site, Please contact to site administrator.";
        }
        /* ------------------------Start Appointment Email ------------------------------ */
        $options = array();
		$options['email'] = $email;
		$options['password'] = $password;
		$options['name'] = $name;
		$options['date'] = $appointmentDate;
		$options['time'] = $appointmentTime;
		$options['address1'] = $docObject->getStreet(). "<br>" . $docObject->getCity() . ", " . $docObject->getCountry() . " " . $docObject->getZipcode();
		$options['address2'] = "";
		$options['office'] = $docObject->getOffice();
		$options['phone'] = $docObject->getAssignPhone();
		$options['doctor'] = $docObject->getFname();

        $Mail = new Base_Mail('UTF-8');

        if ($status == 'n') {

            $Mail->sendPatientAppointmentBookingRegistrationMail($options);
        } else {

            $Mail->sendPatientAppointmentBookingMail($options);
        }
        $AdminMail = new Base_Mail('UTF-8');
        $AdminMail->sendAdministratorAppointmentBookingMail($options); // email to site administrator
        /* ------------------------End Appointment Email ------------------------------ */

        $return['app_id'] = $appointmentId;
        echo Zend_Json::encode($return);
        exit();
    }

    public function deleteimageAction() {

        $id = $this->_getParam("doctor_id");

        if (0 < (int) $id) {


            $model = new Application_Model_Doctor();
            $object = $model->find($id);
            $path = "images/doctor_image/";
            $del_image = $path . $object->getCompanylogo();
            if (file_exists($del_image)
                )unlink($del_image);
            $small_del_image = $path . "thumb1_" . $object->getCompanylogo();
            ;
            if (file_exists($small_del_image)
                )unlink($small_del_image);

            $object->setCompanylogo("");
            $object->save();
            die("pz wait");
        }
    }
    
	public function doctorReviewAction() {
		$settings = new Admin_Model_GlobalSettings();
		$model = new Application_Model_DoctorReview();
		$usersNs = new Zend_Session_Namespace("members");
		$where = null;
		
		$adminModeration = $settings->settingValue('admin_moderation');		
		if($adminModeration=="true") {
			$where = "admin_approved=1 AND doctor_id={$usersNs->doctorId}";
		} else {
			$where = "doctor_id={$usersNs->doctorId}";
		}
		
		$page_size = $settings->settingValue('pagination_size');
		$page = $this->_getParam('page', 1);
		$pageObj = new Base_Paginator();
		$paginator = $pageObj->fetchPageData($model, $page, $page_size, $where, "id DESC");
		$this->view->total = $pageObj->getTotalCount();
		$this->view->paginator = $paginator;

		$this->view->msg = base64_decode($this->_getParam('msg', ''));
	}
	 
     public function publishAction() {

        $ids = $this->_getParam('id');
        $page = $this->_getParam('page');
        $usersNs = new Zend_Session_Namespace("members");
        
        $idArray = explode(',', $ids);
        $model = new Application_Model_DoctorReview();
        foreach ($idArray as $id) {
            $object = $model->fetchRow("id={$id} AND doctor_id='{$usersNs->doctorId}'");
            if($object){
                $object->setStatus('1');
                $object->save();
            }
        }

        $publish = base64_encode($this->view->lang[584]);
        $this->_helper->redirector('doctor-review', 'index', "user", Array('page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('id');
        $page = $this->_getParam('page');
        $usersNs = new Zend_Session_Namespace("members");
        $idArray = explode(',', $ids);
        $model = new Application_Model_DoctorReview();
        foreach ($idArray as $id) {
            $object = $model->fetchRow("id={$id} AND doctor_id='{$usersNs->doctorId}'");
            if($object){
                $object->setStatus(0);
                $object->save();
            }
        }
        $publish = base64_encode($this->view->lang[583]);
        $this->_helper->redirector('doctor-review', 'index', "user", Array('page' => $page, 'msg' => $publish));
    }
    public function newAppointmentAction(){
        $usersNs = new Zend_Session_Namespace("members");
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->fetchRow("user_id='{$usersNs->userId}'");
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
            $status = 'e';
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
				$status = 'n';
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
            $options['name'] = $name." ".$surname;
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
            $this->_helper->redirector("appointment", "index", "user");
        }
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

}// end class

