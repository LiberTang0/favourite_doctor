<?php

class Admin_InsuranceCompanyController extends Base_Controller_Action {

    public function indexAction() {
        $this->view->title = "Admin Panel- List Insurance Company";
        $this->view->headTitle("Admin Panel");

        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_InsuranceCompany();

        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $pageObj = new Base_Paginator();
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, null, "company ASC");
        $this->view->total = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;

        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function deleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $objModelInsurance = new Application_Model_InsuranceCompany();
        foreach ($idArray as $id) {
            $object = $objModelInsurance->find($id);
            if ($object->getLogo() != '') {
                $filename = 'images/insurance/' . $object->getLogo();
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }

            $object->delete("id={$id}");
        }
        // delete after article delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('index', 'insurance-company', "admin", Array('msg' => $msg, 'page' => $page));
    }

    public function publishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $model = new Application_Model_InsuranceCompany();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus('1');
            $object->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('index', 'insurance-company', "admin", Array('page' => $page, 'msg' => $publish));
    }

    public function unpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');

        $idArray = explode(',', $ids);
        $model = new Application_Model_InsuranceCompany();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus(0);
            $object->save();
        }
        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('index', 'insurance-company', "admin", Array('page' => $page, 'msg' => $publish));
    }

    public function addEditAction() {
        $id = $this->_getParam('id');
        $page = $this->_getParam('page');
        $this->view->page = $this->_getParam('page');

        $form = new Admin_Form_InsuranceCompany();

        
        
        if (0 < (int) $id) {
            $model = new Application_Model_InsuranceCompany();
            $object = $model->find($id);
            $options['id'] = $id;
            $options['company'] = $object->getCompany();
            $options['description'] = $object->getDescription();            
            $options['metadescription'] = $object->getMetadescription();
            $options['metatitle'] = $object->getMetatitle();
            $options['metakeywords'] = $object->getMetakeywords();
            

            $form->populate($options);
        }

        $request = $this->getRequest();

        
        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {

                $upload = new Zend_File_Transfer_Adapter_Http();
                $path = "images/insurance/";
                $upload->setDestination($path);
                try {
                    $upload->receive();
                } catch (Zend_File_Transfer_Exception $e) {
                    $e->getMessage();
                }
//        echo "<pre>";print_r($upload->getFileName('logo'));exit;
                $upload->setOptions(array('useByteString' => false));
                $file_name = $upload->getFileName('logo');
                if(!empty($file_name)){
                    $imageArray = explode(".", $file_name);
                    $ext = strtolower($imageArray[count($imageArray) - 1]);
                    $target_file_name = "ins_".time().".{$ext}";
                    $targetPath = $path . $target_file_name;
                    $filterFileRename = new Zend_Filter_File_Rename(array('target' => $targetPath , 'overwrite' => true));
                    $filterFileRename -> filter($file_name);
                    /*------------------ THUMB ---------------------------*/
                    $image_name	=	$target_file_name;
                    $newImage	=	$path . $image_name;

                    $thumb = Base_Image_PhpThumbFactory ::create($targetPath);
                    $thumb->resize(150, 60);
                    $thumb->save($newImage);
                    if (0 < (int) $id) {
                        $del_image = $path . $object->getLogo();
                        if(file_exists($del_image))unlink($del_image);
                        $object->setLogo($image_name);
                    }else{
                        $options['logo'] = $image_name;
                    }
                    
                    /*------------------ END THUMB ------------------------*/
                }

                
                


                $msg = base64_encode("Record has been save successfully!");
                if (0 < (int) $id) {
                    $options['id'] = $id;
                    $object->setId($id);
                    $object->setCompany($options['company']);
                    $object->setDescription($options['description']);
                    $object->setMetadescription($options['metadescription']);
                    $object->setMetatitle($options['metatitle']);
                    $object->setMetakeywords($options['metakeywords']);
                    $object->save();
                    $this->_helper->redirector('index', 'insurance-company', "admin", Array('msg' => $msg, 'page' => $page));
                } else {
                    $model = new Application_Model_InsuranceCompany($options);
                    $model->save();
                }
                $this->_helper->redirector('index', 'insurance-company', "admin", Array('msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }



    /*------------------------------------------------INSURANCE PLAN---------------------------------------------------------------*/
    public function planAction() {
        $this->view->title = "DIH Admin Panel- List Insurance Plan";
        $this->view->headTitle("DIH Admin Panel");
        $cid = $this->_getParam('cid', 1);
        $links = array('cid'=>$cid);

        $settings = new Admin_Model_GlobalSettings();
        $model = new Application_Model_InsurancePlan();
        $Company = new Application_Model_InsuranceCompany();
        $companies = $Company->fetchAll('status=1',"company asc");

        $page_size = $settings->settingValue('pagination_size');
        $page = $this->_getParam('page', 1);
        $pageObj = new Base_Paginator();
        $paginator = $pageObj->fetchPageData($model, $page, $page_size, "insurance_company_id='{$cid}'", "plan ASC");
        $this->view->total = $pageObj->getTotalCount();
        $this->view->paginator = $paginator;

        $this->view->companies = $companies;
        $this->view->linkArray = $links;//array('content'=>$content);
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    public function planPublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $model = new Application_Model_InsurancePlan();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus('1');
            $object->save();
        }

        $publish = base64_encode("Record(s) published successfully");
        $this->_helper->redirector('plan', 'insurance-company', "admin", Array('cid'=>$cid, 'page' => $page, 'msg' => $publish));
    }

    public function planUnpublishAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $model = new Application_Model_InsurancePlan();
        foreach ($idArray as $id) {
            $object = $model->find($id);
            $object->setStatus(0);
            $object->save();
        }
        $publish = base64_encode("Record(s) unpublished successfully");
        $this->_helper->redirector('plan', 'insurance-company', "admin", Array('cid'=>$cid, 'page' => $page, 'msg' => $publish));
    }

    public function planDeleteAction() {
        $ids = $this->_getParam('ids');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');

        $idArray = explode(',', $ids);
        $objModelInsurance = new Application_Model_InsurancePlan();
        foreach ($idArray as $id) {
            $object = $objModelInsurance->find($id);
            $object->delete("id={$id}");
        }
        // delete after article delete
        $msg = base64_encode("Record(s) has been deleted successfully!");
        $this->_helper->redirector('plan', 'insurance-company', "admin", Array('cid'=>$cid, 'page' => $page, 'msg' => $publish));
    }

    public function addEditPlanAction() {
        $id = $this->_getParam('id');
        $page = $this->_getParam('page');
        $cid = $this->_getParam('cid');
        
        $this->view->page = $this->_getParam('page');
        $this->view->cid = $this->_getParam('cid');

        $form = new Admin_Form_InsurancePlan();



        if (0 < (int) $id) {
            $model = new Application_Model_InsurancePlan();
            $object = $model->find($id);
            $options['id'] = $id;
            $options['plan'] = $object->getPlan();
            $options['planType'] = $object->getPlanType();

            $form->populate($options);
        }

        $request = $this->getRequest();


        $options = $request->getPost();
        if ($request->isPost()) {
            if ($form->isValid($options)) {

                $msg = base64_encode("Record has been save successfully!");
                if (0 < (int) $id) {
                    $options['id'] = $id;
                    $object->setId($id);
                    $object->setPlan($options['plan']);
                    $object->setPlanType($options['planType']);
                    $object->save();
                    $this->_helper->redirector('plan', 'insurance-company', "admin", Array('cid'=>$cid, 'page' => $page, 'msg' => $msg));
                } else {
                    $options['insuranceCompanyId'] = $cid;//print_r($options);exit;
                    $model = new Application_Model_InsurancePlan($options);
                    $model->save();
                }
                $this->_helper->redirector('plan', 'insurance-company', "admin", Array('cid'=>$cid, 'msg' => $msg));
            } else {
                $form->reset();
                $form->populate($options);
            }
        }

        $this->view->form = $form;
    }
}
?>