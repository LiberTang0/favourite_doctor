<?php

class Base_Controller_Action extends Zend_Controller_Action {

    public function init() {

    }

    public function preDispatch() {

        parent::preDispatch();


        /* ---- Current Request Object ------ */
        $request = $this->getRequest();
        /* ----------------------------------- */

        /* --- Requested Module ------ */
        $this->view->module = $module = $request->getModuleName();
        /* ----------------------------------- */

        /* ---- Requested Action ------- */
        $this->view->actionName = $actionName = $request->getActionName();
        /* ----------------------------------- */

        /* ---- Requested Controller ------- */
        $this->view->controllerName = $controllerName = $request->getControllerName();
        /* ----------------------------------- */



        /* ------Authorization- (ACL) -------- */
        $roleName = 'guest';
        $usersNs = new Zend_Session_Namespace("members");
        if ($usersNs->userType <> '') {
            $roleName = $usersNs->userType;
        }

        $acl = new Base_Acl();
        if (!$acl->isAllowed($roleName, $module . ":" . $controllerName, $actionName)) {
            if($usersNs->userType=='subadmin'){
                $this->_helper->redirector('index', 'index', 'admin');
            }else if ($module == "admin" && $actionName == "index" && $controllerName == "index") {
                $this->_helper->redirector('login', 'login', 'admin');
            } else if ($module == "admin") {
                $this->_helper->redirector('login', 'login', 'admin');
                //$this->_helper->redirector('warning', 'login', 'admin');
            } else {
                $this->_helper->redirector('index', 'index', 'default');
            }
        }
        
        /* --------------------------------- */


        /* ----------Jquery------------ */

        
        $this->view->headScript()->appendFile('/js/main.js');
       

        if(in_array($controllerName,array('search--','profile','appointment'))){           
          //  $this->view->headScript()->appendFile('/js/fancybox/jquery.fancybox-1.3.3.js');
            //$this->view->headLink()->appendStylesheet('/js/fancybox/jquery.fancybox-1.3.3.css');
        }
		
		if(in_array($controllerName,array('search'))){

            $this->view->headScript()->appendFile('/js/colorbox/colorbox/jquery.colorbox.js');
            $this->view->headLink()->appendStylesheet('/js/colorbox/example5/colorbox.css');
        }
       

        /* ----bread crumb------- */
        
        $uri = $this->_request->getPathInfo();
        $activeNav = $this->view->navigation()->findByUri($uri);
		if(!empty($activeNav))$activeNav->active = true;
		
        /* ------------------------- */
        $MetaTagModel = new Base_MetaTags();
        $array = $MetaTagModel->setMetaTags();
        $FrontController = Base_Controller_Front::getInstance();
        $request = $FrontController->getRequest()->getParams();
        if($request['id']==110768)
        {
         $doctor_custom = "doctoridnew".$request['id'];
         $str_array = implode("~",$array);
       
        }
        
       
       if(isset($array) && !empty($array)){
        	if(isset($array['title']) && $array['title'] != ""){
        		$this->view->headTitle($array['title']);
        	}else{
        		$this->view->headTitle("");
        	}
        	if(isset($array['keywords']) && $array['keywords'] != ""){
        		$this->view->headMeta()->appendName('keywords', $array['keywords']);
        	}
        	if(isset($array['description']) && $array['description'] != ""){
        		$this->view->headMeta()->appendName('description', $array['description']);
        	}
        }
        
        if ($module == 'admin') {
            $this->view->headTitle("Administration");
            $this->_helper->layout->setLayout('admin-layout');
        }
		
		if ($controllerName == "appointment") {
			$this->view->headTitle($this->view->lang[587]);
		}
       
    }

}

