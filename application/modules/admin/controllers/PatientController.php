<?php
class Admin_PatientController extends Base_Controller_Action {

    public function indexAction() {
        $this->view->title = "Admin Panel- List Patien";
        $this->view->headTitle("Admin Panel");

        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_Patient();

        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $strwhere_condition="del_status=0 OR  `del_status` IS NULL";
        
        $pageObj = new Base_Paginator();
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, $strwhere_condition, "id DESC");
        $this->view->total = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;
             
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }
    public function publishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $idArray = explode(',', $ids);
        $model = new Application_Model_Patient();
        $userModel = new Application_Model_User();
        foreach ($idArray as $id) {
            
          
            $object = $model->find($id);
            $objUser = $userModel->find($object->getUserId());
            $objUser->setStatus('active');
             $objUser->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('index', 'patient', "admin", Array('doctor_name'=>$doctor_name,'catid'=>$category_id,'page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");

        $idArray = explode(',', $ids);
        $model = new Application_Model_Patient();
        $userModel = new Application_Model_User();

        foreach ($idArray as $id) {
            
            $object = $model->find($id);
            $objUser = $userModel->find($object->getUserId());
            
            $objUser->setStatus('inactive');
           
            $objUser->save();

        }

        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('index', 'patient', "admin", Array('doctor_name'=>$doctor_name,'catid'=>$category_id,'page' => $page, 'msg' => $publish));
    }

    public function deleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $doctor_name = $this->_getParam("doctor_name");
        $category_id = $this->_getParam("catid");
        $idArray = explode(',', $ids);
        $objModelDoctor = new Application_Model_Patient();
        $userModel = new Application_Model_User();
        foreach ($idArray as $id) {
            $object = $objModelDoctor->find($id);
             $objUser = $userModel->find($object->getUserId());
             $objUser->setStatus('deleted');
              $object->setDelStatus(1);
            $object->save();
            $objUser->save();
        }
        // delete after article delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('index', 'patient', "admin", Array('doctor_name'=>$doctor_name,'catid'=>$category_id,'msg' => $msg, 'page' => $page));
    }

    public function addEditAction() {
        $id = $this->_getParam("id");
        $form=new Admin_Form_Patient();
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element){
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
        }
		
		if (0 < (int) $id) {
			$Patient = new Application_Model_Patient();
			$User = new Application_Model_User();
			
			$patObject = $Patient->fetchRow("id='{$id}'");
			$userObject = $User->fetchRow("id='{$patObject->getuserId()}'");

			$options['id'] = $patObject->getId();
			$options['name'] = $patObject->getName();
			$options['zipcode'] = $patObject->getZipcode();
			$options['age'] = $patObject->getAge();
			$options['phone'] = $patObject->getPhone();
			$options['month_dob'] = $patObject->getMonthDob();
			$options['date_dob'] = $patObject->getDateDob();
			$options['year_dob'] = $patObject->getYearDob();
			$options['gender'] = $patObject->getGender();
			$options['insurance'] = $patObject->getInsuranceCompanyId();
			$options['plan'] = $patObject->getInsurancePlanId();
			$options['user_id'] = $id;
			$options['email'] = $userObject->getEmail();
			$options['last_name'] = $userObject->getLastName();

			$form->populate($options);

		} else {
			$Patient = new Application_Model_Patient();
			$User = new Application_Model_User();
			$patObject = new Application_Model_Patient();
			$userObject = new Application_Model_User();
		}
        
		$request = $this->getRequest();
        $options = $request->getPost();
        
		if ($request->isPost()) {

			$email=trim($options['email']);
            $userObject = $User->fetchRow("id!='{$patObject->getuserId()}' AND email='{$email}'");
            if(is_object($userObject)) {
                 $form->setErrorMessages(array('This Email already exists'));
                 $emailerror=1;
            } else {
				$emailerror=0;
            }

            if (($form->isValid($options) && $emailerror<1)) {
				$msg = "Record has been save successfully!";
                $options['plan']=0;// For time being plan is not required
				
				if($id==0) {
					$userObjectsave = new Application_Model_User();
					$userObjectsave->setEmail($options['email']);
					$userObjectsave->setUserName($options['email']);
					$userObjectsave->setPassword($options['email']);
					$userObjectsave->setLastVisitDate(time());
					$userObjectsave->setStatus(1);
					$userObjectsave->setFirstName($options['name']." ".$options['last_name']);
					$userObjectsave->setLastName($options['last_name']);               
					$userObjectsave->setUserLevelId(3);               
					$userid = $userObjectsave->save();

					$dob['year'] = $options['year_dob'];
                    $dob['month'] = $options['month_dob'];
                    $dob['day'] = $options['date_dob'];
					$age = $userObjectsave->getAge($dob);	                
	                $patObject->setAge($age);
					
					$patObject->setName($options['name']);
					$last_updated=strtotime("now");
					$patObject->setZipcode($options['zipcode']);
					$patObject->setLastUpdated($last_updated);

					$patObject->setMonthDob($options['month_dob']);
					$patObject->setDateDob($options['date_dob']);
					$patObject->setYearDob($options['year_dob']);
					$patObject->setPhone($options['phone']);
					$patObject->setGender($options['gender']);
					$patObject->setUserId($userid);
					
					$patId = $patObject->save();
				} else {
					$patObject->setName($options['name']);
					$last_updated=strtotime("now");
					$patObject->setZipcode($options['zipcode']);
					$patObject->setLastUpdated($last_updated);

					$patObject->setMonthDob($options['month_dob']);
					$patObject->setDateDob($options['date_dob']);
					$patObject->setYearDob($options['year_dob']);

					$dob['year'] = $options['year_dob'];
                    $dob['month'] = $options['month_dob'];
                    $dob['day'] = $options['date_dob'];
                    $userObject = new Application_Model_User();
					$age = $userObject->getAge($dob);	                
	                $patObject->setAge($age);

					$patObject->setPhone($options['phone']);
					$patObject->setGender($options['gender']);
					$patId = $patObject->save();

					$userObjectsave = $User->fetchRow("id='{$patObject->getUserId()}'");
	                $userObjectsave->setEmail($options['email']);
	                $userObjectsave->setLastName($options['last_name']);               
	                $userObjectsave->save();
				}
				$form->populate($options);
                $this->view->msg=$msg;

                $msg = base64_encode($msg);
                $this->_helper->redirector('index', 'patient', "admin", Array('msg' => $msg, 'page' => $page));
			} else {
                $form->reset();
                $form->populate($options);
            }
        } else {
			if(0 >= (int) $id) {
				$Patient = new Application_Model_Patient();
				$patObject = new Application_Model_Patient();
				$userObject = new Application_Model_User();
				$options['id'] = $patObject->getId();
				$options['name'] = $patObject->getName();
				$options['zipcode'] = $patObject->getZipcode();
				$options['age'] = $patObject->getAge();
				$options['phone'] = $patObject->getPhone();
				$options['gender'] = $patObject->getGender();
				$options['insurance'] = $patObject->getInsuranceCompanyId();
				$options['plan'] = $patObject->getInsurancePlanId();
				$options['user_id'] = $id;
				$options['last_name'] = $userObject->getLastName();
			} else {
				$patObject = $Patient->fetchRow("id='{$id}'");
				$options['id'] = $patObject->getId();
				$options['name'] = $patObject->getName();
				$options['zipcode'] = $patObject->getZipcode();
				$options['age'] = $patObject->getAge();
				$options['phone'] = $patObject->getPhone();
				$options['gender'] = $patObject->getGender();
				$options['insurance'] = $patObject->getInsuranceCompanyId();
				$options['plan'] = $patObject->getInsurancePlanId();
				$options['user_id'] = $id;
				
				$userObject = $User->fetchRow("id='{$patObject->getUserId()}'");
				$options['last_name'] = $userObject->getLastName();
			}
        }
        $this->view->form = $form;
    }
}
?>