<?php

class AppointmentController extends Base_Controller_Action {


	public function preDispatch() {
        parent::preDispatch();
    }


    public function indexAction() {
		$this->_helper->layout->setLayout('dih_wide');
        $drid = $this->_getParam('drid');
        $date = $this->_getParam('date');
        $time = $this->_getParam('time');
        $Doctor = new Application_Model_Doctor();
        $doctorObject = $Doctor->find($drid);
        $this->view->doctor = $doctorObject;
        $this->view->category = $Doctor->getDoctorCategoryList($drid);

        $error = "";
        $this->view->error = "";
        $Appointment = new Application_Model_Appointment();
        $appointTime = strftime("%I:%M %P", strtotime($time));
        $appObject = $Appointment->fetchRow("appointment_date='$date' AND appointment_time='$appointTime' AND doctor_id='{$drid}' AND deleted!=1");
        $appTime = strtotime("$date $time");		
        if(!empty($appObject)){
            $error['err'] = 1;
            $error['msg'] = "<li>".$this->view->lang[402]."<li></li>".$this->view->lang[402]."</li><li><a href='javascript:history.back()'>".$this->view->lang[361]."</a></li>";
            $this->view->error = $error;            
            return;
        }
        if($appTime<time()) {
        	$error['err'] = 1;
            $error['msg'] = "<li> ".$this->view->lang[400]." </li><li>".$this->view->lang[401]."</li><li><a href='javascript:history.back()'>".$this->view->lang[361]."</a></li>";
         	$this->view->error = $error;            
         	return;
        }
        
		//enable or desable SMS plugin
		$settings = new Admin_Model_GlobalSettings();
		$this->view->smsPlugin = $settings->settingValue('sms_plugin');
		
		$hours = $settings->settingValue('hours');
	    if($hours) {
			$this->view->timeformat = "%I:%M %P";
	    } else {
	        $this->view->timeformat = "%H:%M";
	    }
        
		$DocCategory = new Application_Model_DoctorCategory();
        $catObject = $DocCategory->fetchAll("doctor_id='{$drid}'");
        $str = "";
        if($catObject){
            $array = array();
            foreach($catObject as $cat){
                $array[] = $cat->getCategoryId();
            }
            $str = implode(',', $array);
        }
        if($str=='')$catlist = "0";else $catlist = "$str";
       
		$this->view->reasonforvisit = $Doctor->getReasonForVisit($drid);
        $this->view->insurance_companies = $Doctor->getInsuranceCompany();
		
		$modeldoctor_insurance = new Application_Model_DoctorInsurance();
		$ArrDoctorInsurance=$modeldoctor_insurance->getDoctorinsurance("doctor_id={$drid}");
		$InsuranceCompany = new Application_Model_InsuranceCompany();
		foreach($ArrDoctorInsurance as $key=>$value)
		{
			$insuranceobject = $InsuranceCompany->find($value);
			if($insuranceobject)$insurancedata[$insuranceobject->getId()]=$insuranceobject->getCompany();
		}
		$this->view->insurancedataArr = $insurancedata;
		
		
		$reasonNamespace = new Zend_Session_Namespace('company');
        
        $User = new Application_Model_User();
        $this->view->months = $this->listAllMonths();
        $this->view->days = $User->listAllDates();
        $this->view->years = $User->listAllYear();

        $this->view->drid = $drid;
        $this->view->date = $date;
        $this->view->time = $time;

      
    }// end function

    public function checkLoginAction() {

        $return = array();
        $usersNs = new Zend_Session_Namespace("members");
        if($usersNs->userId <> '' && $usersNs->userType=='patient'){
            die("1");
        }else{
            die("0");
        }
    }// end function

