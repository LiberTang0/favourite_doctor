<?php

class Application_Model_MasterTimeslot {

    protected $_id;
    protected $_doctorId;
    protected $_slotDay;
    protected $_isChecked;
    protected $_startTime;
    protected $_endTime;
    protected $_slotInterval;
    protected $_weekNumber;
    protected $_displaySlots;
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
            $this->setMapper(new Application_Model_MasterTimeslotMapper());
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

    public function setDoctorId($doctorid) {
        $this->_doctorId = (int) $doctorid;
        return $this;
    }
    public function getDoctorId() {
        return $this->_doctorId;
    }

    
     public function setSlotDay($slotday) {
        $this->_slotDay = (string) $slotday;
        return $this;
    }
    public function getSlotDay() {
        return $this->_slotDay;
    }

   
    public function setIsChecked($ischecked) {
        $this->_isChecked = (int) $ischecked;
        return $this;
    }
    public function getIsChecked() {
        return $this->_isChecked;
    }

    public function setStartTime($starttime) {
        $this->_startTime = (string) $starttime;
        return $this;
    }
    public function getStartTime() {
        return $this->_startTime;
    }

    
    public function setEndTime($endtime) {
        $this->_endTime = (string) $endtime;
        return $this;
    }
    public function getEndTime() {
        return $this->_endTime;
    }

    
    public function setSlotInterval($slotinterval) {
        $this->_slotInterval = (int) $slotinterval;
        return $this;
    }
    public function getSlotInterval() {
        return $this->_slotInterval;
    }

   
    public function setWeekNumber($weeknumber) {
        $this->_weekNumber = (int) $weeknumber;
        return $this;
    }
    public function getWeekNumber() {
        return $this->_weekNumber;
    }

    public function setDisplaySlots($display_slots) {
        $this->_displaySlots = (string) $display_slots;
        return $this;
    }
    public function getDisplaySlots() {
        return $this->_displaySlots;
    }

    

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_MasterTimeslot();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setSlotDay($row->slot_day)
                ->setIsChecked($row->is_checked)
                ->setStartTime($row->start_time)
                ->setEndTime($row->end_time)
                ->setSlotInterval($row->slot_interval)
                ->setWeekNumber($row->week_number)
                ->setDisplaySlots($row->display_slots)
        ;

        return $model;
    }

    public function save() {
        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'slot_day' => $this->getSlotDay(),
            'is_checked' => $this->getIsChecked(),
            'start_time' => $this->getStartTime(),
            'end_time' => $this->getEndTime(),
            'slot_interval' => $this->getSlotInterval(),
            'week_number' => $this->getWeekNumber(),
            'display_slots' => $this->getDisplaySlots()
        );
        if (null === ($id = $this->getId()) || $id < 1) {
            unset($data['id']);
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
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

}// end class