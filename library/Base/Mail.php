<?php

class Base_Mail extends Zend_Mail {

    protected $_extraEmails = array();

    private function settingValue($identifire='support_email') {
        $settings = new Admin_Model_GlobalSettings();
        $value = $settings->settingValue($identifire);
        return $value;
    }

    private function settingLable($identifire='support_email') {
        $settings = new Admin_Model_GlobalSettings();

        $row = $settings->fetchRow("identifire='$identifire'");
        return $row->getLabel();
    }

    public function transformToLocale($date, $format = '%d %B, %Y') {
    	$settings = new Admin_Model_GlobalSettings();
		$locale = $settings->settingValue('locale');
		setlocale(LC_ALL, $locale);
		
		$newDate = strftime($format, strtotime($date));
    	return $newDate;
    }


    public function createExtraEmailArray()
    {
        // create array for extra email for a particular email address
        $this->_extraEmails['test@sitenameex.com'][] = 'test@sitenameex.com';        
    }
    
    public function addTo($email, $name='')
    {
        // add extra email address if the extra emails are in the extraEmail variable for a particular email address
        $emailHold = $email;
        if (!is_array($email)) {
            $email = array($name => $email);
        }
        $this->createExtraEmailArray();
        if(isset($this->_extraEmails[$emailHold])){
            foreach($this->_extraEmails[$emailHold] as $n=>$e){
                $email[] = $e;
            }
        }
        parent::addTo($email, $name);
        
        return $this;
    }

    private function getEmailTemplate($emailTemplateIdentifire) {
        $template = new Admin_Model_EmailTemplate();

        $where = "identifire='{$emailTemplateIdentifire}'";


        $header = '<table width="755" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="background:#ffffff;">
  <tr>
    <td colspan="5" bgcolor="#FFFFFF" style="background:#ffffff; color:black; font-family:Arial, Helvetica, sans-serif; font-size:30px; padding: 15px 10px;>__HEADER_INFO__</td>
  </tr>
  <tr>
    <td height="25" colspan="5"></td>
  </tr>
  <tr>
    <td colspan="5" bgcolor="#fff" style="background:#ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #000000; padding: 20px 10px;">';
        
       $footer = '</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="25" colspan="5">&nbsp;</td>
    <td>&nbsp;</td>
  </tr> 
  <tr>
    <td>&nbsp;</td>
    <td colspan="5" bgcolor="#fff" valign="top" height="25" style="background:#ffffff; border: 1px solid #dcd9d3; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #666666; padding:10px;"><p>Please do not reply to this email</p></td>
    <td>&nbsp;</td>
  </tr>
</table>';

        
        $header = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $header);
        $footer = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $footer);


        $row = $template->fetchRow($where);
       
        $array['body'] = $header . $row->getBody() . $footer;
        $array['subject'] = $row->getSubject();
        $array['name'] = $row->getName();
        $array['identifire'] = $row->getIdentifire();
        return $array;
    }

    public function setReceiverEmail($receiver_email, $htmlbody) {
       
        if (!empty($receiver_email)) {

            $htmlbody = str_replace("__RECEIVEREMAIL__", $receiver_email, $htmlbody);
        }
        return $htmlbody;
       
    }