    public function getPatientDetailsAction() {

        $return = array();
        $usersNs = new Zend_Session_Namespace("members");
        $return['err'] = '0';
        if($usersNs->userId <> '' && $usersNs->userType=='patient'){
            $Patient = new Application_Model_Patient();
            $object = $Patient->fetchRow("user_id='{$usersNs->userId}'");

            $User = new Application_Model_User();
            $user = $User->find($usersNs->userId);
            if(!empty($object)){
                $return['name'] = $object->getName();
                $return['zipcode'] = $object->getZipcode();
                $return['age'] = $object->getAge();
                $return['month'] = $object->getMonthDob();
                $return['day'] = $object->getDateDob();
                $return['year'] = $object->getYearDob();
                $return['gender'] = $object->getGender();
                $return['phone'] = $object->getPhone();

                $return['email'] = $usersNs->userEmail;
                $return['lastname'] = $user->getLastName();
            }
        }else{
            $return['email'] = '';
        }
        echo Zend_Json::encode($return);
        exit;

    }// end function
	
    public function doLoginAction(){
        $Auth = new Base_Auth_Auth();
        $Auth->doLogout();
        $return = array('err'=>'0');
        $loginStatusEmail = true;
        $loginStatusUsername = true;
        $params['email'] = $this->_getParam('username');
        $params['password'] = $this->_getParam('password');
        $params['rememberMe'] = $this->_getParam('rememberMe');

        $loginStatusEmail = $Auth->doLogin($params, 'email');
        if ($loginStatusEmail == false) {
            $loginStatusUsername = $Auth->doLogin($params, 'username');
        }

        if ($loginStatusEmail == false && $loginStatusUsername == false) {
            $return['err'] = 1;
            $return['msg'] = "<li>".$this->view->lang[325]."</li>";
            echo Zend_Json::encode($return);
            exit();
        } else {
            if ($params['rememberMe'] == 1) {
                $Auth->remeberMe(true, $params);
            }
            $this->getPatientDetailsAction();
        }
        
    }

	
    public function createAppointmentAction(){
       
        $return = array();
        
        $return['err'] = 0;
        $drid = $this->_getParam('drid');
        $name = $this->_getParam('name');
        $lastname = $this->_getParam('lastname');
        $zipcode = $this->_getParam('zipcode');
        $phone = $this->_getParam('phone');
        $email = $this->_getParam('email');
		$newemail = $this->_getParam('newemail');
        $month = $this->_getParam('month');
        $day = $this->_getParam('day');
        $year = $this->_getParam('year');
        $password = $this->_getParam('newpassword');
        $gender = $this->_getParam('gender');
        $firstVisit = $this->_getParam('first_visit');
        $status = $this->_getParam('status');
        $appointTime = $this->_getParam('appointment_time');		
        $appointmentDate = $this->_getParam('appointment_date');
        $needs = $this->_getParam('needs');
        $reason = $this->_getParam('reason');
        $insuranceCompany = $this->_getParam('insurance_company');
        $insurancePlan = $this->_getParam('insurance_plan');
        
        $appointTime = date("H:i", strtotime($appointTime));
		
        $appointmentNs = new Zend_Session_Namespace("appointment");
        $appointmentNs->appointmentId = 0;
        $randTime = strtotime("$appointmentDate $appointTime");
		if($randTime<time()){
            $return['err'] = 1;
            $return['msg'] = "<li> ".$this->view->lang[400]." </li><li>".$this->view->lang[401]."</li>";
            echo Zend_Json::encode($return);
            exit();
        }
		$Appointment = new Application_Model_Appointment();
        $appObject = $Appointment->fetchRow("appointment_date='$appointmentDate' AND appointment_time='$appointTime' AND doctor_id='{$drid}' AND deleted!=1");
        if(!empty($appObject)){
            $return['err'] = 1;
            $return['msg'] = "<li>".$this->view->lang[402]."<li></li>".$this->view->lang[402]."</li>";
            echo Zend_Json::encode($return);
            exit();
        }



        if($status=='n'){
            $Auth = new Base_Auth_Auth();
            $Auth->doLogout();
        }
        

        $userId = 0;
        $usersNs = new Zend_Session_Namespace("members");
      
        if($status=='e' && (isset($usersNs->userType) && $usersNs->userType=='patient')){
            $userId = $usersNs->userId;
        }elseif($status=='n'){
            $return['email'] = $newemail; // if new user the return with email address
			
			$User = new Application_Model_User();
            if (true === $User->isExist("email='{$newemail}'")) { //should allready have passed this
                $return['err'] = 1;
                $return['msg'] = "<li>".$this->view->lang[391]."</li>";
            } else {
                $User->setEmail($newemail);
                $User->setUsername($newemail);
                $User->setFirstName($name);
                $User->setLastName($lastname);
                $User->setUserLevelId(3); // for patient
                $User->setSendEmail(1);
                $User->setLastVisitDate(time());
                $User->setStatus('active');
                $User->setPassword(md5($password));
                $userId = $User->save();
                if(!$userId){
                    $return['err'] = 1;
                    $return['msg'] = "<li>".$this->view->lang[403]."</li>";
                } else {
                    $Patient = new Application_Model_Patient();
                    $Patient->setUserId($userId);
                    $Patient->setName($name." ".$lastname);
                    $Patient->setZipcode($zipcode);
                    $Patient->setAge($age);
                    $Patient->setGender($gender);
                    $Patient->setPhone($phone);
                    $Patient->setLastUpdated(time());
					$Patient->setMonthDob($month);
					$Patient->setDateDob($day);
					$Patient->setYearDob($year);
                    $patientId = $Patient->save();
                    if(!$patientId){
                        $return['err'] = 1;
                        $return['msg'] = $this->view->lang[404];
                    }
                }
            }
            if($return['err'] == 1){
                echo Zend_Json::encode($return);
                exit();
            }
            $Auth = new Base_Auth_Auth();
            $Auth->doLogout();
            $loginStatusEmail = false;
            $params['email'] = $newemail;
            $params['password'] = $password;
            $loginStatusEmail = $Auth->doLogin($params, 'email');

            $Mail = new Base_Mail('UTF-8');
            $options = array();
            $options['email'] = $email;
            $options['password'] = $password;
            $options['first_name'] = $name;
            $Mail->sendPatientRegistrationMail($options);
        }
       
        if($return['err'] >0){
            echo Zend_Json::encode($return);
            exit();
        }

        /*------------------------Start Insert Appointment ------------------------------*/
        $User = new Application_Model_User();
        $age = $User->getAge(array('month'=>$month,'day'=>$day,'year'=>$year));
       
        $Appointment->setUserId($userId);
        $Appointment->setFname($name);
		$Appointment->setLname($lastname);
        $Appointment->setZipcode($zipcode);
        $Appointment->setPhone($phone);
        $Appointment->setEmail($email);
        $Appointment->setAge($age);
        $Appointment->setGender($gender);
        $Appointment->setFirstVisit($firstVisit);
        $Appointment->setPatientStatus($status);
        $Appointment->setAppointmentDate($appointmentDate);	
        $Appointment->setAppointmentTime($appointTime);
        $Appointment->setBookingDate(time());
        $Appointment->setDoctorId($drid);
        $Appointment->setReasonForVisit($reason);
        $Appointment->setNeeds($needs);
        $Appointment->setInsurance($insuranceCompany);
        $Appointment->setPlan($insurancePlan);
        $Appointment->setMonthDob($month);
        $Appointment->setDateDob($day);
        $Appointment->setYearDob($year);
        $Appointment->setAppointmentType('1');
        $Appointment->setCancelledBy('0');
        $appointmentId = $Appointment->save();
        
        //$appointmentId = 1;
        /*------------------------End Insert Appointment ------------------------------*/
        
        if(!$appointmentId){
            $return['err'] = 1;
            $return['msg'] = "<li>".$this->view->lang[403]."</li>";
            echo Zend_Json::encode($return);
            exit();
        }
        
        $appointmentNs->appointmentId = $appointmentId;// update appointment session id
        
        /*------------------------Start Appointment Email ------------------------------*/
        $options = array();
        $options['email'] = $email;
         
        $options['name'] = $name;
        $options['lastname'] = $lastname;
        $options['date'] = $appointmentDate;
        $options['time'] = $appointTime;
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);
       
