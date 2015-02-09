<?php

class Base_Plugin_Action extends Zend_Controller_Plugin_Abstract {

    public function routeStartup(Zend_Controller_Request_Abstract $request) {

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        if ($config->seofriendlyurl == "1") {
            $seoUrlM = new Application_Model_SeoUrl();

            $requestURI = $_SERVER['REQUEST_URI'];

            $strpos1 = strpos($requestURI, "&");  // position for &;
            $strpos2 = strpos($requestURI, "?");  // position for ?;

            $strpos1 = $strpos1 ? $strpos1 : 0;
            $strpos2 = $strpos2 ? $strpos2 : 0;

            if ($strpos1 > 0 && $strpos2 > 0) {
                if ($strpos1 > $strpos2) {
                    $separator = "?";
                    $strposreplace = $strpos2;
                } elseif ($strpos2 > $strpos1) {
                    $separator = "&";
                    $strposreplace = $strpos1;
                } else {
                    $separator = "DIH";
                    $strposreplace = "DIH";
                }
            } elseif ($strpos1 > 0 && $strpos2 == 0) {
                $separator = "&";
                $strposreplace = $strpos1;
            } elseif ($strpos2 > 0 && $strpos1 == 0) {
                $separator = "?";
                $strposreplace = $strpos2;
            } else {
                $strposreplace = '';
            }

            if ($strposreplace != '') {
                $initUrl = substr($requestURI, 0, $strposreplace);
                $remainingUrl = substr($requestURI, $strposreplace + 1);
            } else {
                $initUrl = $requestURI;
                $remainingUrl = "";
            }
            $seoUrl = $seoUrlM->fetchRow("seo_url='" . $initUrl . "' AND status=1");

            if (false !== $seoUrl) {
                if ($remainingUrl != '') {

//                    $changeUrl = implode('?',array($seoUrl->getActualUrl(),$urlArray[1]));
                    if (strstr($seoUrl->getActualUrl(), "?")
                        )$separator = "&";
                    else
                        $separator = "?";
                    $remainingUrl = str_replace("?", "&", $remainingUrl);
                    $changeUrl = $seoUrl->getActualUrl() . $separator . $remainingUrl;
                }else {
                    $changeUrl = $seoUrl->getActualUrl();
                }
                $request->setRequestUri($changeUrl);
            } else {

            }
        }// if for seo urls

        // Cron Class
        Base_CronJob::run();
    }

    public function routeStartupTemporary($request) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        if ($config->seofriendlyurl == "1") {
            $seoUrlM = new Application_Model_SeoUrl();
            $requestUri = str_replace("?", "&", $request->getRequestUri());
//            $urlArray = explode('&', $request->getRequestUri());
            $urlArray = explode('&', $requestUri);

//            $initUrl = ltrim ($urlArray[0], "/");
            $initUrl = $urlArray[0];
            //echo "<pre>";print_r($initUrl);exit;
            $seoUrl = $seoUrlM->fetchRow("seo_url='" . $initUrl . "' AND status=1");

            if (false !== $seoUrl) {
                if (isset($urlArray[1])) {

                    $changeUrl = implode('?', array($seoUrl->getActualUrl(), $urlArray[1]));
                } else {
                    $changeUrl = $seoUrl->getActualUrl();
                }
                $request->setRequestUri($changeUrl);
            } else {

            }
        }// if for seo urls
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        // for meta Tags
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $cookie = new Base_Http_Cookie();

        /* if (!$cookie->isExpired('rememberMe')) {
          if (Zend_Auth::getInstance()->hasIdentity() != true) {

          $params = unserialize(base64_decode($cookie->getCookie('rememberMe')));

          $Auth = new Base_Auth_Auth();
          $Auth->doLogout();
          $loginStatusEmail = true;
          $loginStatusUsername = true;

          $loginStatusEmail = $Auth->doLogin($params, 'email');
          if ($loginStatusEmail == false) {

          $loginStatusUsername = $Auth->doLogin($params, 'username');
          }
          }
          } */

        if (Zend_Auth::getInstance()->hasIdentity() != true) {
            //$user=new Application_Model_User();
            //$result=$user->doFacebookLogin();
        }//end of has identity
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

    }

    public function dispatchLoopShutdown() {
        
    }

}