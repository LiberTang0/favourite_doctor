<?php

class Application_Model_DoctorAssistant {

    protected $_id;
    protected $_doctorId;
    protected $_assistantId;
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
            $this->setMapper(new Application_Model_DoctorAssistantMapper());
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

    public function getAssistantId() {
        return $this->_assistantId;
    }

    public function setAssistantId($assistantId) {
        $this->_assistantId = (int) $assistantId;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_DoctorAssistant();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setAssistantId($row->assistant_id)
               ;

        return $model;
    }

    public function save() {
        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'assistant_id' => $this->getAssistantId()
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

    public function getDoctorAssistants($where=null, $option=null) {
        $obj=new Application_Model_DoctorAssistant();
        $entries=$obj->fetchAll($where, 'id ASC');
        $arrDoctorAssistant=array();
        
        if(!is_null($option))
            $arrDoctorAssistant['']=$option;
        foreach($entries as $entry) {
            $arrDoctorAssistant[] = $entry->getAssistantId();
        }
        return $arrDoctorAssistant;
    }
	 public function getDoctorAssistantForDoctorEdit($where=null, $option=null,$onlystring=null) {
        $obj=new Application_Model_DoctorAssistant();
        $entries=$obj->fetchAll($where, 'id ASC');
        $arrDoctorAssistant=array();
        $modAssist = new Application_Model_Assistant();
        if(!is_null($option))
            $arrDoctorAssistant['']=$option;
        foreach($entries as $entry) {
            $objassist = $modAssist->find($entry->getAssistantId());
            if(is_object($objassist))
				$arrDoctorAssistant[$entry->getAssistantId()] = $objassist->getName();
        }
        if(is_null($onlystring))
			return $arrDoctorAssistant;
        else
        {
            $arr_keys = array_keys($arrDoctorAssistant);
            $str_assistantid = implode(",",$arr_keys);
            return $str_assistantid;
        }
    }

	public function getDoctorsByAssistant($assistantId) {
		$obj=new Application_Model_DoctorAssistant();
		$where = "assistant_id = '".$assistantId."'";
        $entries=$obj->fetchAll($where , 'id ASC');
		$doctorsList = array();
		$i=0;
		foreach($entries as $entry) {
			$docId = $entry->getDoctorId();
			$doctorModel = new Application_Model_Doctor();
			$doctor = $doctorModel->find($docId);
			$doctorsList[$i]["id"] = $doctor->getId();
			$doctorsList[$i]["name"] = $doctor->getFname();
			$i++;
		}
		return $doctorsList;
	}
	
}