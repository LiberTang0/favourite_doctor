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
class Application_Model_Comingsoon {

    /**
     * @var int
     */
    protected $_id;
    protected $_title;
    protected $_memberNumber;
    protected $_category;
    protected $_street;
    protected $_city;
    protected $_billingState;
    protected $_zip;
    protected $_membership;
    protected $_state;
    protected $_addedon;
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

    /**
     * Set entry id
     *
     * @param  int $id
     * @return Application_Model_ArticleCategory
     */
    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    
    public function setTitle($title) {
        $this->_title = (string) $title;
        return $this;
    }
    public function getTitle() {
        return $this->_title;
    }


    public function setMemberNumber($memberNumber) {
        $this->_memberNumber = (string) $memberNumber;
        return $this;
    }

    public function getMemberNumber() {
        return $this->_memberNumber;
    }

    public function setCategory($category) {
        $this->_category = (string) $category;
        return $this;
    }

    public function getCategory() {
        return $this->_category;
    }

    public function setStreet($street) {
        $this->_street = (string) $street;
        return $this;
    }

    public function getStreet() {
        return $this->_street;
    }

    public function setCity($city) {
        $this->_city = (string) $city;
        return $this;
    }

    public function getCity() {
        return $this->_city;
    }

    public function setBillingState($billingState) {
        $this->_billingState = (string) $billingState;
        return $this;
    }

    public function getBillingState() {
        return $this->_billingState;
    }

    public function setZip($zip) {
        $this->_zip = (string) $zip;
        return $this;
    }

    public function getzip() {
        return $this->_zip;
    }

    public function setMembership($membership) {
        $this->_membership = (string) $membership;
        return $this;
    }

    public function getMembership() {
        return $this->_membership;
    }

    public function setState($state) {
        $this->_state = (string) $state;
        return $this;
    }

    public function getState() {
        return $this->_state;
    }
    public function setAddedon($addedon) {
        $this->_addedon = (int) $addedon;
        return $this;
    }

    public function getAddedon() {
        return $this->_addedon;
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
            $this->setMapper(new Application_Model_ComingsoonMapper());
        }
        return $this->_mapper;
    }

    private function setModel($row) {
        $model = new Application_Model_Comingsoon();
        $model->setId($row->id)
                ->setTitle($row->title)
                ->setMemberNumber($row->member_number)
                ->setCategory($row->category)
                ->setStreet($row->street)
                ->setCity($row->city)
                ->setBillingState($row->billing_state)
                ->setZip($row->zip)
                ->setMembership($row->membership)
                ->setState($row->state)
                ->setAddedon($row->addedon)
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
            'title' => $this->getTitle(),
            'member_number' => $this->getMemberNumber(),
            'category' => $this->getCategory(),
            'street' => $this->getStreet(),
            'city' => $this->getCity(),
            'billing_state' => $this->getBillingState(),
            'zip' => $this->getZip(),
            'membership' => $this->getMembership(),
            'state' => $this->getState()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['addedon'] = time();
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
        if (!empty($resultSet)) {
            foreach ($resultSet as $row) {
                $res = $this->setModel($row);
                $entries[] = $res;
            }
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

   
}// end Class

?>