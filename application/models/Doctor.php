<?php

/**
 * User model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single
 * user entry.
 *
 * @uses       Application_Model_Doctor
 * @package    Directory
 * @subpackage Model
 */
class Application_Model_Doctor {

    /**
     * @var int
     */
    protected $_id;
    protected $_userId;
    protected $_memberNumber;
    protected $_categoryId;
    protected $_fname;
    protected $_company;
    protected $_specialtyTitle;
    protected $_street;
    protected $_zipcode;
    protected $_zipcode1;
    protected $_zipcode2;
    protected $_zipcode3;
    protected $_zipcode4;
    protected $_zipcode5;
    protected $_city;
    protected $_country;
    protected $_officeHours;
    protected $_education;
    protected $_creditlines;
    protected $_associates;
    protected $_assignPhone;
    protected $_actualPhone;
    protected $_awards;
    protected $_about;
    protected $_amenities;
    protected $_paymentOptions;
    protected $_insuranceAccepted;
    protected $_office;
    protected $_language;
    protected $_association;
    protected $_featured;
    protected $_geocode;
    protected $_membershipLevel;
    protected $_membershipLevelNo;
    protected $_yearsAtPractice;
    protected $_yearsPractice;
    protected $_communityInvolvement;
    protected $_hobbies;
    protected $_staff;
    protected $_services;
    protected $_technology;
    protected $_brands;
    protected $_video;
	protected $_photos;
	protected $_area;
    protected $_specialNeeds;
    protected $_testimonials;
    protected $_state;
    protected $_gallery;
    protected $_clicktotalkurl;
    protected $_county;
    protected $_website;
    protected $_companylogo;
    protected $_companyicon;
    protected $_publishUp;
    protected $_publishDown;
    protected $_status;
    protected $_textAward;
    protected $_mapper;
    protected $_useZip;
    protected $_useZip1;
    protected $_useZip2;
    protected $_useZip3;
    protected $_useZip4;
    protected $_useZip5;

