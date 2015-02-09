<?php

class Application_Model_SeoUrl {

    protected $_id;
    protected $_actualUrl;
    protected $_seoUrl;
    protected $_urlType;
    protected $_createDate;
    protected $_status;
    protected $_mapper;
    protected $_metaTitle;
    protected $_metaKeywords;
    protected $_metaDescription;
    protected $_metaStatus;

    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property specified ' . $method);
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property specified');
        }
        return $this->$method();
    }

    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Application_Model_SeoUrlMapper());
        }
        return $this->_mapper;
    }

    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function setActualUrl($actualUrl) {
        $this->_actualUrl = (string) $actualUrl;
        return $this;
    }

    public function getActualUrl() {
        return $this->_actualUrl;
    }

    public function setSeoUrl($seoUrl) {
        $this->_seoUrl = (string) $seoUrl;
        return $this;
    }

    public function getSeoUrl() {
        return $this->_seoUrl;
    }

    public function setUrlType($urlType) {
        $this->_urlType = (int) $urlType;
        return $this;
    }

    public function getUrlType() {
        return $this->_urlType;
    }

    public function setCreateDate($createDate) {
        $this->_createDate = (int) $createDate;
        return $this;
    }

    public function getCreateDate() {
        return $this->_createDate;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setMetaTitle($metatitle) {
        $this->_metaTitle = $metatitle;
        return $this;
    }

    public function getMetaTitle() {
        return $this->_metaTitle;
    }

    public function setMetaKeywords($metakeywords) {
        $this->_metaKeywords = $metakeywords;
        return $this;
    }

    public function getMetaKeywords() {
        return $this->_metaKeywords;
    }

    public function setMetaDescription($metadescription) {
        $this->_metaDescription = $metadescription;
        return $this;
    }

    public function getMetaDescription() {
        return $this->_metaDescription;
    }

    public function setMetaStatus($metastatus) {
        $this->_metaStatus = $metastatus;
        return $this;
    }

    public function getMetaStatus() {
        return $this->_metaStatus;
    }

	
    /* ----Data Manupulation functions ---- */

    private function setModel($row) {


        $model = new Application_Model_SeoUrl();
        $model->setId($row->id)
                ->setActualUrl($row->actual_url)
                ->setSeoUrl($row->seo_url)
                ->setUrlType($row->url_type)
                ->setCreateDate($row->create_date)
                ->setStatus($row->status)
                ->setMetaTitle($row->meta_title)
                ->setMetaDescription($row->meta_description)
                ->setMetaKeywords($row->meta_keywords)
                ->setMetaKeywords($row->meta_keywords)
                ->setMetaStatus($row->meta_status)
        ;
        return $model;
    }

	public function sanitize_title($title) {
		$the_iso = array(
					" "=>"_"
					 );
		return strtr($title, $the_iso);
	}
	
    public function save() {
        $data = array(
            'seo_url' => $this->getSeoUrl(),
            'actual_url' => $this->getActualUrl(),
            'url_type' => $this->getUrlType(),
            'create_date' => $this->getCreateDate(),
            'status' => $this->getStatus(),
            'meta_title' => $this->getMetaTitle(),
            'meta_keywords' => $this->getMetaKeywords(),
            'meta_description' => $this->getMetaDescription()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['meta_status'] = 1;
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['meta_status'] = $this->getMetaStatus();
            return $this->getMapper()->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($id) {
        $result = $this->getMapper()->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }

        $row = $result->current();
        $res = $this->setModel($row);
        return $res;
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $resultSet = $this->getMapper()->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $res = $this->setModel($row);
            $entries[] = $res;
        }
        return $entries;
    }

    public function fetchRow($where) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where);

        if (!empty($row)) {
            $res = $this->setModel($row);
            return $res;
        } else {
            return false;
        }
    }

    public function delete($where) {
        return $this->getMapper()->getDbTable()->delete($where);
    }

    /* ----Data Manupulation functions ---- */


    /* ------Data utility functions------ */

    public function retrieveSeoUrl($string) {

        
        $FrontController = Zend_Controller_Front::getInstance();
        $controllerName = $FrontController->getRequest()->getControllerName();
        $actionName = $FrontController->getRequest()->getActionName();
          
         
        //echo '<pre>';print_r($FrontController->getRequest()->getParams());exit;
//        $seoUrl = $this->fetchRow("actual_url='{$string}' AND `url_type`!=4");
//        $dbstr = mysql_real_escape_string($string);
        $dbstr = addslashes($string);
        $seoUrl = $this->fetchRow("actual_url='{$dbstr}'");

        
        if (false !== $seoUrl) {
            return $seoUrl->getSeoUrl();
        }
        
        // if url not found in database then insert url and return the seo url
		
        switch ($controllerName) {
            case "sitemap":
                switch ($actionName) {
                    case "doctor":
                        return $this->doctorSitemap($string);
                        break;
                    case "specialty":
                        return $this->specialtySitemap($string);
                        break;
                    case "city":
                    case "neighborhood":
                        return $this->citySitemap($string);
                        break;
                    case "specialty-city":
                        return $this->specialtyCitySitemap($string);
                        break;
                    case "specialty-zipcode":
                        return $this->specialtyZipcodeSitemap($string);
                        break;
                    case "insurance-dentist":
                        return $this->insuranceDentistSitemap($string);
                        break;
                    case "insurance-other":
                        return $this->insuranceOtherSitemap($string);
                        break;
                    case "reason-for-visit":
                        return $this->reasonForVisitSitemap($string);
                        break;
                    case "insurance-dentist-city":
                        return $this->insuranceDentistCitySitemap($string);
                        break;
                    case "insurance-company-plan":
                        return $this->insuranceCompanyPlanSitemap($string);
                        break;
                }
                break;

            case "profile":
                switch ($actionName) {
                    case "index":
                        return $this->doctorSitemap($string);
                        break;
                }
                break;
			case "index":
				return $this->doctorSitemap($string);
                break;
            default:
                return $string;
                break;
        }
    }
	
	protected function createSeoUrl($url){
		$dbstr = addslashes($url);
        $seoUrl = $this->fetchRow("actual_url='{$dbstr}'");

        
        if (false !== $seoUrl) {
            return $seoUrl->getSeoUrl();
        }
		 
		return $this->doctorSitemap($url);
	}

    protected function doctorSitemap($real_url) {

        if(strpos($real_url, 'show-timeslot')!==false)return $real_url;
        if(strpos($real_url, 'all-insurances')!==false)return $real_url;

        $array = explode("/", $real_url);
        $drid = (int) array_pop($array);

        if ($drid < 1
            )return $real_url;
        $Doctor = new Application_Model_Doctor();
        $object = $Doctor->find($drid);

        if (!$object
            )return $real_url;

        $seourl = $this->sanitize_title(trim($object->getFname()));
        $seourl = preg_replace("/[,.']+/", "", $seourl);
        $seourl = strtolower(ereg_replace("[ \t\n\r]+", "-", $seourl));
        $seourl = str_replace("&", "-", $seourl);
        $seourl = ereg_replace("[-]+", "-", $seourl);

        $seourl = "/" . $seourl;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $seourl = $object1->getSeoUrl();
        } else {
            
            $object2 = $this->fetchAll("seo_url LIKE '{$seourl}%'");
            $tempSeourl = $seourl;
            if (!empty($object2)) {
                $seourl = $seourl . '-' . (count($object2) + 1);
            }
            
            $object3 = $this->fetchRow("seo_url LIKE '{$seourl}%'");
            if($object3){
                $seourl = $tempSeourl . '-' . (count($object2) + 2);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($seourl);
            $Model->setUrlType('1');
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->setMetaTitle('');
            $Model->setMetaKeywords('');
            $Model->setMetaDescription('');
            $Model->save();
        }
        return $seourl;
    }

    protected function specialtySitemap($real_url) {
        $array = explode("=", $real_url);
        $catid = (int) array_pop($array);

        if ($catid < 1
            )return $real_url;
        $Category = new Application_Model_Category();
        $object = $Category->find($catid);

        if (!$object
            )return $real_url;

        $url_sef = str_replace(" ", "-", $this->sanitize_title(strtolower(trim($object->getName()))));
        $url_sef = str_replace(".", "", $url_sef);
        $url_sef = str_replace(",", "", $url_sef);
        $url_sef = str_replace("'", "", $url_sef);
        $url_sef = str_replace('"', "", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

    protected function citySitemap($real_url) {

        $array = explode("=", $real_url);
        $city = trim(array_pop($array));

        if ($city == '')return $real_url;
        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($city))));
        $url_sef = str_replace($replaceArray, "", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

//        $dbstring = mysql_real_escape_string($real_url);
        $dbstring = addslashes($real_url);
        $object1 = $this->fetchRow("actual_url='{$dbstring}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }

            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

    protected function specialtyCitySitemap($real_url) {


        $array = explode("=", $real_url);
        if (!isset($array[1]) || !isset($array[2])) {
            return $real_url;
        }
        $catid = trim(str_replace('&search1', '', $array[1]));
        $city = trim($array[2]);
        if ($city == '' || $catid < 1
            )return $real_url;

        $Category = new Application_Model_Category();
        $object = $Category->find($catid);

        if (!$object
            )return $real_url;

        $url_sef = $object->getName() . '-' . $city;

        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function

    protected function specialtyZipcodeSitemap($real_url) {

        $array = explode("=", $real_url);
        if (!isset($array[1]) || !isset($array[2])) {
            return $real_url;
        }
        $catid = trim(str_replace('&search1', '', $array[1]));
        $zipcode = trim($array[2]);
        if ($zipcode == '' || $catid < 1
            )return $real_url;

        $Category = new Application_Model_Category();
        $object = $Category->find($catid);

        if (!$object
            )return $real_url;

        $url_sef = $object->getName() . '-' . $zipcode;

        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function

    protected function insuranceDentistSitemap($real_url) {

        $array = explode("=", $real_url);
        //echo "<pre>";print_r($array);
        $company_id = trim(array_pop($array));

        if ($company_id < 1
            )return $real_url;

        $Company = new Application_Model_InsuranceCompany();
        $object = $Company->find($company_id);

        if (!$object
            )return $real_url;

        $url_sef = 'dentist-' . $object->getCompany();

        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function

    protected function insuranceOtherSitemap($real_url) {

        $array = explode("=", $real_url);
        if (!isset($array[1]) || !isset($array[2])) {
            return $real_url;
        }
        $catid = trim(str_replace('&insurance', '', $array[1]));
        $company_id = trim($array[2]);
        if ($company_id < 1 || $catid < 1
            )return $real_url;

        $Category = new Application_Model_Category();
        $object = $Category->find($catid);
        if (!$object
            )return $real_url;

        $InsuranceCompany = new Application_Model_InsuranceCompany();
        $insObject = $InsuranceCompany->find($company_id);
        if (!$insObject
            )return $real_url;

        $url_sef = $object->getName() . '-' . $insObject->getCompany();

        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");

            if (!empty($object2)) {

                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function

    protected function reasonForVisitSitemap($real_url) {

        $array = explode("=", $real_url);
        if (!isset($array[1]) || !isset($array[2])) {
            return $real_url;
        }
        $catid = trim(str_replace('&reason', '', $array[1]));
        $reason_id = trim($array[2]);
        if ($reason_id < 1 || $catid < 1
            )return $real_url;

        $Category = new Application_Model_Category();
        $object = $Category->find($catid);
        if (!$object
            )return $real_url;

        $ReasonForVisit = new Application_Model_ReasonForVisit();
        $reasonObject = $ReasonForVisit->find($reason_id);
        if (!$reasonObject
            )return $real_url;

        $url_sef = $object->getName() . '-' . $reasonObject->getReason();

        $replaceArray = array(".", ",", "'", '"', '/');
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function

    protected function insuranceDentistCitySitemap($real_url) {


        $array = explode("=", $real_url);

        $company_id = trim(array_pop($array));

        if ($company_id < 1
            )return $real_url;

        $Company = new Application_Model_InsuranceCompany();
        $compObject = $Company->find($company_id);

        if (!$compObject
            )return $real_url;

        $city = trim(str_replace('&insurance', '', $array[2]));
        if($city=='')return $real_url;

        $catid = trim(str_replace('&search1', '', $array[1]));

        
        $url_sef = 'dentist-' . $compObject->getCompany() .'-'.$city;
        //prexit(array($real_url, $url_sef));
        $replaceArray = array(".", ",", "'", '"', "/");
        $url_sef = str_replace(" ", "-", strtolower($this->sanitize_title(trim($url_sef))));
        $url_sef = str_replace($replaceArray, "-", $url_sef);
        $url_sef = str_replace("&", "-", $url_sef);
        $url_sef = ereg_replace("[-]+", "-", $url_sef);

        $url_sef = "/" . $url_sef;

        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('3'); // for sitemap urls
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function


    protected function insuranceCompanyPlanSitemap($real_url) {
        
        $array = explode("=", $real_url);
        $array1 = explode("&", $array[1]);
        $company_id = trim($array1[0]);

        if ($company_id < 1
            )return $real_url;

        $Company = new Application_Model_InsuranceCompany();
        $compObject = $Company->find($company_id);

        if (!$compObject
            )return $real_url;

        $plan_id = array_pop($array);
        if($plan_id < 1)return $real_url;

        $Plan = new Application_Model_InsurancePlan();
        $planObject = $Plan->find($plan_id);
        if (!$planObject
            )return $real_url;

        $url_sef = $compObject->getCompany() .'-'.$planObject->getPlan();
        $replaceArray = array("(\&trade;+)","(\&reg;+)", "(\&+)", "(\ +)", "(\.+)", "(\,+)", "(\'+)", '(\"+)', "(\/+)");
        $url_sef = preg_replace($replaceArray, "-", $url_sef);
        $url_sef = preg_replace("(\-+)", "-", $url_sef);
        $url_sef = trim(strtolower($this->sanitize_title($url_sef)), "-");

        $url_sef = "/" . $url_sef;
        
        $object1 = $this->fetchRow("actual_url='{$real_url}'");
        if (!empty($object1)) {
            $url_sef = $object1->getSeoUrl();
        } else {
            $object2 = $this->fetchAll("seo_url LIKE '{$url_sef}%'");
            if (!empty($object2)) {
                $url_sef = $url_sef . '-' . (count($object2) + 1);
            }
            $Model = new Application_Model_SeoUrl();
            $Model->setActualUrl($real_url);
            $Model->setSeoUrl($url_sef);
            $Model->setUrlType('6'); 
            $Model->setCreateDate(time());
            $Model->setStatus('1');
            $Model->save();
        }
        return $url_sef;
    }

// end function
	
}

// end class
?>
