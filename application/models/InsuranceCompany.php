<?php

class Application_Model_InsuranceCompany {

    protected $_id;
    protected $_company;
    protected $_descripton;
    protected $_metatitle;
    protected $_metadescription;
    protected $_metakeywords;
    protected $_logo;
    protected $_status;
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
            $this->setMapper(new Application_Model_InsuranceCompanyMapper());
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

    public function getCompany() {
        return $this->_company;
    }

    public function setCompany($company) {
        $this->_company = (string) $company;
        return $this;
    }

    public function getDescription() {
        return $this->_descripton;
    }

    public function setDescription($description) {
        $this->_descripton = (string) $description;
        return $this;
    }

    public function getMetatitle() {
        return $this->_metatitle;
    }

    public function setMetatitle($metatitle) {
        $this->_metatitle = (string) $metatitle;
        return $this;
    }

    public function getMetadescription() {
        return $this->_metadescription;
    }

    public function setMetadescription($metadescription) {
        $this->_metadescription = (string) $metadescription;
        return $this;
    }

    public function getMetakeywords() {
        return $this->_metakeywords;
    }

    public function setMetakeywords($metakeywords) {
        $this->_metakeywords = (string) $metakeywords;
        return $this;
    }

    public function getLogo() {
        return $this->_logo;
    }

    public function setLogo($logo) {
        $this->_logo = (string) $logo;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_InsuranceCompany();
        $model->setId($row->id)
                ->setCompany($row->company)
                ->setDescription($row->description)
                ->setMetadescription($row->metadescription)
                ->setMetatitle($row->metatitle)
                ->setMetakeywords($row->metakeywords)
                ->setLogo($row->logo)
                ->setStatus($row->status)
        ;

        return $model;
    }

    public function save() {
        $data = array(
            'company' => $this->getCompany(),
            'description' => $this->getDescription(),
            'metatitle'   => $this->getMetatitle(),
            'metakeywords'   => $this->getMetakeywords(),
            'metadescription'   => $this->getMetadescription(),
            'logo' => $this->getLogo()
        );
        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['status'] = 1;
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['status'] = $this->getStatus();
            $this->getMapper()->getDbTable()->update($data, array('id = ?' => $id));
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

    public function getInsurancecompanies($where=null, $option=array()) {
        $obj = new Application_Model_InsuranceCompany();
        $entries = $obj->fetchAll($where, 'company ASC');
        $arrCompanies = array();
        if (!empty($option)) {
            foreach ($option as $k => $val)
                $arrCompanies[$k] = $val;
        }
        // $arrCompanies['']= '---Select Insurance Companies---';
        foreach ($entries as $entry) {
            $arrCompanies[$entry->getId()] = $entry->getCompany();
        }
        return $arrCompanies;
    }

    public function getInsuranceOther() {
        $db = Zend_Registry::get('db');
        $geocode = "";
        $query = "SELECT id, company FROM insurance_companies WHERE id IN (SELECT DISTINCT insurance_company_id FROM insurance_plans
                WHERE status=1 AND plan_type='g') AND status=1 ORDER BY company ASC";
        $select = $db->query($query);
        $result = $select->fetchAll();
        return $result;
    }

}