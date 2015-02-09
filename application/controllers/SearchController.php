<?php
class SearchController extends Base_Controller_Action {


   public function preDispatch() {
        parent::preDispatch();
        $this->_helper->layout->setLayout('dih_wide');
                
    }

	public function indexAction() {

		$this->view->metaTitle = "";
		$this->view->metaDescription = "";
		$this->view->metaKeywords = "";
		$this->view->description = "";

		$selectedCategory = $this->_getParam($this->view->lang[933]);

        $selectedinsurance = $this->_getParam($this->view->lang[935]);
        
        $reasonid = $this->_getParam('reason');
        $sobi2doctorname = addslashes(trim($this->_getParam('doctorname')));
        $area = trim($this->_getParam($this->view->lang[934]));
        $state = trim($this->_getParam('st'));
        $start_date = $this->_getParam('start_date');
 
        $reasons = array();
        $linkArray = array();
        $insuranceCompany = array();
        
        $this->view->selectedCategory = $selectedCategory;
        
        $this->view->selectedinsurance = $selectedinsurance;
        $this->view->reasonid = $reasonid;
        $this->view->doctorname = stripslashes(stripslashes($sobi2doctorname));
        $this->view->area = stripslashes(stripslashes($area));
        $this->view->start_date = $start_date;
        $this->view->isReasontoVisit = 1;

		//plugin on off
		$settings = new Admin_Model_GlobalSettings();
		$this->view->rev = $settings->settingValue('rev_plugin');
        
        // fetch category
        $Category = new Application_Model_Category();
        $categories = $Category->fetchAll("status=1", "name ASC");
        $this->view->categories = $categories;

        if($selectedCategory) {
	        $selectedCat = $Category->fetchRow("name='".$selectedCategory."'");
	        $catid = $selectedCat->getId();
			$this->view->catid = $catid;
	        $this->view->metaTitle .= $selectedCat->getMetatitle();
			$this->view->metaDescription .= $selectedCat->getMetadescription();
			$this->view->metaKeywords .= $selectedCat->getMetakeywords();
			$this->view->description .= $selectedCat->getDescription();
	    } else {
	    	$this->view->metaTitle .= $this->view->lang[950];
			$this->view->metaDescription .= $this->view->lang[951];
			$this->view->metaKeywords .= $this->view->lang[952];
	    }

        // fetch insurance companies

        $Insurance = new Application_Model_InsuranceCompany();
		if($selectedinsurance != "") {
			if($selectedinsurance != $this->view->lang[936]) {
				$tempinsurance = $Insurance->fetchRow("company = '".$selectedinsurance."'");
				$insuranceid = $tempinsurance->getId();

				$this->view->metaTitle .= " ".$this->view->lang[953]." ".$tempinsurance->getMetatitle();
				$this->view->metaDescription .= " ".$this->view->lang[953]." ".$tempinsurance->getMetadescription();
				$this->view->metaKeywords .= ", ".$tempinsurance->getMetakeywords();
				$this->view->description .= " ".$tempinsurance->getDescription();
			} else {
				$insuranceid = -1;
			}
		}

        $insurances = $Insurance->fetchAll(null,"company ASC" );
        $this->view->insurances = $insurances;
		
		//get patient insurance company
		
        $this->view->insuranceCompany = $insuranceCompany;
        
        if($reasonid > 0){
            $linkArray['reason'] = $reasonid;
            $Reasonfor = new Application_Model_ReasonForVisit();
            $reason = $Reasonfor->find($reasonid);

			$this->view->metaTitle .= " ".$reason->getMetatitle();
			$this->view->metaDescription .= " ".$this->view->lang[954]." ".$reason->getMetadescription();
			$this->view->metaKeywords .= ", ".$reason->getMetakeywords();
			$this->view->description .= " ".$reason->getDescription();

        }
        if($area != ''){
            $linkArray['area'] = $area;
            $this->view->metaTitle .= " ".$this->view->lang[955]." ".$area;
			$this->view->metaDescription .= " ".$this->view->lang[955]." ".$area;
			$this->view->metaKeywords .= ", ".$area;
        }
        if($state != ''){
            $linkArray['st'] = $state;
        }
        if($sobi2doctorname!=''){
            $linkArray['doctorname'] = $sobi2doctorname;
        }
        
        // fetch reason for visits
        if($catid>0) {
            $linkArray['category'] = $catid;
            $Reason = new Application_Model_ReasonForVisit();
            $reasons = $Reason->fetchAll("category_id='{$catid}' AND status=1", "reason ASC");
		}
        $this->view->reasons = $reasons;
       
		$searchResults = $this->orderedSearch( $area, $catid, $insuranceid, stripslashes($sobi2doctorname), $reasonid);
	   
	   
	   
        if(isset($searchResults['other']) && count($searchResults['other']) >0){
            $this->view->otherStates = $searchResults['other'];
        }
        if(isset($searchResults['selected']) && $searchResults['selected']!=''){
            $this->view->selectedStates = $searchResults['selected'];
        }
		$this->view->linkArray = $linkArray;
        
		 
        if(count($searchResults) > 0){
            $model = new Application_Model_Doctor();

            $page_size = $settings->settingValue('pagination_size');
            $page = $this->_getParam('page', 1);

            /*$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($searchResults ['sIds']));
            $paginator->setCurrentPageNumber($page);*/

            $pageObj = new Base_Paginator();
            $paginator = $pageObj->arrayPaginator($searchResults, $page, $page_size);

            //$paginator = $pageObj->fetchPageData($model, $page, $page_size, $where);
            $this->view->total = $pageObj->getTotalCount();
            $this->view->paginator = $paginator;
            $this->view->searchResults = $searchResults;
			if($page*$page_size < $pageObj->getTotalCount()){
				$nextPage = intval($page)+1;
				$nextUrl = 'page='.$nextPage;
				if($this->view->area != '') $nextUrl .= '&amp;'.$this->view->lang[934].'='.$this->view->area;
				if($this->view->catid != '') $nextUrl .= '&amp;'.$this->view->lang[935].'='.$this->view->selectedinsurance;
				if($this->view->insuranceid != '') $nextUrl .= '&amp;'.$this->view->lang[935].'='.$this->view->selectedinsurance;
				if($this->view->doctorname != '') $nextUrl .= '&amp;doctorname='.$this->view->doctorname;
				if($this->view->reasonid != '') $nextUrl .= '&amp;reason='.$this->view->reasonid;
				$this->view->nextUrl = $nextUrl;
			}
			if($page!= 1){
				$prevPage = intval($page)-1;
				$prevUrl = 'page='.$prevPage;
				if($this->view->area != '') $prevUrl .= '&amp;'.$this->view->lang[934].'='.$this->view->area;
				if($this->view->catid != '') $prevUrl .= '&amp;'.$this->view->lang[935].'='.$this->view->selectedinsurance;
				if($this->view->insuranceid != '') $prevUrl .= '&amp;'.$this->view->lang[935].'='.$this->view->selectedinsurance;
				if($this->view->doctorname != '') $prevUrl .= '&amp;doctorname='.$this->view->doctorname;
				if($this->view->reasonid != '') $prevUrl .= '&amp;reason='.$this->view->reasonid;
				$this->view->prevUrl = $prevUrl;
			}
		
        }else{
            $this->view->total = 0;
        }
		
		$sitename = $settings->settingValue('meta_title');
        $this->view->metaTitle .= " - ".$sitename;
    }// end function

    

