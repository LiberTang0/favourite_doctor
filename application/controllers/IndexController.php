<?php



class IndexController extends Base_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        /* $uri=$this->_request->getPathInfo();
          $activeNav=$this->view->navigation()->findByUri($uri);
          $activeNav->active=true; */
    }

    public function __call($method, $args) {

		/* ---- Current Request Object ------ */
        $request = $this->getRequest();
        $actionName = $request->getActionName();
        /* ----------------------------------- */

        //Forward to the controller
        /* $this->_forward('index', 'user', 'default',
          array(
          'nickname' => $actionName
          )); */
    }

    public function indexAction() {

		$this->view->layoutChooser =3 ;
        // fetch category
        $Category = new Application_Model_Category();
        $categories = $Category->fetchAll("status=1", "name ASC");
        $city = $this->_getParam('city');
        $selectCity = "";
        if($city == 'City1'){
            $selectCity = "City1";
        }elseif($city == 'City2'){
            $selectCity = "City2";
        }elseif($city == 'City3'){
            $selectCity = "City3";
        }elseif($city == 'City4'){
            $selectCity = "City4";
        }elseif($city == 'City5'){
            $selectCity = "City5";
        }

        
        $Insurance = new Application_Model_InsuranceCompany();
        $insurances = $Insurance->fetchAll(null,"company ASC" );
        $this->view->categories = $categories;
        $this->view->insurances = $insurances;
        $this->view->city = $city;
        $this->view->selectCity = $selectCity;
        $this->view->isReasontoVisit = 0;

        //echo "<pre>"; print_r($planStr);exit;
		
		$Specialty = new Application_Model_Category();
        $specialties = $Specialty->fetchAll('status=1', 'name ASC');
        $this->view->specialties = $specialties;
    }

    public function insuranceAction() {

        $catid = $this->_getParam('catid');
        $isInnerPage = $this->_getParam('isInnerPage');
        if ($catid == 7) {
            $plan_type = 'd';// d - dentist
        } else {
            $plan_type = 'g';// g - general
        }
        $reasons = array();
        $return['isInnerPage'] = 0;
        if($isInnerPage){//fetch reason for visit
            $Reason = new Application_Model_ReasonForVisit();
            $reasons = $Reason->fetchAll("category_id='{$catid}' AND status=1", "reason ASC");
            $reasonStr = '<option value="">Select Reason for Visit</option>';
            if(!empty($reasons)){
                foreach ($reasons as $r) {
                    $reasonStr .= "<option value='{$r->getId()}'>{$r->getReason()}</option>";
                }
            }
            $return['reasonStr'] = $reasonStr;
            $return['isInnerPage'] = 1;
        }
        $whr = '';
        $db = Zend_Registry::get('db');
        if ($catid != 7) {
            
            $query = "SELECT `id`, `company` FROM `insurance_companies`
                        WHERE
                        `id` NOT IN (SELECT `insurance_company_id` FROM insurance_plans WHERE plan_type IN ('d','g') AND status=1)
                        AND status=1 ORDER BY `company`  ASC";
            $select = $db->query($query);
            $result = $select->fetchAll();

            if($result){
                $array = array();
                foreach($result as $rs){
                    $array[] = $rs->id;
                }
                $whr = " OR id IN (".implode(',', $array).")";
            }
        }

        /*$query = "SELECT `id`, `company` FROM `insurance_companies`
                    WHERE
                    `id` NOT IN (SELECT `insurance_company_id` FROM insurance_plans WHERE plan_type = '{$plan_type}' AND status=1)
                    AND status=1 ORDER BY `company`  ASC";*/
        $query = "SELECT `id`, `company` FROM `insurance_companies`
                    WHERE
                    (`id` IN (SELECT `insurance_company_id` FROM insurance_plans WHERE plan_type = '{$plan_type}' AND status=1) {$whr})
                    AND status=1 ORDER BY `company`  ASC";
                    
        $select = $db->query($query);
        $result = $select->fetchAll();
        
        $insuranceStr = '<option value="">'.$this->view->lang[67].'</option>';
        $insuranceStr .= '<option value="-1">'.$this->view->lang[436].'</option>';
        if($result){
            foreach ($result as $rs) {
                $insuranceStr .= "<option value='{$rs->id}'>{$rs->company}</option>";
            }
        }
        $return['insuranceStr'] = $insuranceStr;
        
        echo Zend_Json::encode($return);
        exit();
    }

    public function loginAction() {

		$this->view->layoutChooser =1 ;
        $this->_helper->layout->setLayout('dih_wide');
        
        $logintype = $this->_getParam("logintype");
        if(empty($logintype))
            $logintype = 'p';
        $usersNs = array();
        $usersNs = new Zend_Session_Namespace("members");
        if (Zend_Auth::getInstance()->hasIdentity() && $usersNs->userType == 'doctor') { // if user is already logged in then redirect to home page
            $this->_helper->redirector("index", "user");
        }
        $params = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getParams();
        }


        $this->view->form = $form = new User_Form_Login();
        $form->getElement('logintype')->setValue('doctor');
        $form->getElement('logintypes')->setValue($logintype);
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element){
            $element->removeDecorator('label');
        }
        
        if (isset($params['logintypes']) && $params['logintypes'] == 'd') { // for doctor login            
            if ($request->isPost()) {
                if ($form->isValid($request->getPost())) {
                    $params['user_level_id'] = 2; // for doctor
                    $loginAuth = $this->dologin($params);
                    if ($loginAuth == false) {                       
                        $form->setErrorMessages(array($this->view->lang[414]));
                    } else {
                        $this->_helper->redirector('index', 'user');
                    }
                } else {
                    //print_r($form->getErrors());exit;
                    //$this->_helper->redirector('index', 'index', "default", array("msg" => "le"));
                }
            }
        } else if (isset($params['logintypes']) && $params['logintypes'] == 'p') { // for patient login
            if ($request->isPost()) {
                if ($form->isValid($request->getPost())) {
                    $params['user_level_id'] = 3; // for patient
                    $loginAuth = $this->dologin($params);

                    if ($loginAuth == false) {
                        $form->setErrorMessages(array($this->view->lang[414]));
                    } else {
                        $this->_helper->redirector('index', 'user');
                    }
                } else {
                    //print_r($form->getErrors());exit;
                    //$this->_helper->redirector('index', 'index', "default", array("msg" => "le"));
                }
            }
        } else if (isset($params['logintypes']) && $params['logintypes'] == 'a') { // for assistant login
            if ($request->isPost()) {
                if ($form->isValid($request->getPost())) {
                    $params['user_level_id'] = 5; // for assistant
                    $loginAuth = $this->dologin($params);

                    if ($loginAuth == false) {
                        $form->setErrorMessages(array($this->view->lang[414]));
                    } else {
                        $this->_helper->redirector('index', 'user');
                    }
                } else {
                    //print_r($form->getErrors());exit;
                    //$this->_helper->redirector('index', 'index', "default", array("msg" => "le"));
                }
            }
        }

        // if some one comes from forgot password by clicking on 'continue' button
        $email = base64_decode($this->_getParam('e'));
        if ($email != '') {            
            $form->getElement('email')->setValue($email);
        }
    }

    public function dologin($params) {
        $Auth = new Base_Auth_Auth();
        $Auth->doLogout();
        
        $loginStatusEmail = true;
        $loginStatusUsername = true;

        $loginStatusEmail = $Auth->doLogin($params, 'email');
        if ($loginStatusEmail == false) {            
            $loginStatusUsername = $Auth->doLogin($params, 'username');
        }

        if ($loginStatusEmail == false && $loginStatusUsername == false) {
            return false;
        } else {
            if ($params['rememberMe'] == 1) {
                $Auth->remeberMe(true, $params);
            }
            return true;
        }
    }

    public function registerAction() {
		$this->view->layoutChooser =1 ;
        $this->_helper->layout->setLayout('dih_wide');
		
		$settings = new Admin_Model_GlobalSettings();
		$this->view->dar = $settings->settingValue('dar_plugin');
    }

	protected function parse_signed_request($signed_request, $secret) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

		// decode the data
		$sig = $this->base64_url_decode($encoded_sig);
		$data = json_decode($this->base64_url_decode($payload), true);

		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			error_log('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}

		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			error_log('Bad Signed JSON signature!');
			return null;
		}

		return $data;
	}

	protected function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
	public function facebookpatientRegistrationAction(){
		$this->view->layoutChooser =1;
        $this->_helper->layout->setLayout('dih_wide');
		$settings = new Admin_Model_GlobalSettings();
		define('FACEBOOK_SECRET', $settings->settingValue('facebook_secret'));
		
		$request = $this->getRequest();
		$error = 0;
		$errArray = array();
		if ($request->isPost()) {
			$options = $request->getPost();
			$the_response = $this->parse_signed_request($options['signed_request'], FACEBOOK_SECRET);
			$this->view->sth = $the_response['registration'];
			$person_data = $the_response['registration'];
			
			$mail = $person_data['email'];
			$Users = new Application_Model_User();
			$object = $Users->fetchRow("email='{$mail}'");
			if(!empty($object)){
				$error = 1;
			}
		
			if($error == 0){
				$Patient = new Application_Model_Patient();
				$User = new Application_Model_User();

				$User->setEmail($person_data['email']);
				$User->setUsername($person_data['email']);
				$name = explode(" ", $person_data['name'], 2);
				$first_name = $name[0];
				$last_name =  $name[1];
				$User->setFirstName($first_name);
				$User->setLastName($last_name);

				$User->setUserLevelId(3); // for patient
				$User->setSendEmail(1);
				$User->setLastVisitDate(time());
				$User->setStatus('active');
				$User->setPassword(md5($person_data['password']));
				$userId = $User->save();
				if ($userId) {
					$name = $first_name;
					$Patient->setName($name);
					$Patient->setGender(substr($person_data['gender'],0,1));
					$the_age = explode("/", $person_data['birthday']);
					$birth_date = $the_age[2] . "-" . $the_age[0] . "-" . $the_age[1];

					$dob['year'] =  $the_age[2];
					$dob['month'] =  $the_age[0];
					$dob['day'] =  $the_age[1];
					$age = $User->getAge($dob);
					$Patient->setAge($age);

					$Patient->setMonthDob($the_age[0]);
					$Patient->setDateDob($the_age[1]);
					$Patient->setYearDob($the_age[2]);

					$Patient->setUserId($userId);
					$Patient->setPhone($person_data['phone']);
					$Patient->setLastUpdated(time());
					$Patient->setInsuranceCompanyId(0);
					$Patient->setInsurancePlanId(0);
					$Patient->save();

					//Code to send Mail
					//$Mail = new Base_Mail('UTF-8');
					//$Mail->sendPatientRegistrationMail($options);
				}
					$msg = base64_encode("Patient Created Successfully");
					$this->_helper->redirector('patient-registration', 'index', "default", array('e' => base64_encode($person_data['email'])));
			}
			else{
				$msg = base64_encode($this->view->lang[391]);
				$this->_helper->redirector('patient-registration', 'index', "default", array('error' => $msg));
			}
			
		}
	}
	

	
    public function patientRegistrationAction() {
		$this->view->layoutChooser =1;
        $this->_helper->layout->setLayout('dih_wide');
        $form = new Application_Form_Patientregistration();
		
		$settings = new Admin_Model_GlobalSettings();
		$this->view->pfr = $settings->settingValue('dfr_plugin');
        $this->view->facebookApiKey = $settings->settingValue('facebook_api_key');

        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }
        $year = '';
        $request = $this->getRequest();
        $options = $request->getPost();
        if ($request->isPost()) {	
            if ($options['terms'] < 1) {
                $form->setErrorMessages(array($this->view->lang[393]));
            }
            $error = 0;
            $errArray = array();
			
            if ($options['month_dob'] == '') {
                $error = 1;
                $errArray[] = $this->view->lang[388];
            }

            if ($options['date_dob'] == '') {
                $error = 1;
                $errArray[] = $this->view->lang[389];
            }

            if ($options['year_dob'] == '') {
                $error = 1;
                $errArray[] = $this->view->lang[390];
            }

            $year = $options['year_dob'];

            if ($error) {
                $options['year_dob'] = '';
                $form->getElement('year_dob')->addErrorMessages($errArray);
            }


            if ($form->isValid($options)) {


                $Patient = new Application_Model_Patient();
                $User = new Application_Model_User();



                $User->setEmail($options['email']);
                $User->setUsername($options['email']);
                $User->setFirstName($options['first_name']);
                $User->setLastName($options['last_name']);

                $User->setUserLevelId(3); // for patient
                $User->setSendEmail(1);
                $User->setLastVisitDate(time());
                $User->setStatus('active');
                $User->setPassword(md5($options['password']));
                $userId = $User->save();
                if ($userId) {
                    $name = $options['first_name'];
                    $Patient->setName($name);
                    $Patient->setGender($options['gender']);
                    $birth_date = $options['year_dob'] . "-" . $options['month_dob'] . "-" . $options['date_dob'];



                    $dob['year'] = $options['year_dob'];
                    $dob['month'] = $options['month_dob'];
                    $dob['day'] = $options['date_dob'];
                    $age = $User->getAge($dob);
                    $Patient->setAge($age);

                    $Patient->setMonthDob($options['month_dob']);
                    $Patient->setDateDob($options['date_dob']);
                    $Patient->setYearDob($options['year_dob']);

                    $Patient->setUserId($userId);
                    $Patient->setPhone($options['phone']);
                    $Patient->setLastUpdated(time());
                    $Patient->setInsuranceCompanyId(0);
                    $Patient->setInsurancePlanId(0);
                    $Patient->save();

                    //Code to send Mail
                    $Mail = new Base_Mail('UTF-8');
                    $Mail->sendPatientRegistrationMail($options);
                }
                $msg = base64_encode("Patient Created Successfully");
                $this->_helper->redirector('patient-registration', 'index', "default", array('e' => base64_encode($options['email'])));
            }
        }
        $options['year_dob'] = $year;
        $form->populate($options);
        $this->view->email = $this->_getParam('e');
        $this->view->form = $form;
        $this->view->error = $this->_getParam('error');
        $this->view->msg = $this->view->lang[415];
    }

	public function doctorRegistrationAction() {
		$this->view->layoutChooser =1;
        $this->_helper->layout->setLayout('dih_wide');
        $form = new Application_Form_Doctorregistration();

        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }
        $request = $this->getRequest();
        $options = $request->getPost();

        if ($request->isPost()) {

            $error = 0;
            $errArray = array();

            if ($options['terms'] < 1) {
                $form->setErrorMessages(array($this->view->lang[393]));
            }

            if ($form->isValid($options)) {
                $Doctor = new Application_Model_Doctor();
                $User = new Application_Model_User();
				
                $User->setEmail($options['email']);
                $User->setUsername($options['email']);
                $User->setFirstName($options['first_name']);
                $User->setLastName($options['last_name']);

                $User->setUserLevelId(2); // for doctor    
                $User->setSendEmail(1);
                $User->setLastVisitDate(time());
                $User->setStatus('inactive');
                $User->setPassword(md5($options['password']));
                $userId = $User->save();
                if ($userId) {
                    $name = $options['first_name'];
                    $Doctor->setFname($name);
                    $Doctor->setUserId($userId);
                    $Doctor->setActualPhone($options['phone']);
					$Doctor->setMembershipLevelNo(2);
					$Doctor->setTextAward("");
					$Doctor->setUseZip("");
					$Doctor->setUseZip1("");
					$Doctor->setUseZip2("");
					$Doctor->setUseZip3("");
					$Doctor->setUseZip4("");
					$Doctor->setUseZip5("");
					
					$Doctor->setMemberNumber($userId);
					$Doctor->setCategoryId(199); //XXX - hardcoded to unspecified 
					$Doctor->setSpecialtyTitle("");
					$Doctor->setCompany("");
					$Doctor->setStreet("");
					$Doctor->setZipcode("");
					$Doctor->setZipcode1("");
					$Doctor->setZipcode2("");
					$Doctor->setZipcode3("");
					$Doctor->setZipcode4("");
					$Doctor->setZipcode5("");
					$Doctor->setCity("");
					$Doctor->setCountry("");
					$Doctor->setOfficeHours("");
					$Doctor->setEducation("");
					$Doctor->setCreditlines("");
					$Doctor->setAssociates("");
					$Doctor->setAssignPhone("");
					$Doctor->setAwards("");
					$Doctor->setAbout("");
					$Doctor->setAmenities("");
					$Doctor->setPaymentOptions("");
					$Doctor->setInsuranceAccepted("");
					$Doctor->setOffice("");
					$Doctor->setLanguage("");
					$Doctor->setAssociation("");
					$Doctor->setFeatured("");
					$Doctor->setGeocode(""); //XXX can it be null?
					$Doctor->setMembershipLevel("Gold");
					$Doctor->setYearsAtPractice("");
					$Doctor->setYearsPractice("");
					$Doctor->setCommunityInvolvement("");
					$Doctor->setHobbies("");
					$Doctor->setStaff("");
					$Doctor->setServices("");
					$Doctor->setTechnology("");
					$Doctor->setBrands("");
					$Doctor->setVideo("");
					$Doctor->setPhotos("");
					$Doctor->setArea("");
					$Doctor->setSpecialNeeds("");
					$Doctor->setTestimonials("");
					$Doctor->setState("");
					$Doctor->setGallery("");
					$Doctor->setClicktotalkurl("");
					$Doctor->setCounty("");
					$Doctor->setWebsite("http://");
					$Doctor->setCompanylogo("");
					$Doctor->setCompanyicon("");
					$Doctor->setPublishUp("");
					$Doctor->setPublishDown("");
					$Doctor->setStatus(0);
					$Doctor->setTextAward("");
					
                    $outcome = $Doctor->save();
					
					$DocCat = new Application_Model_DoctorCategory();
					$DocCat->setDoctorId($outcome);
					$DocCat->setCategoryId(198);
					$DocCat->save();
					
					$Doc = new Application_Model_Doctor();
					$obj = $Doc->fetchRow("user_id=$userId");
					$docId = $obj->getId();
					
					if(empty($Doc)){
						$this->_redirect('/404');
					}
					
                    //Code to send Mail
					if($docId != null) {
						$options["docid"]= $docId;
						$Mail = new Base_Mail('UTF-8');
						$Mail->sendDoctorReg($options);
					} else {
						//something went wrong
					}
                }
                $msg = base64_encode("Doctor Created Successfully");
                $this->_helper->redirector('doctor-registration', 'index', "default", array('e' => base64_encode($options['email'])));
            }
        }
        $form->populate($options);
        $this->view->email = $this->_getParam('e');
        $this->view->form = $form;
        
        $this->view->msg = $this->view->lang[551];
    }
	
    public function GetAge($Birthdate) {
		// Explode the date into meaningful variables
        list($BirthYear, $BirthMonth, $BirthDay) = explode("-", $Birthdate);
        // Find the differences
        $YearDiff = date("Y") - $BirthYear;
        $MonthDiff = date("m") - $BirthMonth;
        $DayDiff = date("d") - $BirthDay;

        // If the birthday has not occured this year
        if ($DayDiff < 0 || $MonthDiff < 0)
            $YearDiff--;
        return $YearDiff;
    }

    public function forDataAction(){
      	$this->_helper->layout->disableLayout();
		$request = $this->getRequest(); 	
        $options = $request->getParams();
        
        if ($request->isPost()) {
			$model = new Application_Model_User();
			$model = $model->fetchRow("email='{$options['email']}'");
			if (false !== $model) {
				$Auth = new Base_Auth_Auth();
				$Auth->recoverPassword($model);
				//  $this->_helper->redirector('forgot', 'index', null, array('msg' => base64_encode($options['email'])));
				die("Done");
			}else{
				die("error");
			}
        }
    }
        
    public function forgotAction() {
		$this->_helper->layout->disableLayout();
        $request = $this->getRequest();
           	
        $this->view->form = $form = new Application_Form_Forgot();
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element)
            $element->removeDecorator('label');

        $options = $request->getParams();
        
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model = new Application_Model_User();
                $model = $model->fetchRow("email='{$options['email']}'");
                if (false !== $model) {
                    $Auth = new Base_Auth_Auth();
                    $Auth->recoverPassword($model);
                    $this->_helper->redirector('forgot', 'index', null, array('msg' => base64_encode($options['email'])));
                    $form->reset();
                }
            }
        }

        $email = '';
        if ($this->_getParam('msg') != '') {
            $email = base64_decode($this->_getParam('msg'));
            $User = new Application_Model_User();
            $obj = $User->fetchRow("email='{$email}'");
            if (!empty($obj)) {
                if ($obj->getUserLevelId() == 2) {
                    $this->view->loginType = 'd';
                } elseif ($obj->getUserLevelId() == 3) {
                   $this->view->loginType = 'p';
                }
                $this->view->loginPath = 'login';
                
            }
        }
        $this->view->email = $email;
    }

    public function logoutAction() {
        $Auth = new Base_Auth_Auth();
        $Auth->doLogout();
        $Auth->forgotMe('rememberMe');
        $this->_helper->redirector('index', 'index', "default");
        exit();
    }

    public function checkEmailAction() {
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();
        $options = $request->getParams();

        $model = new Application_Model_User();
        if (true === $model->isExist("email='{$options['email']}'")) {
            $result = Array('error' => 1, 'msg' => $this->view->lang[391]);
        } else {
            $result = Array('error' => 0, 'msg' => $this->view->lang[416]);
        }

        echo Zend_Json::encode($result);
        exit;
    }

    public function aboutUsAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');
        $Article = new Application_Model_Article();
        $object = $Article->find(20);

        $this->view->object = $object;
    }

    public function contactUsAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');
		
        $form = new Application_Form_Contactus();
        $request = $this->getRequest();
        $options = $request->getParams();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $Mail = new Base_Mail('UTF-8');
                $Mail->sendEnquiryMailToAdmin($options);
                if ($options['email_copy']) {
                    $Mail1 = new Base_Mail('UTF-8');
                    $Mail1->sendEnquiryMailCopy($options);
                }
                $this->_helper->redirector('thankyou', 'index', "default");
            }
        }
        $this->view->form = $form;
    }

    public function thankyouAction() {
        $referer = $_SERVER['HTTP_REFERER'];
        $this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');
        //if (strstr($referer, 'index/contact-us')) {
        if (strstr($referer, '/dih')) {
            $this->view->message = 'contact';
        } else {
            $this->view->message = 'register';
        }
        //echo "<pre>";print_r($_SERVER);exit;
    }

    public function privacyPolicyAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');  
        $Article = new Application_Model_Article();
        $object = $Article->find(7);

        $this->view->object = $object;
    }
	
	public function faqAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');  
        $Article = new Application_Model_Article();
        $object = $Article->find(15);

        $this->view->object = $object;
    }
	
	public function answersAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');  
        $Article = new Application_Model_Article();
        $object = $Article->find(16); 

        $this->view->object = $object;
    }

	public function videoAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');  
        $Article = new Application_Model_Article();
        $object = $Article->find(17);

        $this->view->object = $object;
    }
	
	public function pageAction(){
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');  
        $Article = new Application_Model_Article();
		
		$article_id = $this->_getParam('id');
		$object = $Article->find($article_id);

        $this->view->object = $object;
	}
	
    public function termsAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');       
		$Article = new Application_Model_Article();
        $object = $Article->find(6);

        $this->view->object = $object;
    }
    public function doctorSitemapAction() {
        $this->_helper->layout->setLayout('dih_wide');
    }
   
	public function commingsoonAction() {
        $this->_helper->layout->setLayout('dih_wide');
    }
	
	public function doctorverifyedAction() {
		$this->view->layoutChooser = 1;
		$this->_helper->layout->setLayout('dih_wide');
		  
		$request = $this->getRequest();
		$options = $request->getParams();
		if ($options['docid']) {
			//find user and activate
			$DoctorModel = new Application_Model_Doctor();
			$doctorObject = $DoctorModel->find($options['docid']);	
			$User = new Application_Model_User();
			$user = $User->find($doctorObject->getUserId());
			$old=false;
			if($user) {
				if($user->getStatus() == 'active') {
					// already activated
					$this->view->message = $this->view->lang[554];
					$old = true;
				} else {
					$user->setStatus('active');	 
					$user->save();
				}
			}
		} else {
			$this->view->message=$this->view->lang[556];
		}
	}

	/* call daily once */
	public function appointmentsReminderAction() {
		$this->_helper->layout->disableLayout();
		$today = date("Y-m-d");
		$tomorrow = date("Y-m-d", strtotime("tomorrow", strtotime($today)));
		$midDay = strtotime("tomorrow 12:00pm");

		$this->view->message = "morning check: ".date("Y-m-d H:i", $midDay);

		$Appointment = new Application_Model_Appointment();
		$appointments = $Appointment->fetchAll("appointment_date = '".$tomorrow."' AND approve = 1 AND cancelled_by = '0'");
		$this->view->message .= "<br/>appointment_date = '".$tomorrow."' AND (approve = 1 || approve = 3) AND cancelled_by = '0'";
		foreach($appointments as $appointment) {
			$appTimestamp = strtotime($tomorrow." ".$appointment->getAppointmentTime());
			$this->view->message .= " checking appointment ".date("Y-m-d H:i", $appTimestamp);
			if($appTimestamp <= $midDay) {
				$this->view->message .= "sent<br/>";
				$Mail = new Base_Mail('UTF-8');
				$Mail->sendLongReminder($appointment);
			} else {
				$this->view->message .= "not sent<br/>";
			}
		}
	}

	/* call every 15 minutes */
	public function appointmentsAfterReminderAction() {
		$this->_helper->layout->disableLayout();
		$today = date("Y-m-d");
		$now = time();
		$timeAfter = 4; //how many hours after the reminder will be sent
		$end = strtotime("+15 minutes", $now);

		$this->view->message = "start: ".date("Y-m-d H:i", $now)." end: ".date("Y-m-d H:i", $end);

		$Appointment = new Application_Model_Appointment();
		$appointments = $Appointment->fetchAll("appointment_date = '".$today."' AND (approve = 1 || approve = 3) AND cancelled_by = '0'");				
		foreach($appointments as $appointment) {
			$appTimestamp = strtotime($today." ".$appointment->getAppointmentTime());
			$checkTime = $appTimestamp + ($timeAfter * 3600);
			$this->view->message .= " checking appointment ".date("Y-m-d H:i", $appTimestamp)." and time after appoitnment: ".date("Y-m-d H:i", $checkTime) ;
			if($checkTime >= $now && $checkTime < $end ) {
				$this->view->message .= "sent<br/>";
				$Mail = new Base_Mail('UTF-8');
				$Mail->sendAfterReminder($appointment);
			} else {
				$this->view->message .= "not sent<br/>";
			}
		}
	}

	function updateTimeslotsAction(){
		//fix timeslots
		$Timeslots = new Application_Model_MasterTimeslot();
		$timeslots = $Timeslots->fetchAll();
		foreach($timeslots as $timeslot) {
			$timeslot->setStartTime(date("H:i", strtotime($timeslot->getStartTime())));
			$timeslot->setEndTime(date("H:i", strtotime($timeslot->getEndTime())));
			$timeslot->save();
		}

		//fix appointments
		$Appointment = new Application_Model_Appointment();
		$appointments = $Appointment->fetchAll();
		foreach($appointments as $appointment) {
			$appointment->setAppointmentTime(date("H:i", strtotime($appointment->getAppointmentTime())));
			$appointment->save();
		}
	}

	function updateDoctorTimeslotsAction(){
		$Timeslots = new Application_Model_MasterTimeslot();
		$timeslots = $Timeslots->fetchAll();
		foreach($timeslots as $timeslot) {
			$slots = $timeslot->getDisplaySlots();
			if($slots != "") {
				$slotsArray = explode(",", $slots);
				$correctSlotsArray = array();
				foreach($slotsArray as $slot) {
					$correctSlotsArray[] = date("H:i", strtotime($slot));
				}
				$newslots = implode(", ", $correctSlotsArray);
				$timeslot->setDisplaySlots($newslots);
				$timeslot->save();
			}
		}
	}
}
