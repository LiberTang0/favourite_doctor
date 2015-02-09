<?php

class Application_Model_DoctorReasonForVisit {

    protected $_id;
    protected $_doctorId;
    protected $_reasonId;
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
            $this->setMapper(new Application_Model_DoctorReasonForVisitMapper());
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

    public function getReasonId() {
        return $this->_reasonId;
    }

    public function setReasonId($reasonId) {
        $this->_reasonId = (int) $reasonId;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_DoctorReasonForVisit();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setReasonId($row->reason_id)
               ;

        return $model;
    }

    public function save() {
        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'reason_id' => $this->getReasonId()
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

     public function getDoctorReasonForVisit($where=null, $option=null) {
        $obj=new Application_Model_DoctorReasonForVisit();
        
        $entries=$obj->fetchAll($where, 'id ASC');
        $arrDoctorReasonForVisit=array();
        if(!is_null($option))
            $arrDoctorReasonForVisit['']=$option;
        foreach($entries as $entry) {
            

            if($entry)
            $arrDoctorReasonForVisit[]= $entry->getReasonId();
        }
        return $arrDoctorReasonForVisit;
    }

    
	 public function getDoctorReasonForVisitForDoctorEdit($where=null, $option=null,$onlystring=null) {
        $obj=new Application_Model_DoctorReasonForVisit();
        $model =new Application_Model_ReasonForVisit();
        $entries=$obj->fetchAll($where, 'id ASC');
        $arrDoctorReasonForVisit=array();
        if(!is_null($option))
            $arrDoctorReasonForVisit['']=$option;
        foreach($entries as $entry) {
            $objReasonforvisit1 = $model->find($entry->getReasonId());
           
            if($objReasonforvisit1)
            $arrDoctorReasonForVisit[$entry->getReasonId()]= $objReasonforvisit1->getReason();
        }
        if(is_null($onlystring))
        return $arrDoctorReasonForVisit;
        else
        {
           $arr_keys = array_keys($arrDoctorReasonForVisit);
            $str_reason = implode(",",$arr_keys);
            return $str_reason;
        }
    }

}