	public function autoseggestAction(){
        
        $q = strtolower($this->_getParam('q'));
        if (!$q) return;
        $db = Zend_Registry::get('db');
        $query = "SELECT name FROM autocomplete WHERE name LIKE ".$db->quote($q.'%')." ORDER BY name ASC";
        $select = $db->query($query);
        $docObject = $select->fetchAll();
		foreach($docObject as $obj){
			echo $obj->name."\n";
		}		
        exit();
    }
	
    public function timeslotAction(){

        $post = array();
        $post['drid']       = $this->_getParam('drid');
        $post['start_date'] = $this->_getParam('start_date');
        $post['type'] = 0; // type '0' for doctor listing page.

        $Search = new Base_Timeslot();
        $Search->getAppointmentAvailability($post);
    }
	
    public function insuranceAction() {
        
        $this->_helper->layout->disableLayout();
        $drids = trim($this->_getParam('drids'));
        $comp_id = $this->_getParam('comp_id');
        $DoctorInsurance = new Application_Model_DoctorInsurance();
        if($comp_id > 0){
            $Company = new Application_Model_InsuranceCompany();
            $insuranceCompany = $Company->find($comp_id);
        }
        $returnArray = array();
        $dridArray = explode(' ', $drids);
        if(count($dridArray)){
            foreach($dridArray as $drid){
                if($comp_id > 0){
                    $object = $DoctorInsurance->fetchRow("doctor_id={$drid} AND insurance_id={$comp_id}");
                    if(!empty($object)){
                        $returnArray[$drid] = "<div class=\"in-network\">In Network</div>
        <img width=\"125\" alt=\"{$insuranceCompany->getCompany()}\" src=\"/images/insurance/{$insuranceCompany->getLogo()}\">";
                    }else{
                        $returnArray[$drid] = "<strong>Out of network.</strong><br />Please contact the Doctor's office to see if they file paperwork.";
                    }
                }elseif($comp_id==-1){
                    $returnArray[$drid] = "<span class='na'>N/A</span>";
                }else{
                    $returnArray[$drid] = "Please enter your insurance at the top of the page.";
                }
            }
        }
        echo Zend_Json::encode($returnArray);
        exit();
    }
	
	
	public function orderedSearch($area, $catId, $company, $name, $reason, $gender=null) {
		$result = array();
		
		$db = Zend_Registry::get('db');
		
		if($area!=""){$area = $db->quote($area);}
		if($catId!=""){$catId = $db->quote($catId);}
		if($company!=""){$company = $db->quote($company);}
		if($name!=""){
			$name = str_replace(" ", "%", $name);
			$name = $db->quote('%'.$name.'%');}
		if($reason!=""){$reason = $db->quote($reason);}
		
		$query= "SELECT DISTINCT id FROM doctors WHERE status=1";
		$where ="";

		if($area!=""){
			$where.=" AND ( area = $area OR city = $area OR country= $area OR state= $area OR zipcode = $area)";
		}
		
		if($catId!="") {
			$where .=" AND ( id in (SELECT doctor_id FROM doctor_categories WHERE category_id = $catId) )";
		}
		
		if($company!="") {
			$where .=" AND ( id in (SELECT doctor_id FROM doctor_insurance WHERE insurance_id = $company) )";
		}
		
		if($name!="") {
			$where .=" AND fname LIKE $name ";
		}
		
		if($reason!="") {
			$where .=" AND ( id in (SELECT doctor_id FROM doctor_reason_for_visit WHERE reason_id = $reason) )";
		}

		if($gender == $this->view->lang[117]) {
			$where .=" AND gender = 'm' ";
		} 

		if($gender == $this->view->lang[118]) {
			$where .=" AND gender = 'f' ";
		} 
		
		$queryByMembership = $query.$where." ORDER BY FIELD(membership_level, 'Gold', 'Silver', 'Standard') ASC, fname ASC";
		$select = $db->query($queryByMembership);
        $result = $select->fetchAll();
		
		return $result; 
	}

}// end class

  function removeEmptyArrayNode($var)
    {
	return trim($var);
    }