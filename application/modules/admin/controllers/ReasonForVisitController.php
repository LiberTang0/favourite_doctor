<?php

class Admin_ReasonForVisitController extends Base_Controller_Action {

    public function indexAction() {
        $this->view->title = "Admin Panel- List Reason for Visit";
        $this->view->headTitle("Admin Panel");
        //$cid = $this->_getParam('cid', 11);
        $cid = $this->_getParam('cid');
        $links = array('cid'=>$cid);
        
        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_ReasonForVisit();
        $Category = new Application_Model_Category();
        $categories = $Category->fetchAll('status=1', 'name ASC');

        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $pageObj = new Base_Paginator();
        if(!empty($cid))
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, "category_id='{$cid}'", "reason ASC");
        else
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, NULL, "reason ASC");
        $this->view->total = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;

        $this->view->categories = $categories;
        $this->view->linkArray = $links;//array('content'=>$content);
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function deleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $model = new Application_Model_ReasonForVisit();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->delete("id={$id}");
        }
        // delete after article delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('index', 'reason-for-visit', "admin", Array('msg' => $msg,'cid' => $cid, 'page' => $page));
    }

    public function publishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $model = new Application_Model_ReasonForVisit();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus('1');
            $object->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('index', 'reason-for-visit', "admin", Array('cid' => $cid, 'page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $model = new Application_Model_ReasonForVisit();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus(0);
            $object->save();
        }
        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('index', 'reason-for-visit', "admin", Array('cid' => $cid,'page' => $page, 'msg' => $publish));
    }

    public function addEditAction() {
        $id = $this->_getParam('id');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');
        $this->view->page = $this->_getParam('page');
        $this->view->cid = $this->_getParam('cid');

        $options = array();
        $form = new Admin_Form_ReasonForVisit();

        
        $options['categoryId'] = $cid; // for default category selected in add

        if (0 < (int) $id) {
            $model = new Application_Model_ReasonForVisit();
            $object = $model->find($id);
            $options['id'] = $id;
            $options['reason'] = $object->getReason();
         
            $options['categoryId'] = $object->getCategoryId();

        }

        $form->populate($options);
        
        $request = $this->getRequest();

        
        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {

         
                $msg = base64_encode("Record has been save successfully!");
                if (0 < (int) $id) {
                          
                   
                   
                    
                    $options['id'] = $id;
                    $object->setId($id);
                    $object->setReason($options['reason']);
                    
                  
                    $object->setCategoryId($options['categoryId']);
                    $object->save();
                   
                    $this->_helper->redirector('index', 'reason-for-visit', "admin", Array('cid' => $cid,'msg' => $msg, 'page' => $page));
                } else {
                    
                    $model = new Application_Model_ReasonForVisit($options);
                     $arreason= explode(",",$options['reason']);

                    foreach($arreason as $reason)
                    {

                    $model->setReason($reason);
                    $model->save();
                    }
                }
                $this->_helper->redirector('index', 'reason-for-visit', "admin", Array('cid' => $cid,'msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }


}
?>