        $options['doctor'] = $docObject->getFname();
        $options['office'] = $docObject->getCompany();
        $options['address1'] = $docObject->getStreet();
        $options['address2'] = $docObject->getCity().', '.$docObject->getState().' '.$docObject->getZipcode();
        $options['phone'] = $docObject->getActualPhone();
        $options['membership_level'] = $docObject->getMemberShipLevel();
        $options['PTPhone'] = $phone;

        
       $AdminMail = new Base_Mail('UTF-8');
       $AdminMail->sendAdministratorAppointmentBookingMail($options); // email to site administrator
        
       $Mail = new Base_Mail('UTF-8');
      
       $Mail->sendPatientAppointmentBookingMail($options);
       
       
        
         /*------------------------End Appointment Email ------------------------------*/

        $return['app_id'] = $appointmentId;
		$return['options'] = $options;
        $this->sendNewAppointmentEmail($appointmentId);
		$return['name'] = $name;
		$return['lastname'] = $lastname;
		$return['zipData'] = $zipcode;
		$return['phoneData'] = $phone;		
		$return['emailData'] = $email;		
		$return['genderData'] = $gender;	
		$return['birthData'] = $day."/".$month."/".$year;	
		$return['reasonData'] = $reason;	
		$return['needsData'] = $needs;	
		$return['insuranceCompanyData'] = $insuranceCompany;	
		
		
	   
