<?php

/**
 * IndexController
 * 
 * @author
 * @version 
 */
class Admin_LoginController extends Base_Controller_Action {

    /**
     * The default action - show the home page
     */
    public function preDispatch() {
        parent::preDispatch();
        $this->_helper->layout->setLayout('admin-login');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            if ('logout' != $this->getRequest()->getActionName()) {
                $usersNs = new Zend_Session_Namespace("members");
                if ($usersNs->userType == 'administrator') {
                    $this->_helper->redirector('index', 'index', 'admin');
                } else {
                    $this->_helper->redirector('logout', 'login', 'admin');
                }
            }
        } else {
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'login', 'admin');
            }
        }
    }

    public function indexAction() {
        $this->_helper->redirector('login', 'login', 'admin');
    }

    public function logoutAction() {
        $Auth = new Base_Auth_Auth();
        $Auth->doLogout();
        $this->_helper->redirector('index', 'login', 'admin'); // back to login page
    }

    public function loginAction() {
        $request = $this->getRequest();
        $form = new Admin_Form_Login();
        $this->view->form = $form;
        $cookie = new Base_Http_Cookie();
        //echo "<pre>";print_r(unserialize(base64_decode($cookie->getCookie('rememberMe'))));exit;
        $rememberArray = unserialize(base64_decode($cookie->getCookie('rememberMe')));
        if(isset($rememberArray['email'])){
            $form->getElement('email')->setValue($rememberArray['email']);
            if(isset($rememberArray['password'])){
                $form->getElement('password')->setValue($rememberArray['password']);
            }
            $form->getElement('rememberMe')->setAttrib('checked', true);
        }
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $Auth = new Base_Auth_Auth();
                $params = $request->getParams();
                $Auth->doLogout();

                $loginStatusEmail = true;
                $loginStatusUsername = true;

                $loginStatusEmail = $Auth->doLogin($params, 'email');
                if ($loginStatusEmail == false) {
                    $loginStatusUsername = $Auth->doLogin($params, 'username');
                }

                if ($loginStatusEmail == false && $loginStatusUsername == false) {
                    // Invalid credentials
                    $form->setDescription('Invalid credentials provided');
                } else {
                    if ($params['rememberMe'] == 1) {
                        $Auth->remeberMe(true, $params);
                    }else{
                        $cookie->setCookie('rememberMe', '', (time()-10));
                    }
                    // Valid credentials
                    // We're authenticated! Redirect to the home page
                    $this->_helper->redirector('dashboard', 'index', 'admin');
                }
            }
        }
    }

    public function warningAction() {
        $this->view->headTitle("Unauthorized Access");
    }

}
