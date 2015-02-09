<?php

class Admin_PatientCommentController extends Base_Controller_Action {

    public function indexAction() {
        $this->view->title = "Admin Panel- List Patient Comment";
        $this->view->headTitle("Admin Panel");

        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_DoctorReview();

        $stext = $this->_getParam('stext');
        $this->view->stext = $stext;
        $where = '1=1 ';
        if($stext!=''){
            $where .= " AND (username LIKE '%{$stext}%'";
            $db = Zend_Registry::get('db');
            $query = "SELECT d.id FROM doctor_review dr, doctors d WHERE dr.doctor_id=d.id AND d.fname LIKE '%{$stext}%' AND d.status=1 AND d.membership_level IN ('Platinum','Gold','Silver')";
            $select = $db->query($query);
            $result = $select->fetchAll();
            if(!empty($result)){
                foreach($result as $doc){
                    $array[] = $doc->id;
                }
                $where .= " OR doctor_id IN (".implode(',', $array).")";
            }
            $where .= ")";
//            prexit($where);

        }
        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $pageObj = new Base_Paginator();
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, $where, "id DESC");
        $this->view->total = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;

        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function deleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $objModelPatientComment = new Application_Model_DoctorReview();
        foreach ($idArray as $id) {
            $object = $objModelPatientComment->find($id);
            $object->delete("id={$id}");
        }
        // delete after comment delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('index', 'patient-comment', "admin", Array('msg' => $msg, 'page' => $page));
    }

    public function publishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $model = new Application_Model_DoctorReview();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus('1');
            $object->setAdminApproved('1');
            $object->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('index', 'patient-comment', "admin", Array('page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $model = new Application_Model_DoctorReview();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus(0);
            $object->setAdminApproved(0);
            $object->save();
        }
        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('index', 'patient-comment', "admin", Array('page' => $page, 'msg' => $publish));
    }

    public function addEditAction() {
        $id = $this->_getParam('id');
        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');

        $form = new Admin_Form_DoctorReview();
        $Doctor = new Application_Model_Doctor();
        

        
        
        if (0 < (int) $id) {
            $model = new Application_Model_DoctorReview();
            $object = $model->find($id);
            $docObject = $Doctor->find($object->getDoctorId());
            $options['id'] = $id;
            $options['review'] = $object->getReview();
            $options['title'] = $object->getTitle();
            $options['username'] = $object->getUsername();
	    $options['doctorId'] = $object->getDoctorId();
            $options['doctorName'] = $docObject->getFname();
            $form->populate($options);
            
        }

        $request = $this->getRequest();

        
        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {
               
                if (0 < (int) $id) {
		$msg = base64_encode("Record has been updated successfully!");
                    $options['id'] = $id;
                    $object->setId($id);
                    $object->setReview($options['review']);
                    $object->setTitle($options['title']);
                    $object->setUsername($options['username']);
		    $object->save();
                    $this->_helper->redirector('index', 'patient-comment', "admin", Array('msg' => $msg, 'page' => $page));
                } else {
					 $msg = base64_encode("Record has been saved successfully!");
                    $model = new Application_Model_PatientComment($options);
                    $model->save();
                }
                $this->_helper->redirector('index', 'patient-comment', "admin", Array('msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }
}
?>