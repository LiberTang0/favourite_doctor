<?php

/**
 * User model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single
 * user entry.
 *
 * @uses       Application_Model_ArticleCategory
 * @package    Directory
 * @subpackage Model
 */
class Application_Model_Patient {

    /**
     * @var int
     */
    protected $_id;
    protected $_name;
    protected $_userId;
    protected $_zipcode;
    protected $_age;
    protected $_gender;
    protected $_phone;
    protected $_lastUpdated;
    protected $_insuranceCompanyId;
    protected $_insurancePlanId;
    protected $_monthDob;
    protected $_dateDob;
    protected $_yearDob;
    protected $_delStatus;
    
    protected $_mapper;

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

    public function setDelStatus($status) {
        $this->_delStatus = (int) $status;
        return $this;
    }

    public function getDelStatus() {
       return $this->_delStatus;
        
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getZipcode() {
        return $this->_zipcode;
    }

    public function setZipcode($zipcode) {
        $this->_zipcode = (string) $zipcode;
        return $this;
    }

    public function getAge() {
        return $this->_age;
    }

    public function setAge($age) {
        $this->_age = (int) $age;
        return $this;
    }

    public function getGender() {
        return $this->_gender;
    }

    public function setGender($gender) {
        $this->_gender = (string) $gender;
        return $this;
    }

    public function getPhone() {
        return $this->_phone;
    }

    public function setPhone($phone) {
        $this->_phone = (string) $phone;
        return $this;
    }

    public function getLastUpdated() {
        return $this->_lastUpdated;
    }

    public function setLastUpdated($lastupdated) {
        $this->_lastUpdated = (int) $lastupdated;
        return $this;
    }

    public function setInsuranceCompanyId($insurance_company_id)
    {
        $this->_insuranceCompanyId = (int) $insurance_company_id;
        return $this;
    }

    public function getInsuranceCompanyId()
    {
        return $this->_insuranceCompanyId;
    }

    public function setInsurancePlanId($insurance_plan_id)
    {
        $this->_insurancePlanId = (int) $insurance_plan_id;
        return $this;
    }

    public function getInsurancePlanId()
    {
        return $this->_insurancePlanId;
    }

    public function setMonthDob($month)
    {
        $this->_monthDob = (int)$month;
        return $this;
    }

    public function getMonthDob()
    {
       return $this->_monthDob;
    }
    
    public function setDateDob($date)
    {
        $this->_dateDob = (int)$date;
        return $this;
    }

    public function getDateDob()
    {
        return $this->_dateDob;
    }

    public function setYearDob($year)
    {
        $this->_yearDob = (int)$year;
        return $this;
    }
    
    
    public function getYearDob()
    {
        return $this->_yearDob;
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
            $this->setMapper(new Application_Model_PatientMapper());
        }
        return $this->_mapper;
    }

    private function setModel($row) {
        $model = new Application_Model_Patient();
        $model->setId($row->id)
                ->setUserId($row->user_id)
                ->setName($row->name)
                ->setZipcode($row->zipcode)
                ->setAge($row->age)
                ->setGender($row->gender)
                ->setPhone($row->phone)
                ->setLastUpdated($row->last_updated)
                ->setInsuranceCompanyId($row->insurance_company_id)
                ->setInsurancePlanId($row->insurance_plan_id)
                ->setMonthDob($row->month_dob)
                ->setDateDob($row->date_dob)
                ->setYearDob($row->year_dob)
                ->setDelStatus($row->del_status)

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
            'name' => $this->getName(),
            'zipcode' => $this->getZipcode(),
            'age' => $this->getAge(),
            'gender' => $this->getGender(),
            'phone' => $this->getPhone(),
            'last_updated' => time(),
            'insurance_company_id' => $this->getInsuranceCompanyId(),
            'insurance_plan_id' => $this->getInsurancePlanId(),
            'month_dob'=>$this->getMonthDob(),
            'date_dob'=>$this->getDateDob(),
            'year_dob'=>$this->getYearDob(),
            'del_status'=>$this->getDelStatus()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
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

    public function getFullGender($where=null,$order=null)
    {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);
        $return="";
        if(!empty($row))
        {
            switch($row->gender)
            {
                case 'm':
                    $return="Male";
                break;
                case 'f':
                    $return="Female";
                 break;
            }
            return $return;
        }
        else
            return false;
    }

}

?>