        if($userId){
            $return['msg'] = $this->view->lang[405];
        }else{
            $return['msg'] = $this->view->lang[406];
        }
        echo Zend_Json::encode($return);
        exit();
    }

    
public function checkAppointmentStatusAction(){
    $appointmentNs = new Zend_Session_Namespace("appointment");
    $return = array();
    $userId = 0;
    if($appointmentNs->appointmentId){
        $drid = $this->_getParam('drid');
        $email = $this->_getParam('email');
        $Appointment = new Application_Model_Appointment();
        $appObject = $Appointment->fetchRow("id={$appointmentNs->appointmentId} AND doctor_id={$drid} AND email ='{$email}'");
        if(!empty($appObject)){ // if appointment posted
            $usersNs = new Zend_Session_Namespace("members");
            $status = $this->_getParam('status');
            if($status=='e' && (isset($usersNs->userType) && $usersNs->userType=='patient')){
                $userId = $usersNs->userId;
            }elseif($status=='n'){
                $return['email'] = $email;
            }
            $return['app_id'] = $appointmentNs->appointmentId;
            $return['err'] = 0;
            if($userId){
                $return['msg'] = $this->view->lang[405];
            }else{
                $return['msg'] = $this->view->lang[406];
            }
        }else{
            $return['err'] = 1;
            $return['msg'] = "<li>".$this->view->lang[403]."</li>";
        }
    }else{
       $return['err'] = 1;
       $return['msg'] = $this->view->lang[407];
    }
    echo Zend_Json::encode($return);
    exit();
}

