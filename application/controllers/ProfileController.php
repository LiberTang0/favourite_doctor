<?php

class ProfileController extends Base_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        /* $uri=$this->_request->getPathInfo();
          $activeNav=$this->view->navigation()->findByUri($uri);
          $activeNav->active=true; */
    }

     public function preDispatch() {
        parent::preDispatch();
        $this->_helper->layout->setLayout('dih_wide');
    }

  
/**
 * @this function will check that which doector don't have seo url then it creates the url for that doctor
 */
      public function DoctorSeoUrl($real_url, $object) {
        $seoUrlM=new Application_Model_SeoUrl();
        $string = $seoUrlM->retrieveSeoUrl($real_url);
        return true;
    }


    public function indexAction() {
		$id = $this->_getParam('id');
		$allAwards = array();
		$request = $this->getRequest();
		
		//plugin on off
		$settings = new Admin_Model_GlobalSettings();
		$this->view->rev = $settings->settingValue('rev_plugin');
                  
		//Fetch Profile Data
		$Doctor = new Application_Model_Doctor();
		$profileobject = $Doctor->fetchRow("status=1 AND id=$id");
		$actual_url = "/profile/index/id/".$id;
		$this->DoctorSeoUrl($actual_url,$profileobject);
		if(empty($profileobject)){
			$this->_redirect('/404');
		}
		$this->view->profiledata = $profileobject;
		$profileImage = "/images/doctor_image/" . $profileobject->getCompanylogo();
		if (!file_exists(getcwd() . $profileImage) || $profileobject->getCompanylogo()=='')$profileImage = "/images/doctor_image/noimage.jpg";
		$this->view->profileImage = $profileImage;
		$this->view->logo = $profileobject->getCompanylogo();

		//Fetch Category Data
		$DocCategory = new Application_Model_DoctorCategory();
		$categoryArr = $DocCategory->getDoctorCategories("doctor_id='{$id}'");
               
		//Fetch Category Data
		$DocExtraCategory = new Application_Model_DoctorExtraCategory();
		$categoryExtraArr = $DocExtraCategory->getDoctorCategories("doctor_id='{$id}'");
		asort($categoryArr);
		$this->view->categorydata = $categoryArr;
		//extracategory
		asort($categoryExtraArr);
		$this->view->categoryExtradata = $categoryExtraArr;

		//Fetch Insurance Accepted
		$modeldoctor_insurance = new Application_Model_DoctorInsurance();
		$ArrDoctorInsurance=$modeldoctor_insurance->getDoctorinsurance("doctor_id={$id}");
		$InsuranceCompany = new Application_Model_InsuranceCompany();
		$model_hospital_affiliation =new Application_Model_DoctorHospitalAffiliation();
		$arrdoctorHA = $model_hospital_affiliation->getDoctorHospitalAffiliate("doctor_id={$id}");
				
		$this->view->hospitalAffiliation =$arrdoctorHA;
		
		$insurancedata = array();
		foreach($ArrDoctorInsurance as $key=>$value) {
			$insuranceobject = $InsuranceCompany->find($value);
			if($insuranceobject)$insurancedata[$insuranceobject->getId()]=$insuranceobject->getCompany();
		}
		asort($insurancedata);
		$this->view->insurancedataArr = $insurancedata;
		$planSelected = false;

		$association = array();
		$DocAssociation = new Application_Model_DoctorAssociation();
		$assObject = $DocAssociation->fetchAll("doctor_id='{$id}'");
		if(!empty($assObject)){
			$array = array();
			foreach($assObject as $ass){
				$array[] = $ass->getAssociationId();
			}
			$str = implode(",",$array);

			$Association = new Application_Model_Association();
			$association = $Association->fetchAll("id IN ($str)");
		}
		$staticAwards = array();
		$allAwards = array();
		$award_id = 0;
	  
		$DocAward = new Application_Model_DoctorAward();
		$awardObject = $DocAward->fetchAll("doctor_id='{$id}'");

		if(!empty($awardObject)) {
			$arawardid = array();
			$staticawardid = array();
			$str_award=0;
			$str_statis_award=0;
			foreach($awardObject as $award) {
				$static_awards=array(244,245,246,247,248,249);
				$award_id =$award->getAwardId();
			   
				if(in_array($award_id,$static_awards))
					$staticawardid[] = $award->getAwardId();
				
			}
                       

			if(count($staticawardid)>0)
				$str_statis_award =implode(", ",$staticawardid);

			$Award = new Application_Model_Award();
			if(empty($str_statis_award))
				$str_statis_award = 0;
			$staticAwards = $Award->fetchAll("id in ({$str_statis_award})");
		}
		$this->view->associations = $association;
	  
		$this->view->textAward = $profileobject->getTextAward();
		$this->view->staticAwards = $staticAwards;
		
		/* review */
		$modeldoctorreview = new Application_Model_DoctorReview();
		$this->view->viewreviewobject = $modeldoctorreview; 
		$request = $this->getRequest();		 
		$reviewobject = $modeldoctorreview->fetchAll("status=1 and doctor_id={$id}", "added_on ASC");
		$this->view->reviewobjectdata = $reviewobject;
		/* /review */
		
		$categoryobject = array();
		$DoctorCategories = new Application_Model_DoctorCategory();
		$DoctorCatObject = $DoctorCategories->fetchAll("doctor_id={$profileobject->getId()}");
		if(!empty($DoctorCatObject)){
			$Category = new Application_Model_Category();
			foreach($DoctorCatObject as $DoctorCatObj){
				$categoryobject = $Category->find($DoctorCatObj->getCategoryId());
				if($categoryobject)break;
			}
		}
    }
	
	/* review */
	public function addReviewAction(){
        $request = $this->getRequest();
        $return['flag'] = 0;
        if ($request->isPost()) {
            $options = $request->getPost();
            $Review = new Application_Model_DoctorReview();
            $Review->setVote($options['rvote']);
            $Review->setDoctorId($options['drid']);
            $Review->setTitle($options['revTitle']);
            $Review->setReview($options['sobireview']);
            $Review->setUsername($options['uname']);
            $Review->setEmail($options['umail']);
            $Review->setIp($_SERVER['REMOTE_ADDR']);
            $Review->setAddedOn(time());

            $Review->save();
            $array = $Review->getRatingReviews($options['drid']);
            $return['msg'] = '<b>Thanks for your review ...</b>';
            $return['image'] = $array['image'];
            $return['votes'] = $array['votes'];
            $return['rating'] = '1';
            $return['flag'] = 1;
        }else{
            $return['msg'] = '<b>There is some problem. Please try later...</b>';
        }
        echo json_encode($return);exit();
    }
	/* /review */
	
    public function ratingImageAction() {
		$Review = new Application_Model_DoctorReview();
		$image = $Review->ratingImage($this->_getParam('vote'));
		$return['image'] = $image;
		$return['vote'] = $this->_getParam('vote');
       
        echo json_encode($return);exit();
    }

    public function setReasonforvisitAction(){
		$reasonNamespace = new Zend_Session_Namespace('reason');
		$reasonNamespace->reasonforvisit = $this->_getParam('reason');
		die('1');
    }

	public function timeslotAction(){
        $post = array();
        $post['drid']       = $this->_getParam('drid');
        $post['start_date'] = $this->_getParam('start_date');
        $post['disp'] = $this->_getParam('disp');// dispaly 'more...' link
        $post['type'] = 1; // type '0' for doctor listing page.

        $Search = new Base_Timeslot();
        $Search->getAppointmentAvailability($post);
    }

	public function showIcalAction(){
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        $Doctor =  new Application_Model_Doctor();
        $doctor = $Doctor->fetchRow("user_id=".$id);
        $docId = $doctor->getId();
        //Fetch Profile Data
        $Appointment = new Application_Model_Appointment();
        $object = $Appointment->fetchAll("doctor_id={$docId} AND deleted!=1");
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN"."\r\n";
        if (!empty($object)) {
            foreach ($object as $obj) {
                
                $date = $obj->getAppointmentDate();
                $start_time = strtotime($date.' '.$obj->getAppointmentTime());
                $interval = $this->getInterval($docId, $date);
                $end_time = strtotime("+$interval minutes", $start_time);
                
                $dtstart = gmdate('Ymd', $start_time).'T'. gmdate('His', $start_time) . "Z"; // converts to UTC time
                $dtend = gmdate('Ymd', $end_time).'T'. gmdate('His', $end_time) . "Z"; // converts to UTC time
                $gender = $obj->getGender();
                if($gender == 'm') {$pret='Mr';} else {$pret='Mrs';}
                $summary = 'Appointment with '.$pret. ' '.$obj->getLname().' '.$obj->getFname().', Age: '.$obj->getAge().', email:'.$obj->getEmail().', Reason for visit: '.$obj->getNeeds();
                $ical .= "BEGIN:VEVENT
UID:" . md5($obj->getId()) . "@buscoturno.ar
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:" . $dtstart . "
DTEND:" . $dtend . "
SUMMARY:" . $summary . "
END:VEVENT"."\r\n";
            }
        }

        $ical .= "END:VCALENDAR";
        $this->view->ical = $ical;
        
    }

	
	public function getInterval($drid, $date){
		$timeslot = new Base_Timeslot();
		$weekNumber = $timeslot->fetchSlotWeek($date);
		$slotDay = strtoupper(date('D', strtotime($date)));
		$MasterSlot = new Application_Model_MasterTimeslot();
        $object = $MasterSlot->fetchRow("doctor_id='$drid' AND week_number='{$weekNumber}' AND is_checked='1' AND slot_day='{$slotDay}'", "id ASC");
		if($object){
			return $object->getSlotInterval();
		}
		else{
			$object = $MasterSlot->fetchRow("doctor_id='-1' AND week_number='{$weekNumber}' AND is_checked='1' AND slot_day='{$slotDay}'", "id ASC");
			return $object->getSlotInterval();
		}
	}
	
    public function showTimeslotAction(){
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        //Fetch Profile Data
        $Doctor = new Application_Model_Doctor();
        $profileobject = $Doctor->find($id);
        $this->view->profiledata = $profileobject;

        $profileImage = "/images/doctor_image/" . $profileobject->getCompanylogo();
        if (!file_exists(getcwd() . $profileImage) || $profileobject->getCompanylogo()=='')$profileImage = "/images/doctor_image/noimage.jpg";
        $this->view->profileImage = $profileImage;
        
    }

    public function viewAllInsurancesAction() {
		$this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        //Fetch Profile Data
        $componies = array();
        $db = Zend_Registry::get('db');
		$query = "SELECT c.id comp_id, c.company FROM doctor_insurance di, insurance_companies c
                    WHERE di.insurance_id= c.id AND di.doctor_id='{$id}' ORDER BY company";
        $select = $db->query($query);
        $insurances = $select->fetchAll();
        if(count($insurances)){
            foreach($insurances as $ins){
                $query = "SELECT p.id plan_id, p.plan FROM doctor_insurance_plan dp, insurance_plans p, insurance_companies c
                    WHERE dp.plan_id=p.id AND p.insurance_company_id= c.id AND dp.doctor_id='{$id}' AND c.id={$ins->comp_id} ORDER BY p.plan";
                $select = $db->query($query);
                $plans = $select->fetchAll();
                if(count($plans)){
                    foreach($plans as $p){
                        $componies[$ins->company][] = $p->plan;
                    }
                }else{
                    $componies[$ins->company] = array();
                }
            }
        }
        $this->view->componies = $componies;
    }
	
	public function testAction(){
        $drid = $this->_getParam('drid');
        die("testing for timeslot: $drid");
    }

}// end class
