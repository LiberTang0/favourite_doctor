<?php

class Application_Model_DoctorExtraCategory {

    protected $_id;
    protected $_doctorId;
    protected $_categoryId;
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
            $this->setMapper(new Application_Model_DoctorExtraCategoryMapper());
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

    public function getCategoryId() {
        return $this->_categoryId;
    }

    public function setCategoryId($categoryId) {
        $this->_categoryId = (int) $categoryId;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_DoctorExtraCategory();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setCategoryId($row->category_id)
        ;

        return $model;
    }

    public function save() {
        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'category_id' => $this->getCategoryId()
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

    public function getDoctorCategories($where=null, $option=null, $onlystring=null) {
        
        $entries = $this->fetchAll($where);
        $arrDoctorCategory = array();
        $modCat = new Application_Model_Category();
        if (!is_null($option))
            $arrDoctorCategory[''] = $option;
        foreach ($entries as $entry) {
            $objassoc = $modCat->find($entry->getCategoryId());
            if($objassoc)$arrDoctorCategory[$entry->getCategoryId()] = $objassoc->getName();
        }
        if (is_null($onlystring))
            return $arrDoctorCategory;
        else {
            $arr_keys = array_keys($arrDoctorCategory);
            $str_associationid = implode(",", $arr_keys);
            return $str_associationid;
        }
    }

}