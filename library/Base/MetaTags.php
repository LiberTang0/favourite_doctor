<?php
class Base_MetaTags {

    public function setMetaTags() {

        
        $FrontController = Base_Controller_Front::getInstance();
        $controllerName = $FrontController->getRequest()->getControllerName();
        $actionName = $FrontController->getRequest()->getActionName();
        $request = $FrontController->getRequest()->getParams();
		$actual_url = addslashes($FrontController->getRequest()->getRequestUri());

        $setting = new Admin_Model_GlobalSettings();
		$title = $setting->settingValue('meta_title');
		
		$seoUrlM = new Application_Model_SeoUrl();

        $metaTagObject = $seoUrlM->fetchRow("`actual_url`='{$actual_url}' AND ((`meta_title`!='' AND `meta_title` IS NOT NULL) AND (`meta_description`!=''  AND `meta_description` IS NOT NULL) AND `meta_status`!=0)");

        if ($metaTagObject){
            $return['title'] = stripslashes(stripslashes($metaTagObject->getMetaTitle()));
            $return['keywords'] = stripslashes(stripslashes($metaTagObject->getMetaKeywords()));
            $return['description'] = stripslashes(stripslashes($metaTagObject->getMetaDescription()));
           
            return $return;
        }

        $metaTagObject = $seoUrlM->fetchRow("`actual_url`='{$actual_url}'");
               
        
        switch ($controllerName) {
            case "search":

            	if (isset($request['category']) && $request['category'] > 0){
            		 $category = new Application_Model_Category();
            		 $specialty_row = $category->fetchRow("status = 1 AND id = ".$request['category']);
            	     if(empty($specialty_row)){
            		 	return false;
            		 }
            		 $specialty = $specialty_row->getName();
            	}

                if ((isset($request['category']) && $request['category'] > 0) && (isset($request['search1']) && $request['search1'] != '') && !isset($request['insurance'])) {
                    // Category - City/Zipcode

                    if (is_int($request['search1'])) {
                        // Zipcode
                       return $this->setCategoryZipcodeMeta($metaTagObject,$specialty,$request['search1']);
                    } else {
                        // City

                        return $this->setCategoryCityMeta($metaTagObject,$specialty,$request['search1']);
                    }
                } elseif ((isset($request['category']) && $request['category'] > 0) && (isset($request['reason']) && $request['reason'] > 0)) {
                    // Category - Reason for visit
                    	$reason = new Application_Model_ReasonForVisit();
                    	$res = $reason->find($request['reason']);

                      	return $array = $this->setReasonVisitMeta($metaTagObject,$res->getReason());
                } elseif ((isset($request['category']) && $request['category'] > 0) && (isset($request['insurance']) && $request['insurance'] > 0)) {
                    // Category - Insurance

                    if(isset($request['search1']) && !is_int($request['search1'])){
                        // Category - Insurance - City
                        $miscellaneous['catid'] = $request['category'];
                        $miscellaneous['insurance'] = $request['insurance'];
                        $miscellaneous['city'] = $request['search1'];
                        return $this->setCategoryInsuranceCityMeta($metaTagObject, $miscellaneous);
                    }else{
                         $insurance_comp = new Application_Model_InsuranceCompany();
            		 $insurance_row = $insurance_comp->fetchRow("status = 1 AND id = ".$request['insurance']);
            		 $insurance = $insurance_row->getCompany();
            		 if($request['category'] == 7){
            		 	return $this->setCategoryInsuranceMeta($metaTagObject,$insurance);
            		 }else{
            		 	return $this->setCategoryInsuranceOthersMeta($metaTagObject,$insurance);
            		 }
                    }

                } elseif (!isset($request['category']) && !isset($request['insurance']) && !isset($request['reason'])
                        && (isset($request['search1']) && $request['search1'] !='')) {
                    // City / Zipcode
                    if (is_int($request['search1'])) {
                        // Zipcode
                        return $array= $this->setZipcodeMeta($request['search1']);
                    } else {
                        // City
                        return $array= $this->setCityMeta($metaTagObject, $request['search1']);
                    }
                } elseif ((isset($request['category']) && $request['category'] > 0)
                        && !isset($request['search1']) && !isset($request['insurance']) && !isset($request['reason'])) {
                    // Category
                    	return $array= $this->setCategoryMeta($metaTagObject, $specialty);
                }
                elseif ((isset($request['insurance']) && $request['insurance'] > 0 && (isset($request['plan'])) && $request['plan'] > 0)
                        && !isset($request['search1']) && !isset($request['category']) && !isset($request['reason']) && !isset($request['search'])) {
                    // Category
                        
                         $insurance_comp = new Application_Model_InsuranceCompany();
                         $Objinsurance = $insurance_comp->fetchRow("status =1 and id=".$request['insurance']."");
                         $insurance_plan  = new Application_Model_InsurancePlan();
                         $ObjInsurance_plan = $insurance_plan->fetchRow("status =1 and id=".$request['plan']."");
                         
                         
                         
                         
                         
                    	return $array= $this->setInsurancePlanMeta($metaTagObject,  $Objinsurance->getCompany(),$ObjInsurance_plan->getPlan());
                }
                break;
            case "index":
            	if(isset($request['action']) && $request['action'] == "about-us"){
            		return $array = $this->setAboutMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "register"){
            		return $array = $this->setRegisterMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "patient-registration"){
            		return $array = $this->setPatientRegistrationMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "registration"){
            		return $array = $this->setDoctorRegistrationMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "login"){
            		return $array = $this->setLoginMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "privacy-policy"){
            		return $array = $this->setPrivatePolicyMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "terms"){
            		return $array = $this->setTermsOfUseMeta($metaTagObject);
            	}elseif(isset($request['action']) && $request['action'] == "index"){
            		return $array = $this->setTopHomeMeta($metaTagObject);
            	}

            	break;
			case "sitemap":
            	if(isset($request['action']) && $request['action'] != ""){
            		return $array = $this->setSitemapMeta($metaTagObject);
            	}
            	break;
			case "profile":
            	if(isset($request['action']) && $request['action'] == "index" && isset($request['id']) && $request['id'] > 0){
                     
            		$Doctor = new Application_Model_Doctor();
            		$docObject = $Doctor->find(trim($request['id']));
            		
            		$array = array();
                       
            		if($docObject){
                           
                            if($docObject->getMembershipLevel()=='Comingsoon'){
                                $array = $this->setDoctorComingsoonProfileMeta($metaTagObject, $docObject );
                            }else{
                                
                                $array = $this->setDoctorProfileMeta($metaTagObject, $docObject );
                                
                               
                            }
            			
            			return $array;
            		}

            	}
            	break;
        }

    }
	protected function saveMeta($metaTagObject,$title,$keywords,$description){
            if(empty($metaTagObject)) return false;
            $metaTagObject->setMetaTitle($title);
            $metaTagObject->setMetaKeywords($keywords);
            $metaTagObject->setMetaDescription($description);
            $metaTagObject->setMetaStatus(1);
            $metaTagObject->save();

	}
        protected function setCategoryInsuranceCityMeta($metaTagObject, $miscellaneous) {

            if($miscellaneous['catid']!=7)return true; // for time being its only for Dentist
            $insurance_comp = new Application_Model_InsuranceCompany();
            $insurance_row = $insurance_comp->fetchRow("status = 1 AND id = ".$miscellaneous['insurance']);
            if(!$insurance_row)return false;
            $insurance = trim($insurance_row->getCompany());
            $Category = new Application_Model_Category();
            $catObject = $Category->find($miscellaneous['catid']);
            if(!$catObject)return false;
            $catName = $catObject->getName();

            $title = "{$insurance} {$miscellaneous['city']}, {$catName} {$insurance} {$miscellaneous['city']} | ".$title."";
            $description = "{$insurance} {$miscellaneous['city']}: Find  {$catName} that accepts {$insurance} at {$miscellaneous['city']} at ".$title."";
            $keywords = "";
                    $this->saveMeta($metaTagObject,$title,$keywords,$description);

            return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);

	}
	protected function setCategoryInsuranceMeta($metaTagObject,$insurance) {

        $insurance = trim($insurance);
        if(empty($insurance))return true;

        $title = "{$insurance}. Find a doctor that accepts {$insurance} | ".$title."";
        $description = "{$insurance}. Find a doctor that accepts {$insurance} at ".$title."";
        $keywords = "";
	$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);

	}
	protected function setCategoryInsuranceOthersMeta($metaTagObject,$insurance) {

        $insurance = trim($insurance);
        if(empty($insurance))return true;

        $title = "{$insurance} Find a doctor that accepts {$insurance} | ".$title."";
        $description = "{$insurance}: Find a doctor that accepts {$insurance}. ";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);

	}

        public function setDoctorProfileMeta($metaTagObject, $docObject) {


        if(empty($docObject))return true;
        $fname = trim($docObject->getFname());
        $specialty = "";

        if(empty($fname))return true;
        $Doctor = new Application_Model_Doctor();

        $specialty = $Doctor->getDoctorCategoryList($docObject->getId());

		$title = "{$fname}, {$docObject->getCity()} {$specialty} | Make an appointment at area {$docObject->getCity()}, {$docObject->getState()} {$docObject->getZipcode()} ";
        $keywords = "";

	$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }

     public function setDoctorComingsoonProfileMeta($metaTagObject, $docObject) {


        if(empty($docObject))return true;
        $fname = trim($docObject->getFname());
        $specialty = "";

        if(empty($fname))return true;
        $Doctor = new Application_Model_Doctor();

        $specialty = $Doctor->getDoctorCategoryList($docObject->getId());

        $title = "{$fname}, {$docObject->getCity()} {$specialty} | Free Online Booking!";
        $description = "Make an appointment with {$fname} {$docObject->getAssignPhone()} or with {$docObject->getCompany()}, at {$docObject->getCity()}, {$docObject->getState()} {$docObject->getZipcode()} at ".$title."!";
        $keywords = "";

		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setProfile1Meta($metaTagObject, $docObject) {
        if(empty($docObject))return true;
        $fname = trim($docObject->getFname());
        $specialty = "";

        if(empty($fname))return true;

       
        $title = "{$fname}, {$docObject->getCity()} {$specialty} | ".$title."!";

        $description = "Make appointment with {$fname} {$docObject->getActualPhone()} or {$docObject->getCompany()}, at {$docObject->getCity()}, {$docObject->getState()} {$docObject->getZipcode()} at ".$title."!";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setProfile2Meta($metaTagObject, $fname = null, $company= null, $specialty= null, $city = null, $state = null , $zipcode = null) {
        $fname = trim($fname);
        if(empty($fname))return true;

        $title = "{$fname}, {$company} | ".$title." {$specialty} {$city} {$state} {$zipcode}";
        $description = "Make appointment with {$fname} or {$company}, at {$city}, {$state} {$zipcode} ".$title.".";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setProfile3Meta($metaTagObject, $fname = null, $company= null, $specialty= null, $city = null, $state = null , $zipcode = null) {
        $fname = trim($fname);
        if(empty($fname))return true;

        $title = "{$fname}, {$company} | ".$title." {$specialty} {$city} {$state} {$zipcode}";
        $description = "Make an appointment with {$fname} or {$company}, at {$city}, {$state} {$zipcode} ".$title.".";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setCategoryMeta($metaTagObject, $specialty = null) {
        $specialty = trim($specialty);
        if(empty($specialty))return true;

        $title = "{$specialty}, Find {$specialty} | ".$title."";
        $description = "{$specialty}: Find {$specialty} . Choose {$specialty} for you";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setCategoryZipcodeMeta($metaTagObject, $specialty = null, $zipcode=null) {
        $specialty = trim($specialty);
        $zipcode = trim($zipcode);
        if(empty($zipcode) || empty($specialty))return true;

        $title = "{$zipcode} {$specialty}, Find {$specialty} at {$zipcode} | ".$title."";
        $description = "{$zipcode} {$specialty}: Find {$specialty} at {$zipcode}.";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setCategoryCityMeta($metaTagObject, $specialty = null, $city=null) {
        $specialty = trim($specialty);
        $city = trim($city);
        if(empty($city) || empty($specialty))return true;

        $title = "{$city} {$specialty}, Find {$specialty} at {$city} | ".$title."";
        $description = "{$city} {$specialty}: Find {$specialty} at {$city}.";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setCityMeta($metaTagObject, $city=null) {
        $city = trim($city);
        if(empty($city))return true;

        $title = "Doctors at {$city} | ".$title."";
        $description = "Doctors at {$city}.";
        $keywords = "";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }

    protected function setZipcodeMeta($metaTagObject,$zipcode=null) {
        $zipcode = trim($zipcode);
        if(empty($zipcode))return true;

        $title = "Doctors at {$zipcode}. | ".$title."";
        $keywords = "Doctors at {$zipcode}: Choose doctors from {$zipcode} ";
        $description = "{$zipcode}: Find doctors at {$zipcode}!";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);
        return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setAboutMeta($metaTagObject){
    	$title = "About ".$title."";
    	$keywords = "";
    	$description =  "Book doctor now!";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setRegisterMeta($metaTagObject){
    	$title = "Register | ".$title."";
    	$keywords = "";
    	$description =  "Register at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setPatientRegistrationMeta($metaTagObject){
    	$title = "Patient Registration | ".$title."";
    	$keywords = "";
    	$description =  "Register now! It's free! | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setDoctorRegistrationMeta($metaTagObject){
    	$title = "doctor Registration | ".$title."";
    	$keywords = "";
    	$description =  "Register now | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setLoginMeta($metaTagObject){
    	$title = "Login | ".$title."";
    	$keywords = "";
    	$description =  "Login at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setPrivatePolicyMeta($metaTagObject){
    	$title = "Privacy Policy | ".$title."";
    	$keywords = "";
    	$description =  "Privacy Policy ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setTermsOfUseMeta($metaTagObject){
    	$title = "Terms of use | ".$title."	";
    	$keywords = "";
    	$description =  "Terms of use | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setTopCityPage1Meta($metaTagObject){
    	$title = "".$title."- Book online  Doctor| ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book Doctors at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setTopCityPage2Meta($metaTagObject){
    	$title = "".$title."- Book Doctor Online | ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctor at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setTopCityPage3Meta($metaTagObject){
    	$title = "".$title."- Book doctor Online | ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctors at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setTopCityPage4Meta($metaTagObject){
    	$title = "".$title."- Book doctor Online | ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctors | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
	protected function setTopCityPage5Meta($metaTagObject){
    	$title = "".$title."- Book doctors | ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctors at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }

	protected function setTopCityPage6Meta($metaTagObject){
    	$title = "".$title."- Book doctors | ".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctors | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setTopHomeMeta($metaTagObject){
    	$title = "".$title."";
    	$keywords = "";
    	$description =  "".$title."- Book doctors | ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);

    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setReasonVisitMeta($metaTagObject,$reasonForVisit = null){
    	$title = "{$reasonForVisit}, Book Doctor at {$reasonForVisit} | ".$title."";
    	$keywords = "";
    	$description =  "{$reasonForVisit}, Book Doctor for  {$reasonForVisit} ".$title.", Book doctor now";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);
    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }
    protected function setSitemapMeta($metaTagObject){
    	$title = "Sitemap | ".$title."";
    	$keywords = "";
    	$description =  "Sitemap at ".$title."";
		$this->saveMeta($metaTagObject,$title,$keywords,$description);
    	return array('title'=>$title,'keywords'=>$keywords,'description'=>$description);
    }

}// end class