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

class Application_Model_HospitalAffiliation {

    /**
     * @var int
     */
    protected $_id;
    protected $_name;
    protected $_address;
    protected $_city;
    protected $_state;
    protected $_zipcode;
    protected $_status;
    protected $_phone;
    protected $_mapper;
	protected $_logo;


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
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function getAddress() {
        return $this->_address;
    }

    public function setAddress($address) {
        $this->_address = (string) $address;
        return $this;
    }
    public function getCity() {
        return $this->_city;
    }

    public function setCity($city) {
        $this->_city = (string) $city;
        return $this;
    }
    public function getState() {
        return $this->_state;
    }

    public function setState($state) {
        $this->_state = (string) $state;
        return $this;
    }

    public function setZipcode($zipcode) {
        $this->_zipcode = (string) $zipcode;
        return $this;
    }

    public function getZipcode() {
        return $this->_zipcode;
    }

     public function setPhone($phone) {
        $this->_phone = (string) $phone;
        return $this;
    }

    public function getPhone($phone) {
        
        return $this->_phone;
    }

    
    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }
	
	public function getLogo() {
        return $this->_logo;
    }

    public function setLogo($logo) {
        $this->_logo = (string) $logo;
        return $this;
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
            $this->setMapper(new Application_Model_HospitalAffiliationMapper());
        }
        return $this->_mapper;
    }


    private function setModel($row) {
        $model=new Application_Model_HospitalAffiliation();
        $model->setId($row->id)
                ->setName($row->name)
                ->setAddress($row->address)
                ->setCity($row->city)
                ->setState($row->state)
                ->setZipcode($row->zipcode)
                ->setPhone($row->phone)
				->setLogo($row->logo)
                ->setStatus($row->status);
        return $model;
    }

    /**
     * Save the current entry
     *
     * @return void
     */
    public function save() {

        $data = array(
                'name'   => $this->getName(),
                'address'   => $this->getAddress(),
                'city'   => $this->getCity(),
                'state'   => $this->getState(),
                'zipcode' => $this->getZipcode(),
				'logo' => $this->getLogo(),
                'phone' => $this->getPhone()
                
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
        $res=$this->setModel($row);

        return $res;
    }


    /**
     * Fetch all entries
     *
     * @return array
     */
    public function fetchAll($where=null, $order=null, $count=null, $offset=null) {
        $resultSet = $this->getMapper()->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row) {
            $res=$this->setModel($row);
            $entries[] = $res;
        }
        return $entries;

    }

    public function fetchRow($where=null, $order=null) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);

        if(!empty($row)) {
            $res=$this->setModel($row);
            return $res;
        }
        else {
            return false;
        }

    }


    public function delete($where) {
        return $this->getMapper()->getDbTable()->delete($where);
    }

    public function getAllAffiliation($where=null, $option=null) {
        $obj=new Application_Model_HospitalAffiliation();
        $entries=$obj->fetchAll($where, "name");
        $arrCountry=array();
        if(!is_null($option))
            $arrCountry['']=$option;
       
        foreach($entries as $entry) {
            $arrCountry[$entry->getId()]=$entry->getName();
        }
        return $arrCountry;
    }

    public function GetAllStates()
    {
        $db = Zend_Registry::get('db');
        $geocode = "";
        $query = "SELECT distinct(state) as state FROM  `hospital_affiliations`";
        $select = $db->query($query);
        $result = $select->fetchAll();
        $entries = array();
        $entries['']="Choose State";
        foreach ($result as $row) {

            $entries[$row->state] = $row->state;
        }

        return $entries;
    }
}

?>