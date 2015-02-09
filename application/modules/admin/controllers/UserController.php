<?php

class Admin_UserController extends Base_Controller_Action {

    public function indexAction() {	

        $model = new Application_Model_User();

        $settings = new Admin_Model_GlobalSettings();
        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $pageObj = new Base_Paginator();
        $paginator = $pageObj->fetchPageData($model, $page, $page_size,"user_level_id=1");
        $this->view->total_users = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;

        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function editAction() {
        $id = $this->_getParam('id');
        $model1 = new Application_Model_User();
        $model = $model1->find($id);

        $options['firstName'] = $model->getFirstName();
        $options['lastName'] = $model->getLastName();
        $options['userLevelId'] = $model->getUserLevelId();

        $request = $this->getRequest();
        $form = new Admin_Form_User();
        $form->populate($options);

        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {
                $model->setOptions($options);
                $model->save();
                $this->view->msg = "'User Id : {$model->getId()}' has been updated successfully!";
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }

    public function blockAction() {
        $id = $this->_getParam('id');
        $model1 = new Application_Model_User();
        $model = $model1->find($id);
        if ($model->getStatus() == "active") {
            $model->setStatus("inactive");
            $publish = "blocked";
        } else {
            $model->setStatus('active');
            $publish = "unblocked";
        }
        $model->save();
        return $this->_helper->redirector('index', 'user', "admin", Array('msg' => base64_encode("User [Id : {$model->getId()}] has been $publish!")));
    }

    public function resetPasswordAction() {
        $id = $this->_getParam('id');
        $foruser = $this->_getParam('foruser');
        $User = new Application_Model_User();
        $res = $User->find($id);

        $Auth = new Base_Auth_Auth();
        $Auth->recoverPassword($res);
        if(isset($foruser) && $foruser==1)
        {
			$doctor_name = $this->_getParam("doctor_name");
			$category_id = $this->_getParam("catid");
			$doctor_name = $this->_getParam("doctor_name");
			$category_id = $this->_getParam("catid");
			$state = $this->_getParam("state");
			$scriteria = $this->_getParam("scriteria");

			$sorder = $this->_getParam("sorder");
			$zip = $this->_getParam("zip");
			$mlevel = $this->_getParam("mlevel");
			$msg = base64_encode("User [Id : ".$res->getId()."] Password has been changed!");
			//return $this->_helper->redirector('index', 'doctor', "admin", Array('msg' => base64_encode("User [Id : {$res->getId()}] Password has been changed!")));
			$this->_helper->redirector('index', 'doctor', "admin", Array('doctor_name' => $doctor_name, 'catid' => $category_id,'state' =>$state,'zip'=>$zip,'scriteria'=>$scriteria,'mlevel'=>$mlevel,'sorder'=>$sorder, 'msg' => $msg, 'page' => $page));

        }
        if(isset($foruser) && $foruser==2)
			return $this->_helper->redirector('index', 'patient', "admin", Array('msg' => base64_encode("User [Id : {$res->getId()}] Password has been changed!")));
        else if(isset($foruser) && $foruser==3)
			return $this->_helper->redirector('index', 'assistant', "admin", Array('msg' => base64_encode("User [Id : {$res->getId()}] Password has been changed!")));
		else
			return $this->_helper->redirector('index', 'user', "admin", Array('msg' => base64_encode("User [Id : {$res->getId()}] Password has been changed!")));
    }

    function changePasswordAction() {

        $usersNs = new Zend_Session_Namespace("members");
        $user = new Application_Model_User();
        $model = $user->find($usersNs->userId);


        $request = $this->getRequest();
        $form = new Application_Form_ChangePassword();

        if ($request->isPost()) {
            $options = $request->getPost();
            if ($form->isValid($options)) {
                $model->setPassword(md5($options ['password']));
                $model->save();
                $this->view->msg = "Your password changed successfully!";
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        // Assign the form to the view
        $this->view->form = $form;
    }

}
