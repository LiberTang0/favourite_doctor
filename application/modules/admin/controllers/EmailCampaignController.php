<?php
class Admin_EmailCampaignController extends Base_Controller_Action {
	
	public function indexAction() {
		$form = new Admin_Form_EmailCampaign();
		$request = $this->getRequest();
		$member_level = $this->_getParam('member_level');
		
		$action = $this->_getParam('action');
			
		if ($member_level != "") {	
            $model = new Application_Model_Doctor();
            $object = $model->fetchAll("membership_level = '{$member_level}'");

			$this->view->object = $object;
         
        }
        //echo "<pre>";print_r($object);exit;
        $count = $this->_getParam('count');
		if(isset($action) && $action != "sendemail"){
			for($i = 1;$i < $count;$i++){
				$chk_email = "chk_email".$i;	
				$doctorid = "";
				$doctorid = $this->_getParam($chk_email);
								
				$Mail = new Zend_Mail('UTF-8');
				
				$Doctor = new Application_Model_Doctor();
				$objDoctor = $Doctor->find($doctorid);
				
				//echo "<pre>";print_r($objDoctor);
				//echo $objDoctor->getUserId();
				$User = new Application_Model_User();
				if(isset($objDoctor) && !empty($objDoctor)){
					$objuser = $User->find($objDoctor->getUserId());//exit;
					
					$options = array();
					$options['doctor_email'] = $objuser->getEmail();
	                $options['doctor_name'] = $objDoctor->getFname();
	                $options['assign_phone'] = $objDoctor->getAssignPhone();
	                $options['actual_phone'] = $objDoctor->getActualPhone();   
	                $subject = "";
	                $htmlBody = "
	                Dear <span id='dname'>__DOCTOR__</span> , 
<p>SITE NAME has generated a new patient telephone inquiry for your practice.  A patient called your SITE NAME toll free phone number __PHONE__ located on your personal webpage. This call was answered and screened by our team and then transferred to your office phone number __ACTUALPHONE__. </p>
<p>Please note that the patient has already been in contact with a member from your office. 
<br><br>
Thank You,
<br><br>
Your  Team
<p style='font-size: 12px;'>Please do not reply to this email. It was sent from an unattended mailbox, and replies are not reviewed.</p>
	                ";        
		$htmlBody=stripslashes($htmlBody);
		$htmlBody=str_replace("__DOCTOR__", $options['doctor_name'], $htmlBody);
		$htmlBody=str_replace("__PHONE__", $options['assign_phone'], $htmlBody);
		$htmlBody=str_replace("__ACTUALPHONE__", $options['actual_phone'], $htmlBody);
		$Mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
		$Mail->setFrom('noreply@example.com', 'SITE NAME');
		/*---------------------*/
		$Mail->setBodyHtml ($htmlBody);
		//$this->setFrom ( $from_email, $from_name);
		$Mail->addTo($objuser->getEmail());
		//$this->setSubject ($template['subject']);
		$Mail->setSubject ("New Telephone Inquiry");
		$Mail->send();

		#insert data into email campaign table 
		$EmailCamp = new Admin_Model_EmailCampaign();
		$EmailCamp->setEmail($objuser->getEmail());
		$EmailCamp->setDocId($doctorid);
		$EmailCamp->setContent($htmlBody);
		$EmailCamp->setStatus($htmlBody);
		
		$EmailCamp->save();
		$msg = base64_encode("Email have been sent successfully!");
        $this->_helper->redirector('index', 'doctor', "admin", Array('msg' => $msg));
				}
			}

		}  
		     
		$form->getElement('member_level')->setValue($member_level);
        $elements = $form->getElements();
        foreach ($elements as $element) {
            $element->removeDecorator('data');
            $element->removeDecorator('label');
            $element->removeDecorator('table');
            $element->removeDecorator('row');
        }
      

       /* $options = $request->getPost();

                $form->reset();
                $form->populate($options);

  */

        $this->view->form = $form;
	}
}
?>