    /**
     * Constructor
     *
     * @param  array|null $options
     * @return void
     */
    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Overloading: allow property access
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value) {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property specified');
        }
        $this->$method($value);
    }

    /**
     * Overloading: allow property access
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name) {
        $method = 'get' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property specified');
        }
        return $this->$method();
    }

    /**
     * Set object state
     *
     * @param  array $options
     * @return Directory_Model_DirectoryCategory
     */
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

    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }


    public function getId() {
        return $this->_id;
    }

    public function setUserId($userid) {
        $this->_userId = (int) $userid;
        return $this;
    }

    public function getUserId() {
        return $this->_userId;
    }

    public function setMemberNumber($memberNumber) {
        $this->_memberNumber = (string) $memberNumber;
        return $this;
    }

    public function getMemberNumber() {
        return $this->_memberNumber;
    }

    public function getCategoryId() {
        return $this->_categoryId;
    }

    public function setCategoryId($categoryId) {
        $this->_categoryId = (int) $categoryId;
        return $this;
    }

    public function setSpecialtyTitle($specialtyTitle) {
        $this->_specialtyTitle = (string) $specialtyTitle;
        return $this;
    }

    public function getSpecialtyTitle() {
        return $this->_specialtyTitle;
    }

    public function setFname($fname) {
        $this->_fname = (string) $fname;
        return $this;
    }

    public function getFname() {
        return $this->_fname;
    }

    public function setCompany($company) {
        $this->_company = (string) $company;
        return $this;
    }

    public function getCompany() {
        return $this->_company;
    }

    public function getStreet() {
        return $this->_street;
    }

    public function setStreet($street) {
        $this->_street = (string) $street;
        return $this;
    }

    public function getZipcode() {
        return $this->_zipcode;
    }

    public function setZipcode($zipcode) {
        $this->_zipcode = (string) $zipcode;
        return $this;
    }


    public function getZipcode1() {
        return $this->_zipcode1;
    }

    public function setZipcode1($zipcode) {
        $this->_zipcode1 = (string) $zipcode;
        return $this;
    }


    public function getZipcode2() {
        return $this->_zipcode2;
    }

    public function setZipcode2($zipcode) {
        $this->_zipcode2 = (string) $zipcode;
        return $this;
    }

    public function getZipcode3() {
        return $this->_zipcode3;
    }

    public function setZipcode3($zipcode) {
        $this->_zipcode3 = (string) $zipcode;
        return $this;
    }

    public function getZipcode4() {
        return $this->_zipcode4;
    }

    public function setZipcode4($zipcode) {
        $this->_zipcode4 = (string) $zipcode;
        return $this;
    }

    public function getZipcode5() {
        return $this->_zipcode5;
    }

    public function setZipcode5($zipcode) {
        $this->_zipcode5 = (string) $zipcode;
        return $this;
    }

    public function getCity() {
        return $this->_city;
    }

    public function setCity($city) {
        $this->_city = (string) $city;
        return $this;
    }

    public function getCountry() {
        return $this->_city;
    }

    public function setCountry($country) {
        $this->_country = (string) $country;
        return $this;
    }

    public function getOfficeHours() {
        return $this->_officeHours;
    }

    public function setOfficeHours($officeHours) {
        $this->_officeHours = (string) $officeHours;
        return $this;
    }

    public function getEducation() {
        return $this->_education;
    }

    public function setEducation($education) {
        $this->_education = (string) $education;
        return $this;
    }

    public function getCreditlines() {
        return $this->_creditlines;
    }

    public function setCreditlines($creditlines) {
        $this->_creditlines = (string) $creditlines;
        return $this;
    }

    public function getAssociates() {
        return $this->_associates;
    }

    public function setAssociates($associates) {
        $this->_associates = (string) $associates;
        return $this;
    }

    public function getAssignPhone() {
        return $this->_assignPhone;
    }

    public function setAssignPhone($assignPhone) {
        $this->_assignPhone = (string) $assignPhone;
        return $this;
    }

    public function getActualPhone() {
        return $this->_actualPhone;
    }

    public function setActualPhone($actualPhone) {
        $this->_actualPhone = (string) $actualPhone;
        return $this;
    }

    public function getAwards() {
        return $this->_awards;
    }

    public function setAwards($awards) {
        $this->_awards = (string) $awards;
        return $this;
    }

    public function getAbout() {
        return $this->_about;
    }

    public function setAbout($about) {
        $this->_about = (string) $about;
        return $this;
    }

    public function getAmenities() {
        return $this->_amenities;
    }

    public function setAmenities($amenities) {
        $this->_amenities = (string) $amenities;
        return $this;
    }

    public function getPaymentOptions() {
        return $this->_paymentOptions;
    }

    public function setPaymentOptions($paymentOptions) {
        $this->_paymentOptions = (string) $paymentOptions;
        return $this;
    }

    public function getInsuranceAccepted() {
        return $this->_insuranceAccepted;
    }

    public function setInsuranceAccepted($insuranceAccepted) {
        $this->_insuranceAccepted = (string) $insuranceAccepted;
        return $this;
    }

    public function getOffice() {
        return $this->_office;
    }

    public function setOffice($office) {
        $this->_office = (string) $office;
        return $this;
    }

    public function getLanguage() {
        return $this->_language;
    }

    public function setLanguage($language) {
        $this->_language = (string) $language;
        return $this;
    }

    public function getAssociation() {
        return $this->_association;
    }

    public function setAssociation($association) {
        $this->_association = (string) $association;
        return $this;
    }

    public function getFeatured() {
        return $this->_featured;
    }

    public function setFeatured($featured) {
        $this->_featured = (int) $featured;
        return $this;
    }

    public function getGeocode() {
        return $this->_geocode;
    }

    public function setGeocode($geocode) {
        $this->_geocode = (string) $geocode;
        return $this;
    }

    public function getMembershipLevel() {
        return $this->_membershipLevel;
    }

    public function setMembershipLevel($membershipLevel) {
        $this->_membershipLevel = (string) $membershipLevel;
        return $this;
    }
    public function getMembershipLevelNo() {
        return $this->_membershipLevelNo;
    }

    public function setMembershipLevelNo($membershipLevelNo) {
        $this->_membershipLevelNo = (int) $membershipLevelNo;
        return $this;
    }

    public function getYearsAtPractice() {
        return $this->_yearsAtPractice;
    }

    public function setYearsAtPractice($yearsAtPractice) {
        $this->_yearsAtPractice = (string) $yearsAtPractice;
        return $this;
    }

    public function getYearsPractice() {
        return $this->_yearsPractice;
    }

    public function setYearsPractice($yearsPractice) {
        $this->_yearsPractice = (string) $yearsPractice;
        return $this;
    }

    public function getCommunityInvolvement() {
        return $this->_communityInvolvement;
    }

    public function setCommunityInvolvement($communityInvolvement) {
        $this->_communityInvolvement = (string) $communityInvolvement;
        return $this;
    }

    public function getHobbies() {
        return $this->_hobbies;
    }

    public function setHobbies($hobbies) {
        $this->_hobbies = (string) $hobbies;
        return $this;
    }

    public function getStaff() {
        return $this->_staff;
    }

    public function setStaff($staff) {
        $this->_staff = (string) $staff;
        return $this;
    }

    public function getServices() {
        return $this->_services;
    }

    public function setServices($services) {
        $this->_services = (string) $services;
        return $this;
    }

    public function getTechnology() {
        return $this->_technology;
    }

    public function setTechnology($technology) {
        $this->_technology = (string) $technology;
        return $this;
    }

    public function getBrands() {
        return $this->_brands;
    }

    public function setBrands($brands) {
        $this->_brands = (string) $brands;
        return $this;
    }

    public function getVideo() {
        return $this->_video;
    }

    public function setVideo($video) {
        $this->_video = (string) $video;
        return $this;
    }
	
	public function getPhotos() {
        return $this->_photos;
    }

    public function setPhotos($photos) {
        $this->_photos = (string) $photos;
        return $this;
    }
	
	public function getArea() {
        return $this->_area;
    }

    public function setArea($area) {
        $this->_area = (string) $area;
        return $this;
    }

    public function getSpecialNeeds() {
        return $this->_specialNeeds;
    }

    public function setSpecialNeeds($specialNeeds) {
        $this->_specialNeeds = (string) $specialNeeds;
        return $this;
    }

    public function getTestimonials() {
        return $this->_testimonials;
    }

    public function setTestimonials($testimonials) {
        $this->_testimonials = (string) $testimonials;
        return $this;
    }

    public function getState() {
        return $this->_state;
    }

    public function setState($state) {
        $this->_state = (string) $state;
        return $this;
    }

    public function getGallery() {
        return $this->_gallery;
    }

    public function setGallery($gallery) {
        $this->_gallery = (string) $gallery;
        return $this;
    }

    public function getClicktotalkurl() {
        return $this->_clicktotalkurl;
    }

    public function setClicktotalkurl($clicktotalkurl) {
        $this->_clicktotalkurl = (string) $clicktotalkurl;
        return $this;
    }

    public function getCounty() {
        return $this->_county;
    }

    public function setCounty($county) {
        $this->_county = (string) $county;
        return $this;
    }

    public function getWebsite() {
        return $this->_website;
    }

    public function setWebsite($website) {
        $this->_website = (string) $website;
        return $this;
    }

    public function getCompanylogo() {
        return $this->_companylogo;
    }

    public function setCompanylogo($companylogo) {
        $this->_companylogo = (string) $companylogo;
        return $this;
    }

    public function getCompanyicon() {
        return $this->_companyicon;
    }

    public function setCompanyicon($companyicon) {
        $this->_companyicon = (string) $companyicon;
        return $this;
    }

    public function getPublishUp() {
        return $this->_publishUp;
    }

    public function setPublishUp($publishUp) {
        $this->_publishUp = (int) $publishUp;
        return $this;
    }

    public function getPublishDown() {
        return $this->_publishDown;
    }

    public function setPublishDown($publishDown) {
        $this->_publishDown = (int) $publishDown;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }


    public function setTextAward($award) {
        $this->_textAward = (string) $award;
        return $this;
    }

    public function getTextAward() {

        return $this->_textAward;
    }
    
    public function setUseZip($use_zip) {
        $this->_useZip = $use_zip;
        return $this;
    }

    public function getUseZip() {

        return $this->_useZip;
    }
    
    
    public function setUseZip1($use_zip1) {
        $this->_useZip1 = $use_zip1;
        return $this;
    }

    public function getUseZip1() {

        return $this->_useZip1;
    }

    
    public function setUseZip2($use_zip2) {
        $this->_useZip2 = $use_zip2;
        return $this;
    }

    public function getUseZip2() {

        return $this->_useZip2;
    }

    
    public function setUseZip3($use_zip3) {
        $this->_useZip3 = $use_zip3;
        return $this;
    }

    public function getUseZip3() {

        return $this->_useZip3;
    }

    
    public function setUseZip4($use_zip4) {
        $this->_useZip4 = $use_zip4;
        return $this;
    }

    public function getUseZip4() {

        return $this->_useZip4;
    }

    
    public function setUseZip5($use_zip5) {
        $this->_useZip5 = $use_zip5;
        return $this;
    }

    public function getUseZip5() {

        return $this->_useZip5;
    }    

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    /**
     * Get data mapper
     *
     * Lazy loads Directory_Model_DirectoryCategoryMapper instance if no mapper registered.
     *
     * @return Directory_Model_DirectoryCategory
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Application_Model_DoctorMapper());
        }
        return $this->_mapper;
    }

    private function setModel($row) {
        $model = new Application_Model_Doctor();
        $model->setId($row->id)
                ->setUserId($row->user_id)
                ->setMemberNumber($row->member_number)
                ->setCategoryId($row->category_id)
                ->setSpecialtyTitle($row->specialty_title)
                ->setFname($row->fname)
                ->setCompany($row->company)
                ->setStreet($row->street)
                ->setZipcode($row->zipcode)
                ->setZipcode1($row->zipcode1)
                ->setZipcode2($row->zipcode2)
                ->setZipcode3($row->zipcode3)
                ->setZipcode4($row->zipcode4)
                ->setZipcode5($row->zipcode5)
                ->setCity($row->city)
                ->setCountry($row->country)
                ->setOfficeHours($row->office_hours)
                ->setEducation($row->education)
                ->setCreditlines($row->creditlines)
                ->setAssociates($row->associates)
                ->setAssignPhone($row->assign_phone)
                ->setActualPhone($row->actual_phone)
                ->setAwards($row->awards)
                ->setAbout($row->about)
                ->setAmenities($row->amenities)
                ->setPaymentOptions($row->payment_options)
                ->setInsuranceAccepted($row->insurance_accepted)
                ->setOffice($row->office)
                ->setLanguage($row->language)
                ->setAssociation($row->association)
                ->setFeatured($row->featured)
                ->setGeocode($row->geocode)
                ->setMembershipLevel($row->membership_level)
                ->setMembershipLevelNo($row->membership_level_no)
                ->setYearsAtPractice($row->years_at_practice)
                ->setYearsPractice($row->years_practicing)
                ->setCommunityInvolvement($row->community_involvement)
                ->setHobbies($row->hobbies)
                ->setStaff($row->staff)
                ->setServices($row->services)
                ->setTechnology($row->technology)
                ->setBrands($row->brands)
                ->setVideo($row->video)
				->setPhotos($row->photos)
				->setArea($row->area)
                ->setSpecialNeeds($row->special_needs)
                ->setTestimonials($row->testimonials)
                ->setState($row->state)
                ->setGallery($row->gallery)
                ->setClicktotalkurl($row->clicktotalkurl)
                ->setCounty($row->county)
                ->setWebsite($row->website)
                ->setCompanylogo($row->company_logo)
                ->setCompanyicon($row->company_icon)
                ->setPublishUp($row->publish_up)
                ->setPublishDown($row->publish_down)
                ->setStatus($row->status)
                ->setTextAward($row->text_award)
                ->setUseZip($row->use_zip)
                ->setUseZip1($row->use_zip1)
                ->setUseZip2($row->use_zip2)
                ->setUseZip3($row->use_zip3)
                ->setUseZip4($row->use_zip4)
                ->setUseZip5($row->use_zip5)
                ;
                
        return $model;
    }

    /**
     * Save the current entry
     *
     * @return void
     */
    public function save() {

        $data = array(
            'user_id' => $this->getUserId(),
            'member_number' => $this->getMemberNumber(),
            'category_id' => $this->getCategoryId(),
            'specialty_title' => $this->getSpecialtyTitle(),
            'fname' => $this->getFname(),
            'company' => $this->getCompany(),
            'street' => $this->getStreet(),
            'zipcode' => $this->getZipcode(),
            'zipcode1' => $this->getZipcode1(),
            'zipcode2' => $this->getZipcode2(),
            'zipcode3' => $this->getZipcode3(),
            'zipcode4' => $this->getZipcode4(),
            'zipcode5' => $this->getZipcode5(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'office_hours' => $this->getOfficeHours(),
            'education' => $this->getEducation(),
            'creditlines' => $this->getCreditlines(),
            'associates' => $this->getAssociates(),
            'assign_phone' => $this->getAssignPhone(),
            'actual_phone' => $this->getActualPhone(),
            'awards' => $this->getAwards(),
            'about' => $this->getAbout(),
            'amenities' => $this->getAmenities(),
            'payment_options' => $this->getPaymentOptions(),
            'insurance_accepted' => $this->getInsuranceAccepted(),
            'office' => $this->getOffice(),
            'language' => $this->getLanguage(),
            'association' => $this->getAssociation(),
            'featured' => $this->getFeatured(),
            'geocode' => $this->getGeocode(),
            'membership_level' => $this->getMembershipLevel(),
            'membership_level_no' => $this->getMembershipLevelNo(),
            'years_practicing' => $this->getYearsPractice(),
            'years_at_practice' => $this->getYearsAtPractice(),
            'community_involvement' => $this->getCommunityInvolvement(),
            'hobbies' => $this->getHobbies(),
            'staff' => $this->getStaff(),
            'services' => $this->getServices(),
            'technology' => $this->getTechnology(),
            'brands' => $this->getBrands(),
            'video' => $this->getVideo(),
			'photos' => $this->getPhotos(),
			'area' => $this->getArea(),
            'special_needs' => $this->getSpecialNeeds(),
            'testimonials' => $this->getTestimonials(),
            'state' => $this->getState(),
            'gallery' => $this->getGallery(),
            'clicktotalkurl' => $this->getClicktotalkurl(),
            'county' => $this->getCounty(),
            'website' => $this->getWebsite(),
            'company_logo' => $this->getCompanylogo(),
            'publish_up' => $this->getPublishUp(),
            'publish_down' => $this->getPublishDown(),
            'company_icon' => $this->getCompanyicon(),
            'text_award' => $this->getTextAward(),
        	'use_zip' => $this->getUseZip(),
        	'use_zip1' => $this->getUseZip1(),
        	'use_zip2' => $this->getUseZip2(),
        	'use_zip3' => $this->getUseZip3(),
        	'use_zip4' => $this->getUseZip4(),
        	'use_zip5' => $this->getUseZip5()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['status'] = 1;
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['status'] = $this->getStatus();
            return $this->getMapper()->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    /**
     * Find an entry
     *
     * Resets entry state if matching id found.
     *
     * @param  int $id
     * @return User_Model_User
     */
    public function find($id) {
        $result = $this->getMapper()->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }

        $row = $result->current();
        $res = $this->setModel($row);

        return $res;
    }

    /**
     * Fetch all entries
     *
     * @return array
     */
    public function fetchAll($where=null, $order=null, $count=null, $offset=null) {

        $resultSet = $this->getMapper()->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $res = $this->setModel($row);
            $entries[] = $res;
        }
        return $entries;
    }

    public function fetchAllRow($where=null, $order=null, $count=null, $offset=null) {

        $resultSet = $this->getMapper()->getDbTable()->fetchAll($where, $order, $count, $offset);

        return $resultSet;
    }

    public function fetchRow($where=null, $order=null) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);

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


    public function getReasonForVisit($doctor_id) {
        $DocReason = new Application_Model_DoctorReasonForVisit();
        $Reason = new Application_Model_ReasonForVisit();
        $return = array();
        $docreasonObject = $DocReason->fetchAll("doctor_id='{$doctor_id}'");
        $array = array();
        if (!empty($docreasonObject)) {
            foreach ($docreasonObject as $rObj) {
                $array[] = $rObj->getReasonId();
            }
            $str = implode(',', $array);
            $reasonObject = $Reason->fetchAll("id IN  ({$str}) AND status='1'", "reason ASC");
            if (!empty($reasonObject)) {
                foreach ($reasonObject as $object) {
                    $return[$object->getId()] = $object->getReason();
                }
            }
        }
        //echo "<pre>";print_r($return);exit;
        return $return;
    }

    public function getReasonForVisitArray($where) {
        $Reason = new Application_Model_ReasonForVisit();
        $return = array();
        if($where!='')$where = "{$where} AND status='1'";
        else $where = "status='1'";
            $reasonObject = $Reason->fetchAll("{$where} AND status='1'", "reason ASC");
            if (!empty($reasonObject)) {
                foreach ($reasonObject as $object) {
                    $return[$object->getId()] = $object->getReason();
                }
            }
        return $return;
    }

    public function getInsuranceCompany($doctor_id = null) {
        $DocInsurance = new Application_Model_DoctorInsurance();
        $Insurance = new Application_Model_InsuranceCompany();
        $return = array();
        $where = "status='1'";
        if($doctor_id > 0){
            $docInsObject = $DocInsurance->fetchAll("doctor_id='{$doctor_id}'");
            $array = array();
            if (!empty($docInsObject)) {
                foreach ($docInsObject as $rObj) {
                    $array[] = $rObj->getInsuranceId();
                }
                if(count($array)>0){
                    $str = implode(',', $array);
                }else{
                    $str = "0";
                }
                $where .= " AND id IN ({$str})";
            }
        }

        $object = $Insurance->fetchAll($where, "company ASC");
            if (!empty($object)) {
                foreach ($object as $obj) {
                    $return[$obj->getId()] = $obj->getCompany();
                }
            }
        //echo "<pre>";print_r($return);exit;
        return $return;
    }

    public function getZipcodesusa($zipcode) {
        $db = Zend_Registry::get('db');
        $geocode = "";
        $query = "SELECT latitude, longitude FROM  `zipcodesusa` WHERE 	zipcode='{$zipcode}'";
        $select = $db->query($query);
        $result = $select->fetch();

        if ($result) {
            $geocode = $result->latitude . "," . $result->longitude;
        }
        return $geocode;
    }

    public function getUniqueCities() {
        $db = Zend_Registry::get('db');
        $geocode = "";
        $query = "SELECT DISTINCT `city` FROM doctors WHERE status='1' AND `city`!='' ORDER BY city";
        $select = $db->query($query);
        $result = $select->fetchAll();
        return $result;
    }
	
	/* search for areas of the specified city */
	public function getAllCityAreas($city) {
        $db = Zend_Registry::get('db');
		$city = $db->quote($city);
        $geocode = "";
        $query = "SELECT DISTINCT `area` FROM doctors WHERE status='1' AND `city`=$city ORDER BY area";
        $select = $db->query($query);
        $result = $select->fetchAll();
		if($result == null) {// no subareas, return citywide (city)
			$setting = new Admin_Model_GlobalSettings();
			$default_city = $setting->settingValue('default_city');
			
			$query = "SELECT DISTINCT `city` as area FROM doctors WHERE status='1' AND `city`!='' AND state='".$default_city."' ORDER BY city";
			$select = $db->query($query);
			$result = $select->fetchAll();
		}
		return $result;
    }

    public function getDoctors() {
        $obj = new Application_Model_Doctor();
        $entries = $obj->fetchAll("status='1'");
        $arrallDoctors = array();
        foreach ($entries as $entry) {
            $arrallDoctors[$entry->getId()] = $entry->getFname();
        }
        return $arrallDoctors();
    }
	
	public function getDoctors2($where=null, $option=null) {
        $obj=new Application_Model_Doctor();
        $entries=$obj->fetchAll($where);
        $arrallDoctors=array();
        if(!is_null($option))
            $arrallDoctors['']=$option;
        $arrallDoctors[]= 'Select Doctor';
        foreach($entries as $entry) {
            $arrallDoctors[$entry->getId()]=$entry->getFname();
        }
        return $arrallDoctors;
    }

    public function getDoctorCategoryList($did) {
        $catStr = '';
        $DocCategory = new Application_Model_DoctorCategory();
        $categoryArr = $DocCategory->getDoctorCategories("doctor_id='{$did}'");
        if (!empty($categoryArr)){
            $catStr = implode(', ', $categoryArr);
        }
        return $catStr;
    }
	
	
	/****svelon ****/
	public function stringToArrayWithNewLines($string) {
		$array = array();
		
		$explodeArray = explode("\r\n\r\n",$string);
		//$explodeArray = explode("\n",str_replace(array('and','.',','),"\n",  stripslashes($string)));
		//$ArrEducation = explode("\n",$string);
		foreach($explodeArray as $key=>$value){
            $value = trim($value);
            if(strlen($value)>1){
					$value = str_replace("\n", "<br/>", $value);
                    $array[] = $value;
            }
        }
        return $array;
    }
	
	
    public function stringToArray($string) {
       $array = array();
       $explodeArray = explode("\n",$string);
       //$explodeArray = explode("\n",str_replace(array('and','.',','),"\n",  stripslashes($string)));
       //$ArrEducation = explode("\n",$string);
        foreach($explodeArray as $key=>$value){
            $value = trim($value);
            if(strlen($value)>1){
                    $array[] = $value;
            }
        }
        return $array;
    }

    public function breakWithComma($string) {
       $array = array();
       $explodeArray = explode(",",$string);
        foreach($explodeArray as $key=>$value){
            $value = trim($value);
            if(strlen($value)>1){
                    $array[] = $value;
            }
        }
        return $array;
    }
	
	/* true if can have more appointments thins month, false if not */
	public function hasRvleft() {
		if($this->getMembershipLevel() != "Free") { // paid and trial have unlimited appointments
			return true;
		} else {
			$query = "doctor_id = ".$this->getId()." AND YEAR(DATE(appointment_date)) = YEAR(CURDATE()) AND MONTH(DATE(appointment_date)) = MONTH(CURDATE())";
			$db = Zend_Registry::get('db');
			$select = $db->select()
				->from('appointments', 'count(*) as amount')
				->where($query);
			$rvs = $db->fetchOne($select);
			if($rvs>3 ) {
				return false;
			} else {
				return true;
			}
		}
	}
}

// end class
?>