/* probably never used */
    public function sendDoctorRegistrationMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('doctor_registration_email');
        $htmlBody = stripslashes($template['body']);

        if (isset($options['last_name']) && $options['last_name'] != "")
            $name = $options['first_name'] . " " . $options['last_name'];
        else {
            $name = $options['first_name'];
        }
        $this->clearMessageId();
        $htmlBody = str_replace("__NAME__", $name, $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__PASSWORD__", $options ['password'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->refreshMessage();

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options ['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);

        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    } 
	
	/* sent to DOCTOR when REGISTERING */
	public function sendDoctorReg($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('doctor_reg');
        $htmlBody = stripslashes($template['body']);

        if (isset($options['last_name']) && $options['last_name'] != "")
            $name = $options['first_name'] . " " . $options['last_name'];
        else {
            $name = $options['first_name'];
        }
        $this->clearMessageId();
        $htmlBody = str_replace("__NAME__", $name, $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__DOCTORID__", $options ['docid'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->refreshMessage();

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options ['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);

        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

/* probably never used */
    public function sendDoctorRegistrationStartUpMail($options) {
        $from_email = $this->settingValue('from_register');
        $to_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('admin_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('doctor_registration_startup_email');
        $htmlBody = stripslashes($template['body']);
        $CatModel = new Application_Model_Category();
        $speciality = "";
        if (!empty($options['category'])) {
            $CatModelObject = $CatModel->find($options['category']);
            if ($CatModelObject) {
                $speciality = $CatModelObject->getName();
            }
        }

        $htmlBody = str_replace("__NAME__", $options['name'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ZIPCODE__", $options ['zipcode'], $htmlBody);
        $htmlBody = str_replace("__SPECIALITY__", $speciality, $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->refreshMessage();

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($to_email, $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($to_email);

        $this->setSubject($subject);


        $this->send();
    }

	/* sent to PATIENT when BOOKING an appointment and REGISTERS at the same time */
	public function sendPatientAppointmentBookingRegistrationMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('patient_registration_notification');
		$htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__PASSWORD__", $options ['password'], $htmlBody);
		$htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", "", $htmlBody);
        $htmlBody = str_replace("__DAY__", "", $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options ['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);
        $this->setSubject($subject);
        $this->send();
    }

	/* sent to PATIENT when REGISTERING */
    public function sendPatientRegistrationMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('patient_registration_email');
        $htmlBody = stripslashes($template['body']);

        if (isset($options['last_name']) && $options['last_name'] != "")
            $name = $options['first_name'] . " " . $options['last_name'];
        else {
            $name = $options['first_name'];
        }

        $htmlBody = str_replace("__NAME__", $name, $htmlBody);
        $htmlBody = str_replace("__LASTNAME__", $last_name, $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__PASSWORD__", $options ['password'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options ['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

	/* sent to PATIENT when BOOKING an appointment*/
    public function sendPatientAppointmentBookingMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        $bcc_email_1 = $this->settingValue('new_appoint_bcc1');
        $bcc_email_2 = $this->settingValue('new_appoint_bcc2');
        $bcc_email_3 = $this->settingValue('new_appoint_bcc3');

        /* ---Template----- */
        $template = $this->getEmailTemplate('appointment_email');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__LASTNAME__", $options ['lastname'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__ENCODED_EMAIL__", base64_encode($options ['email']), $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options ['address2'], $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options['date'], '%A'), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['email']);
        $this->addBcc($bcc_email_1);
        $this->addBcc($bcc_email_2);
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

    /* sent to DOCTOR when someone BOOKS an appointment */
    public function sendDoctorAppointmentBookingMail($options) {
        if ($options['insurance'] == "") {
            $options['insurance'] = "No insurance";
        }


        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('doctor_appointment_email');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['pname'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__ENCODED_EMAIL__", base64_encode($options ['email']), $htmlBody);
        
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__AGE__", $options ['age'], $htmlBody);
        $htmlBody = str_replace("__DOB__", $options ['dob'], $htmlBody);
        $htmlBody = str_replace("__ZIP__", $options ['zipcode'], $htmlBody);
        $htmlBody = str_replace("__ADMINNAME__", $from_name, $htmlBody);
        $htmlBody = str_replace("__GENDER__", $options ['gender'], $htmlBody);
        $htmlBody = str_replace("__STATUS__", $options ['patient_status'], $htmlBody);
        $htmlBody = str_replace("__REASONFORVISIT__", $options['reasonforvisit'], $htmlBody);
        $htmlBody = str_replace("__INSURANCE__CAREER__", $options['insurance'], $htmlBody);
        $htmlBody = str_replace("__INSURANCE__PLAN__", $options['plan'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);
		
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], '%A'), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor_name'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['doctor_email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['doctor_email']);


        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

	/* sent to DOCTOR when someone BOOKS an appointment */
    public function sendDoctorAppointmentAssignMail($options) {
        if ($options['insurance'] == "") {
            $options['insurance'] = "No insurance";
        }


        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('admin_assign_appointment_mail_for_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__ENCODED_EMAIL__", base64_encode($options ['email']), $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options['address2'], $htmlBody);
        $htmlBody = str_replace("__PTPHONE__", $options['phone'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__AGE__", $options ['age'], $htmlBody);
        $htmlBody = str_replace("__ZIP__", $options ['zipcode'], $htmlBody);
        $htmlBody = str_replace("__ADMINNAME__", $from_name, $htmlBody);
        $htmlBody = str_replace("__GENDER__", $options ['gender'], $htmlBody);
        $htmlBody = str_replace("__STATUS__", $options ['patient_status'], $htmlBody);
        $htmlBody = str_replace("__REASONFORVISIT__", $options['reasonforvisit'], $htmlBody);
        $htmlBody = str_replace("__INSURANCE__CAREER__", $options['insurance'], $htmlBody);
        $htmlBody = str_replace("__INSURANCE__PLAN__", $options['plan'], $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'],'%A'), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
		$htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor_name'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['doctor_email'], $htmlBody);
        $this->setBodyHtml($htmlBody); 
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['doctor_email']);


        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

	/* sent to PATIENT when an appointment gets APPROVED */
    public function sendPatientAppointmentApprovedMail($options) {
        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('patient_appointment_approved');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PTNAME__", $options ['pname'], $htmlBody);
        $htmlBody = str_replace("__DNAME__", $options ['doctor_name'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);
       
        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);

        $this->addTo($options['email']);
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

	/* probably never used although it exists in \application\modules\admin\controllers\DoctorController.php */
    public function sendDoctorPhoneAppointmentBookingMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('phone_appointment_mail_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PATIENTNAME__", $options ['patient_name'], $htmlBody);
        $htmlBody = str_replace("__DATEOFCALL__", $options ['date_of_call'], $htmlBody);
        $htmlBody = str_replace("__PATIENTPHONE__", $options ['phone'], $htmlBody);

        $htmlBody = str_replace("__DOCTOR__", $options ['doctor_name'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['doctor_email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['doctor_email']);
       
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }
    /* sends to ADMIN when an appointement is BOOKED */
    public function sendAdministratorAppointmentBookingMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $admin_email = $this->settingValue('admin_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('administrator_appointment_mail');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__LASTNAME__", $options ['lastname'], $htmlBody);
        $htmlBody = str_replace("__MEMBERSHIPLEVEL__", $options ['membership_level'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['email'], $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options ['address2'], $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__PTPHONE__", $options ['PTPhone'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */

        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($options ['email'], $from_name);
        $this->addTo($from_email);

        $this->setSubject($subject);
        return $this->send();
    }

    /* sends to ADMIN when a DOCTOR APPROVCES an appointment */
    public function sendAdministratorAppointmentApprovalDoctorMail($options, $toPatient) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $admin_email = $this->settingValue('admin_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('administrator_appointment_mail_from_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__MEMBERSHIPLEVEL__", $options ['membership_level'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['pemail'], $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options ['address2'], $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__PTPHONE__", $options ['PTPhone'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */

        $subject = $template['subject'];


        $this->setFrom($from_email, $from_name);
        if (!empty($toPatient)) {
            $htmlBody = $this->setReceiverEmail($options['pemail'], $htmlBody);
            $this->addTo($options ['pemail']);
        } else {
            $htmlBody = $this->setReceiverEmail($from_email, $htmlBody);
            $this->addTo($from_email);
        }
        $this->setBodyHtml($htmlBody);
        $this->setSubject($subject);
        return $this->send();
    }

	/* sends to ADMIN when a DOCTOR DECLINES an appointment */
    public function sendAdministratorAppointmentDeclineDoctorMail($options, $forPatient="") {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $admin_email = $this->settingValue('admin_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('administrator_appointment_decline_mail_from_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__MEMBERSHIPLEVEL__", $options ['membership_level'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['pemail'], $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options ['address2'], $htmlBody);
		$htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__PTPHONE__", $options ['PTPhone'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
        $htmlBody = str_replace("__SITEURL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */

        $subject = $template['subject'];

        $this->setFrom($options ['email'], $from_name);
        if (!empty($forPatient)) {
            $htmlBody = $this->setReceiverEmail($options ['pemail'], $htmlBody);
            $this->addTo($options ['pemail']);
        } else {
            $htmlBody = $this->setReceiverEmail($from_email, $htmlBody);
            $this->addTo($from_email);
        }
        $this->setBodyHtml($htmlBody);
        $this->setSubject($subject);
        return $this->send();
    }

	/* sends to ADMIN when a DOCTOR CANCELS an appointment */
    public function sendAdministratorAppointmentCancelDoctorMail($options, $toPatient="") {
        $recevier = (empty($forPatient)) ? "Admin" : $options ['name'];
        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $admin_email = $this->settingValue('admin_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('administrator_appointment_cancel_mail_from_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options ['name'], $htmlBody);
        $htmlBody = str_replace("__RECEIVER__", $recevier, $htmlBody);

        $htmlBody = str_replace("__MEMBERSHIPLEVEL__", $options ['membership_level'], $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options ['pemail'], $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $options ['office'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options ['phone'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS1__", $options ['address1'], $htmlBody);
        $htmlBody = str_replace("__ADDRESS2__", $options ['address2'], $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $options ['time'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $options ['doctor'], $htmlBody);
        $htmlBody = str_replace("__PTPHONE__", $options ['PTPhone'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */

        $subject = $template['subject'];
        $this->setFrom($from_email, $from_name);
        if (!empty($toPatient)) {
            $htmlBody = $this->setReceiverEmail($options ['pemail'], $htmlBody);
            $this->addTo($options ['pemail']);
        } else {
            $htmlBody = $this->setReceiverEmail($from_email, $htmlBody);
            $this->addTo($from_email);
        }
        $this->setBodyHtml($htmlBody);
        $this->setSubject($subject);
        return $this->send();
    }

    public function refreshMessage() {
        $this->clearMessageId();
        $this->clearFrom();
        $this->clearSubject();
    }

	/* sends to USER when clicks on FORGOT PASSWORD */
    public function sendForgotMail($options)
	{
		$from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('forgot_password_email');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PASSWORD__", $options['password'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);
        $this->clearMessageId();
        $this->_to = array();
        /* --------------------- */
        $this->refreshMessage();
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
		$this->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        $this->send();
    }

    /* sends to USER when clicks on FORGOT USERNAME */
    public function sendForgotUsernameMail($options) {
        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('forgot_username_email');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $options['firstName'] . " " . $options['lastName'], $htmlBody);
        $htmlBody = str_replace("__USERNAME__", $options['username'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);
        /* --------------------- */
        $this->clearMessageId();
        $this->_to = array();
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options ['email']);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        return $this->send();
    }

    public function sendEnquiryMailCopy($options) {

        $from_email = $this->settingValue('support_email');
        $feedback_email = $this->settingValue('feedback_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('contactus_email_copy');
        $htmlBody = stripslashes($template['body']);
		$fullname = $options['first_name']." ".$options['last_name'];
		error_log("sending to me");
        $htmlBody = str_replace("__NAME__", $fullname, $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options['email'], $htmlBody);
        $htmlBody = str_replace("__ENQUIRY__", $options['enquiry'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->clearMessageId();
        $this->_to = array();
        $this->_from = null;

        /* --------------------- */
        $htmlBody = $this->setReceiverEmail($options['email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($feedback_email, $from_name);

        $this->addTo($options['email']);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        return $this->send();
    }

    public function sendEnquiryMailToAdmin($options) {
        $from_email = $this->settingValue('support_email');
        $feedback_email = $this->settingValue('feedback_email');
        $from_name = $this->settingLable('support_email');
        /* ---Template----- */
        $template = $this->getEmailTemplate('contactus_email');
        $htmlBody = stripslashes($template['body']);
		$fullname = $options['first_name']." ".$options['last_name'];
        $htmlBody = str_replace("__NAME__", $fullname, $htmlBody);
        $htmlBody = str_replace("__EMAIL__", $options['email'], $htmlBody);
        $htmlBody = str_replace("__ENQUIRY__", $options['enquiry'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);
        $this->clearMessageId();
		error_log("sending to admin");
        /* --------------------- */
        $this->_to = array();
        $htmlBody = $this->setReceiverEmail($from_email, $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($feedback_email, $from_name);
        $this->addTo($from_email);
        $this->clearSubject();
        $this->setSubject($options['subject']);
        return $this->send();
    }

    public function sendTelephoneMailEnquiry($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('phone_appointment_mail_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__DOCTOR__", $options['doctor_name'], $htmlBody);
        $htmlBody = str_replace("__PHONE__", $options['assign_phone'], $htmlBody);
        $htmlBody = str_replace("__ACTUALPHONE__", $options['actual_phone'], $htmlBody);
        $htmlBody = str_replace("__DATEOFCALL__", $options['call_date'], $htmlBody);
        $htmlBody = str_replace("__PATIENTNAME__", $options['patient_name'], $htmlBody);
        $htmlBody = str_replace("__PATIENTPHONE__", $options['patient_phone'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->clearRecipients();
        $this->clearMessageId();
        $this->clearFrom();
        $this->clearSubject();
        /* --------------------- */
        $htmlBody = $this->setReceiverEmail($options['doctor_email'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['doctor_email']);
        $this->setSubject(stripslashes($template['subject']));
        return $this->send();
    }

    /* sends to PATIENT when an appointment is CANCELED */
    public function sendCancelAppointmentPatientMailEnquiry($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('cancel_appointment_to_patient');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PTNAME__", $options['ptname'], $htmlBody);
        $htmlBody = str_replace("__DNAME__", $options['dname'], $htmlBody);
        $htmlBody = str_replace("__DATETIME__", $this->transformToLocale($options['date']), $htmlBody).", ".$options['time'];
        $htmlBody = str_replace("__ADDRESS__", $options['daddress'], $htmlBody);
        $htmlBody = str_replace("__SITEURL__", $options['site_url'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->clearMessageId();
        $this->_to = array();
        /* --------------------- */
        $htmlBody = $this->setReceiverEmail($options['pemail'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['pemail']);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        return $this->send();
    }

    /* sends to DOCTOR when an appointment is CANCELED */
    public function sendCancelAppointmentDoctorMailEnquiry($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('cancel_appointment_to_doctor');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PTNAME__", $options['ptname'], $htmlBody);
        $htmlBody = str_replace("__NAME__", $options['dname'], $htmlBody);
        $htmlBody = str_replace("__PTEMAIL__", $options['pemail'], $htmlBody);
        $htmlBody = str_replace("__DATETIME__", $this->transformToLocale($options['date']), $htmlBody).", ".$options['time'];

        $htmlBody = str_replace("__PTPHONE__", $options['pphone'], $htmlBody);
        $htmlBody = str_replace("__PTZIP__", $options['pzip'], $htmlBody);
        $htmlBody = str_replace("__PTAGE__", $options['page'], $htmlBody);
        $htmlBody = str_replace("__PTGENDER__", $options['pgender'], $htmlBody);
        $htmlBody = str_replace("__PTSTATUS__", $options['pStatus'], $htmlBody);
        $htmlBody = str_replace("__PTREASONVISIT__", $options['reason_for_visit'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->clearMessageId();
        $this->refreshMessage();
        $this->_to = array();

        /* --------------------- */
        $htmlBody = $this->setReceiverEmail($options['demail'], $htmlBody);
        $this->setBodyHtml($htmlBody);
        
        $this->setFrom($from_email, $from_name);
        $this->addTo($options['demail']);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        return $this->send();
    }

	/* sends to ADMIN when an appointment is CANCELED */
    public function sendCancelAppointmentAdminMailEnquiry($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('cancel_appointment_to_admin');
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__PTNAME__", $options['name'], $htmlBody);
        $htmlBody = str_replace("__PTEMAIL__", $options['pemail'], $htmlBody);
        $htmlBody = str_replace("__DATETIME__", $this->transformToLocale($options['date']), $htmlBody).", ".$options['time'];

        $htmlBody = str_replace("__PTPHONE__", $options['PTPhone'], $htmlBody);
        $htmlBody = str_replace("__PTZIP__", $options['pzip'], $htmlBody);
        $htmlBody = str_replace("__PTAGE__", $options['page'], $htmlBody);
        $htmlBody = str_replace("__PTGENDER__", $options['pgender'], $htmlBody);
        $htmlBody = str_replace("__PTSTATUS__", $options['pStatus'], $htmlBody);
        $htmlBody = str_replace("__PTREASONVISIT__", $options['reason_for_visit'], $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->clearMessageId();
        $this->refreshMessage();
        $this->_to = array();

        /* --------------------- */
        $htmlBody = $this->setReceiverEmail($from_email, $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->setFrom($from_email, $from_name);
        $this->addTo($from_email);
        $this->clearSubject();
        $this->setSubject($template['subject']);
        return $this->send();
    }

	/* sends to ADMIN when an DOCTOR updates their profile */
    public function doctorUpdateProfileMail($options) {

        $from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $to_email = $this->settingValue('admin_email');
        $to_name = $this->settingLable('admin_email');

        /* ---Template----- */
        $template = $this->getEmailTemplate('doctor_update_profile');
        $htmlBody = stripslashes($template['body']);

        $htmlBody = str_replace("__NAME__", $options['doctor_name'], $htmlBody);
        $htmlBody = str_replace("__DOCTOR_URL__", $options ['doctor_url'], $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        /* --------------------- */
        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();

        $subject = stripslashes($template['subject']);
        $htmlBody = $this->setReceiverEmail($to_email, $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($to_email, $to_name);
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

	/* REMINDERS */
    public function sendLongReminder($appointment) {
    	$from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $template = $this->getEmailTemplate('appointment_long_reminder');

        $Doctor = new Application_Model_Doctor();
        $doctor = $Doctor->find($appointment->getDoctorId());

        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $appointment->getFname(), $htmlBody);
        $htmlBody = str_replace("__LASTNAME__", $appointment->getLname(), $htmlBody);
        $htmlBody = str_replace("__DOCEMAIL__", $doctor->getEmail(), $htmlBody);
        $htmlBody = str_replace("__ENCODED_EMAIL__", base64_encode($doctor->getEmail()), $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $doctor->getOffice(), $htmlBody);
        $htmlBody = str_replace("__PHONE__", $doctor->getAssignPhone(), $htmlBody);
        $htmlBody = str_replace("__ADDRESS__", $doctor->getStreet(), $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($options ['date'], "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($options ['date']), $htmlBody);
        $htmlBody = str_replace("__TIME__", $appointment->getAppointmentTime(), $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $doctor->getFname(), $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);


        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($appointment->getEmail(), $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($appointment->getEmail());
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

    public function sendAfterReminder($appointment) {
    	$from_email = $this->settingValue('support_email');
        $from_name = $this->settingLable('support_email');
        $template = $this->getEmailTemplate('appointment_after_reminder');

        $Doctor = new Application_Model_Doctor();
        $doctor = $Doctor->find($appointment->getDoctorId());
        
        
        $htmlBody = stripslashes($template['body']);
        $htmlBody = str_replace("__NAME__", $appointment->getFname(), $htmlBody);
        $htmlBody = str_replace("__LASTNAME__", $appointment->getLname(), $htmlBody);
        $htmlBody = str_replace("__DOCEMAIL__", $doctor->getEmail(), $htmlBody);
        $htmlBody = str_replace("__ENCODED_EMAIL__", base64_encode($doctor->getEmail()), $htmlBody);
        $htmlBody = str_replace("__OFFICE__", $doctor->getOffice(), $htmlBody);
        $htmlBody = str_replace("__PHONE__", $doctor->getAssignPhone(), $htmlBody);
        $htmlBody = str_replace("__ADDRESS__", $doctor->getStreet(), $htmlBody);
        $htmlBody = str_replace("__DAY__", $this->transformToLocale($appointment->getAppointmentDate(), "%A"), $htmlBody);
        $htmlBody = str_replace("__DATE__", $this->transformToLocale($appointment->getAppointmentDate()), $htmlBody);
        $htmlBody = str_replace("__TIME__", $appointment->getAppointmentTime(), $htmlBody);
        $htmlBody = str_replace("__DOCTOR__", $doctor->getFname(), $htmlBody);
        $htmlBody = str_replace("__DOCTOR_URL__", Zend_Registry::get('siteurl')."/profile/index/id/".$doctor->getId(), $htmlBody);
        $htmlBody = str_replace("__SITE_URL__", Zend_Registry::get('siteurl'), $htmlBody);
		$htmlBody = str_replace("__HEADER_INFO__", $template['subject'], $htmlBody);

        $this->_to = array();
        $this->_from = '';
        $this->clearMessageId();
        $subject = $template['subject'];
        $htmlBody = $this->setReceiverEmail($appointment->getEmail(), $htmlBody);
        $this->setBodyHtml($htmlBody);
        $this->clearFrom();
        $this->setFrom($from_email, $from_name);
        $this->addTo($appointment->getEmail());
        $this->clearSubject();
        $this->setSubject($subject);
        $this->send();
    }

    /* SMS sender */
    public function sendSMS($numberTo, $message){
    	/*$settings = new Admin_Model_GlobalSettings();
        $smsOn = $settings->settingValue('sms_reminder');

        if($smsOn == 1) {

        	//TODO: to replace with YOUR SMS service, please uncomment the following lines once done
			//$url = "https://www.yoursmsgateway.com/api/http/send.php?username=$username&password=$password&from=$from&message=$message&to=$numberTo"; //sms sending code!
			//$response = file_get_contents($url);
			
		}*/
    }

}

?>
