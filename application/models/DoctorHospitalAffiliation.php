<?php

class Application_Model_DoctorHospitalAffiliation {

    protected $_id;
    protected $_doctorId;
    protected $_affiliationId;
	protected $_mapper;

    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property specified');
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
            $this->setMapper(new Application_Model_DoctorHospitalAffiliationMapper());
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

    public function getDoctorId() {
        return $this->_doctorId;
    }

    public function setDoctorId($doctorId) {
        $this->_doctorId = (int) $doctorId;
        return $this;
    }

    public function getAffiliationId() {
        return $this->_affiliationId;
    }

    public function setAffiliationId($affiliationId) {
        $this->_affiliationId = (int) $affiliationId;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {

        $model = new Application_Model_DoctorHospitalAffiliation();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setAffiliationId($row->affiliation_id)
               ;

        return $model;
    }

    public function save() {
        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'affiliation_id' => $this->getAffiliationId()
        );

            return $this->getMapper()->getDbTable()->insert($data);

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
	 public function getDoctorHospitalAffiliation($where=null, $option=null) {
        $obj=new Application_Model_DoctorHospitalAffiliation();
        $entries=$obj->fetchAll($where, 'id ASC');
        $arrDoctorAssociation=array();
        if(!is_null($option))
            $arrDoctorAssociation['']=$option;
        foreach($entries as $entry) {
            $arrDoctorAssociation[]=$entry->getAwardId();
        }
        return $arrDoctorAssociation;
    }

    function getMyHospitalAffiliate($where)
    {
         $resultSet = $this->fetchAll($where);
         $model = new Application_Model_HospitalAffiliation();
         
        $entries = array();
        foreach ($resultSet as $row) {

        
        $objHA = $model->find($row->getAffiliationId());
       
            $entries[$row->getAffiliationId()] = $objHA->getName();
			
            unset($objHA);
        }

        return $entries;
    }

	 function getDoctorHospitalAffiliate($where)
    {
         $resultSet = $this->fetchAll($where);
         $model = new Application_Model_HospitalAffiliation();
         
        $entries = array();
        foreach ($resultSet as $row) {

        
        $objHA = $model->find($row->getAffiliationId());
       
            $entries[$row->getAffiliationId()]['name'] = $objHA->getName();
            $entries[$row->getAffiliationId()]['logo'] = $objHA->getLogo();
			
            unset($objHA);
        }

        return $entries;
    }

	
    
}