public function sendtodoctorAction($ids) {
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $gender = $this->_getParam("gender");
        $status = $this->_getParam("status");
        $approved = $this->_getParam("approved");
        $type = $this->_getParam("type");
        $idArray = explode(',', $ids);
        $model = new Application_Model_Appointment();
        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();

        $ReasonForVisite = new Application_Model_ReasonForVisit();

        foreach ($idArray as $id) {
            $object = $model->find($id);

            if($object->getApprove() != 1 && $object->getApprove() != 2){

            $objDoctor = $Doctor->find($object->getDoctorId());
            $objUser = $User->find($objDoctor->getUserId());

            $Doctor_name = $objDoctor->getFname();

            $objReasonForVisite = $ReasonForVisite->find($object->getReasonForVisit());
            $options['doctor_email'] = $objUser->getEmail();
            if($objReasonForVisite){
                $options['reasonforvisit'] = $objReasonForVisite->getReason();
            }else{			
                $options['reasonforvisit'] = $object->getNeeds();
            }
            $options ['office'] = $objDoctor->getCompany();
            $options['doctor_name'] =$options ['doctor']= $Doctor_name;
            $options['pname'] = $objUser->getFirstName()." ".$objUser->getLastName();
            $options['address1'] = $objDoctor->getStreet()."<br>".$objDoctor->getCity().", ".$objDoctor->getCountry()." ". $objDoctor->getZipcode();
            $options['address2'] = "";
            $options ['name'] = $object->getFname();
            $options ['lastname'] = $object->getLname();
			$options ['pname']= $object->getFname().' '.$object->getLname();
            $options ['email'] = $objUser->getEmail();
            $options['phone'] = $object->getPhone();
            $options ['time'] = $object->getAppointmentTime();
            $options ['date'] = $object->getAppointmentDate();
            $options ['PTPhone'] = $object->getPhone();
            $options['email'] = $object->getEmail();
            $options['age'] = $object->getAge();
            $options['dob'] = date('d-M-Y', strtotime("{$object->getDateDob()}-{$object->getMonthDob()}-{$object->getYearDob()}"));
            $options['zipcode'] = $object->getZipcode();
            $options['day'] = date('l',strtotime($object->getAppointmentDate()));
            $options['date'] = $object->getAppointmentDate();
            $options['time'] = $object->getAppointmentTime();
            $options['gender'] = $model->getFullGender("id={$id}");
            $options['patient_status'] = $model->getFullPatientStatus("id={$id}");
 
            $object->setApprove('3');
            $mail_counter=$object->getMailCounterForDoctor();
			$insurance_name="";
			$plan_name="";
			$insuranceObject = new Application_Model_InsuranceCompany();
			$insurance_id = $object->getInsurance();
			$plan_id = $object->getPlan();
			if($insurance_id>0)
			{
				$objInsurance =  $insuranceObject->find($insurance_id);
				if($objInsurance)
					$insurance_name = $objInsurance->getCompany();
			}
 
			$options['insurance'] = $insurance_name;

            $mail_counter++;
			$Mail = new Base_Mail('UTF-8');
           
			$Mail->sendDoctorAppointmentBookingMail($options);
           

            $object->save();
        }
	}
}

    public function sendNewAppointmentEmail($id){
        $Appointment = new Application_Model_Appointment();
        $Doctor = new Application_Model_Doctor();
        $User = new Application_Model_User();
        $ReasonForVisit = new Application_Model_ReasonForVisit();
        
        $object = $Appointment->find($id);

        if($object->getApprove() != 1 && $object->getApprove() != 2){

            $objDoctor = $Doctor->find($object->getDoctorId());
            $objUser   = $User->find($objDoctor->getUserId());
            $Doctor_name = $objDoctor->getFname();

            $objReasonForVisit = $ReasonForVisit->find($object->getReasonForVisit());
            $options['doctor_email'] = $objUser->getEmail();
            if($objReasonForVisit){
                $options['reasonforvisit'] = $objReasonForVisit->getReason();
            }else{
                $options['reasonforvisit'] = $object->getNeeds();
            }
            $options ['office'] = $objDoctor->getCompany();
            $options['doctor_name'] =$options ['doctor']= $Doctor_name;
            $options['pname'] = $objUser->getFirstName()." ".$objUser->getLastName();
            $options['address1'] = $objDoctor->getStreet()."<br>".$objDoctor->getCity().", ".$objDoctor->getCountry()." ". $objDoctor->getZipcode();
            $options['address2'] = "";
            $options ['name'] = $options ['pname']= $object->getFname().$object->getLname();
            $options ['email'] = $objUser->getEmail();
            $options['phone'] = $object->getPhone();
            $options ['time'] = $object->getAppointmentTime();
            $options ['date'] = $object->getAppointmentDate();
            $options ['PTPhone'] = $object->getPhone();
            $options['email'] = $object->getEmail();
            $options['age'] = $object->getAge();
            $options['dob'] = date('d-M-Y', strtotime("{$object->getDateDob()}-{$object->getMonthDob()}-{$object->getYearDob()}"));
            $options['zipcode'] = $object->getZipcode();
            $options['day'] = date('l',strtotime($object->getAppointmentDate()));
            $options['date'] = $object->getAppointmentDate();
            $options['time'] = $object->getAppointmentTime();
            $options['gender'] = $Appointment->getFullGender("id={$id}");
            $options['patient_status'] = $Appointment->getFullPatientStatus("id={$id}");

            $object->setApprove('3');
            $mail_counter=$object->getMailCounterForDoctor();
            $insurance_name="";
            $plan_name="";
            $insuranceObject = new Application_Model_InsuranceCompany();
            $insurance_id = $object->getInsurance();
            $plan_id = $object->getPlan();
            if($insurance_id>0)
            {
                $objInsurance =  $insuranceObject->find($insurance_id);
                if($objInsurance)
                 $insurance_name = $objInsurance->getCompany();
            }

            if($plan_id>0)
            {
                $ObjectPlan = new Application_Model_InsurancePlan();
                $objPlan = $ObjectPlan->find($plan_id);
                if(!empty($objPlan))
                {
                  $plan_name = $objPlan->getPlan();
                }
            }
            $options['insurance'] = $insurance_name;
            $options['plan']= $plan_name;

            $mail_counter++;
            $Mail = new Base_Mail('UTF-8');
            $Mail->sendDoctorAppointmentBookingMail($options);

            $object->save();
        }

    }
	
	public function checknewmailAction(){ 
		$email = $this->_getParam('newemail');
		$return['err'] = 0;
		
		$User = new Application_Model_User();
		if (true === $User->isExist("email='{$email}'")) {
			$return['err'] = 1;
			$return['msg'] = "<li>".$this->view->lang[391]."</li>";
			echo Zend_Json::encode($return);
			exit();
		}
        echo Zend_Json::encode($return);
        exit();
	}
	
	
	public function registerPatientAction(){
        
        $email = $this->_getParam('newemail');
        $password = $this->_getParam('newpassword');
        $app_id = $this->_getParam('app_id');

        $return['err'] = 0;
        $return['app_id'] = $app_id;

        $Appointment = new Application_Model_Appointment();
        $appObject = $Appointment->find($app_id);
        if(!empty($appObject)){
            $User = new Application_Model_User();
            if (true === $User->isExist("email='{$email}'")) {
                $return['err'] = 1;
                $return['msg'] = "<li>".$this->view->lang[391]."</li>";

            } else {
                $User->setEmail($email);
                $User->setUsername($email);
                $User->setFirstName($appObject->getFname());
                $User->setLastName($appObject->getLname());
                $User->setUserLevelId(3); // for patient
                $User->setSendEmail(1);
                $User->setLastVisitDate(time());
                $User->setStatus('active');
                $User->setPassword(md5($password));
                $userId = $User->save();
                if(!$userId){
                    $return['err'] = 1;
                    $return['msg'] = "<li>".$this->view->lang[403]."</li>";
                }else{
                    $Patient = new Application_Model_Patient();
                    $Patient->setUserId($userId);
                    $Patient->setName($appObject->getFname()." ".$appObject->getLname());
                    $Patient->setZipcode($appObject->getZipcode());
                    $Patient->setAge($appObject->getAge());
                    $Patient->setGender($appObject->getGender());
                    $Patient->setPhone($appObject->getPhone());
                    $Patient->setInsuranceCompanyId($appObject->getInsurance());
                    $Patient->setMonthDob($appObject->getMonthDob());
                    $Patient->setDateDob($appObject->getDateDob());
                    $Patient->setYearDob($appObject->getYearDob());
                    $Patient->setLastUpdated(time());
                    $patientId = $Patient->save();
                    $appObject->setUserId($userId);
                    $appObject->save();
                    
                    if(!$patientId){
                        $return['err'] = 1;
                        $return['msg'] = "<li>".$this->view->lang[404]."</li>";
						error_log("Problem with registration from Appointment. Code: RegErr2 ".$appObject);                        
                    }
                }
            }
            if($return['err'] == 1){
                echo Zend_Json::encode($return);
                exit();
            }
            $Auth = new Base_Auth_Auth();
            $Auth->doLogout();
            $loginStatusEmail = false;
            $params['email'] = $email;
            $params['password'] = $password;
            $loginStatusEmail = $Auth->doLogin($params, 'email');

            $Mail = new Base_Mail('UTF-8');
            $options = array();
            $options['email'] = $email;
            $options['password'] = $password;
            $options['first_name'] = $appObject->getFname();
            $options['last_name'] = $appObject->getLname();
            $Mail->sendPatientRegistrationMail($options);
        }
        

        $return['msg'] = $this->view->lang[408];
        echo Zend_Json::encode($return);
        exit();
    }

    public function checkRegistrationStatusAction(){
        $appointmentNs = new Zend_Session_Namespace("appointment");
        $userNs = new Zend_Session_Namespace("members");
        $return = array('err'=>0);
        if($userNs->userId){
            $return['app_id'] = $appointmentNs->appointmentId;
            $return['msg'] = $this->view->lang[408];
        }else{
            $return['err'] = 1;
            $return['msg'] = '<li>'.$this->view->lang[403].'</li>';
        }
        echo Zend_Json::encode($return);
        exit();
    }
    public function thankyouAction() {
		$this->_helper->layout->setLayout('dih_wide');
        $appid = $this->_getParam('appid');
        $usersNs = new Zend_Session_Namespace("members");
        
        $Appointment = new Application_Model_Appointment();
        $object = $Appointment->fetchRow("id='{$appid}'");
        if(!empty($object)){
            $Doctor = new Application_Model_Doctor();
            $docObject = $Doctor->find($object->getDoctorId());
            $Category = new Application_Model_Category();
            $catObject = $Category->find($docObject->getCategoryId());

            $reason = '';
            if($object->getReasonForVisit() > 0){
                $ReasonForVisit = new Application_Model_ReasonForVisit();
                $reasonObject = $ReasonForVisit->find($object->getReasonForVisit());
                $reason = $reasonObject->getReason();
            }else{
                $reason = $object->getNeeds();
            }

            $insurance_name = "";
            if($object->getInsurance() > 0){
                $ObjInsurance = new Application_Model_InsuranceCompany();
                $insuranceObject = $ObjInsurance->find($object->getInsurance());
               if(is_object($insuranceObject))
                $insurance_name = $insuranceObject->getCompany();
            }else{
                $insurance_name = "";
            }

            $profileImage = "/images/doctor_image/" . $docObject->getCompanylogo();
            if (!file_exists(getcwd() . $profileImage) || $docObject->getCompanylogo()=='')$profileImage = "/images/doctor_image/noimage.jpg";
            $this->view->profileImage = $profileImage;

            $this->view->doctor  = $docObject;
            $this->view->catObject  = $catObject;
            $this->view->reason  = $reason;
            $this->view->insurance_name = $insurance_name;
        }
        $this->view->object  = $object;
        

    }// end function
	
	public function smsphoneAction(){
		$phone = trim($this->_getParam('smsphone'));
		if(strlen($phone) != 10)
		{
			$return = array('err'=>1, 'msg' => '<li>'.$this->view->lang[409].'</li>');
			echo Zend_Json::encode($return);
			exit();
		}
		if(!is_numeric($phone))
		{
			$return = array('err'=>1, 'msg' => '<li>'.$this->view->lang[410].'</li>');
			echo Zend_Json::encode($return);
			exit();
		}
		$db = Zend_Registry::get("db");
		$usersNs = new Zend_Session_Namespace("members");
		$memberID = $usersNs->userId;
		$sql = 'SELECT id FROM sms_table where (phone=? or userid =?) and DATEDIFF(DATE(time_sent),  CURDATE())=0 and validated=1';
	
		$result = $db->fetchAll($sql, array(addslashes($phone), $memberID));
		
		$settings = new Admin_Model_GlobalSettings();
		$maxAppoints = $settings->settingValue('max_appoints_per_day');
	            
        if(($maxAppoints !=0) && (count($result) > $maxAppoints)) {
            $return = array('err'=>1, 'msg' => '<li>'.$this->view->lang[411].'</li>');
            echo Zend_Json::encode($return);  
            exit();
        }

		$rand = 123456;
		//uncomment for random code generation
		//$rand = rand(100000, 999999);
		

		//error_log("sms code: ".$rand); 
		$username = 'username';
		$password = 'password';
		$from = 'From';
		$to = '01'.$phone;
		$message = $this->view->lang[412].$rand;
		//TODO: to replace with YOUR SMS service, please uncomment the following lines once done
		//$url = "https://www.yoursmsgateway.com/api/http/send.php?username=$username&password=$password&from=$from&message=$message&to=$to"; //sms sending code!
		//$response = file_get_contents($url);
		$return = array('err'=>0);
		$params = array('phone' => addslashes($phone), 'time_sent' => date('Y-m-d H:i:s'), 'validation_code' => $rand, 'validated' => 0, 'userid' => $memberID);
		$db->insert('sms_table', $params);
		echo Zend_Json::encode($return);
        exit();
	}
	
	public function smscodeAction(){
		$usersNs = new Zend_Session_Namespace("members");
		$memberID = $usersNs->userId;
		$phone = $this->_getParam('smsphone');
		$code = $this->_getParam('smscode');
		$sql = "SELECT validation_code FROM sms_table where (phone='".addslashes($phone)."' or userid ='".$memberID."') and DATEDIFF(DATE(time_sent),  CURDATE())=0 and validated=0";
		
		$db = Zend_Registry::get("db");
		$stmt = $db->query($sql);
		$result = $stmt->fetchAll();
		$vcode = $result[0]->validation_code;
		if($code == $result[0]->validation_code) {
			$return = array('err'=>0);
			$sql = 'UPDATE sms_table set validated=1 where phone=? and DATEDIFF(DATE(time_sent),  CURDATE())=0 and validated=0';
			$stmt = new Zend_Db_Statement_Pdo($db, $sql);
			$stmt->execute(array(addslashes($phone)));
		}
		else {
			$return = array('err'=>1, 'msg' => '<li>'.$this->view->lang[413].'</li>');
		}
		echo Zend_Json::encode($return);
        exit();
	}

    public function listAllMonths()
    {
        $arMonths = array(
            ''=>$this->view->lang[900],
            '1'=>$this->view->lang[901],
            '2'=>$this->view->lang[902],
            '3'=>$this->view->lang[903],
            '4'=>$this->view->lang[904],
            '5'=>$this->view->lang[905],
            '6'=>$this->view->lang[906],
            '7'=>$this->view->lang[907],
            '8'=>$this->view->lang[908],
            '9'=>$this->view->lang[909],
            '10'=>$this->view->lang[910],
            '11'=>$this->view->lang[911],
            '12'=>$this->view->lang[912]
        );
      return $arMonths;
    }

}// end class