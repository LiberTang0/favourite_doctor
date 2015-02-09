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
class Application_Model_Appointment {

    /**
     * @var int
     */
    protected $_id;
    protected $_referenceCode;
    protected $_userId;
    protected $_fname;
    protected $_lname;
    protected $_zipcode;
    protected $_phone;
    protected $_email;
    protected $_age;
    protected $_gender;
    protected $_firstVisit;
    protected $_patientStatus;
    protected $_notes;
    protected $_appointmentDate;
    protected $_appointmentTime;
    protected $_approve;
    protected $_bookingDate;
    protected $_doctorId;
    protected $_reasonForVisit;
    protected $_needs;
    protected $_insurance;
    protected $_plan;
    protected $_appointmentType;
    protected $_updateDate;
    protected $_monthDob;
    protected $_dateDob;
    protected $_yearDob;
    protected $_mailCounterForDoctor;
    protected $_mapper;
    protected $_cancelledBy;
    protected $_calledStatus;
    protected $_deleted;

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
            throw new Exception('The variable is not valid');
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
            throw new Exception('The variable is not valid');
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

    public function getMailCounterForDoctor() {
        return $this->_mailCounterForDoctor;
    }

    public function setMailCounterForDoctor($id) {
        $this->_mailCounterForDoctor = (int) $id;
        return $this;
    }

    public function setReferenceCode($referenceCode) {
        $this->_referenceCode = (string) $referenceCode;
        return $this;
    }

    public function getReferenceCode() {
        return $this->_referenceCode;
    }

    public function setUserId($userId) {
        $this->_userId = (int) $userId;
        return $this;
    }

    public function getUserId() {
        return $this->_userId;
    }

    public function setFname($fname) {
        $this->_fname = (string) $fname;
        return $this;
    }

    public function getFname() {
        return $this->_fname;
    }

    public function setLname($lname) {
        $this->_lname = (string) $lname;
        return $this;
    }

    public function getLname() {
        return $this->_lname;
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

    public function getPhone() {
        return $this->_phone;
    }

    public function setEmail($email) {
        $this->_email = (string) $email;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setAge($age) {
        $this->_age = (int) $age;
        return $this;
    }

    public function getAge() {
        return $this->_age;
    }

    public function setGender($gender) {
        $this->_gender = (string) $gender;
        return $this;
    }

    public function getGender() {
        return $this->_gender;
    }

    public function setFirstVisit($firstVisit) {
        $this->_firstVisit = (int) $firstVisit;
        return $this;
    }

    public function getFirstVisit() {
        return $this->_firstVisit;
    }

    public function setPatientStatus($patientStatus) {
        $this->_patientStatus = (string) $patientStatus;
        return $this;
    }

    public function getPatientStatus() {
        return $this->_patientStatus;
    }

    public function setNotes($notes) {
        $this->_notes = (string) $notes;
        return $this;
    }

    public function getNotes() {
        return $this->_notes;
    }

    public function setAppointmentDate($appointmentDate) {
        $this->_appointmentDate = (string) $appointmentDate;
        return $this;
    }

    public function getAppointmentDate() {
        return $this->_appointmentDate;
    }

    public function setAppointmentTime($appointmentTime) {
        $this->_appointmentTime = (string) $appointmentTime;
        return $this;
    }

    public function getAppointmentTime() {
        return $this->_appointmentTime;
    }

    public function setApprove($approve) {
        $this->_approve = (string) $approve;
        return $this;
    }

    public function getApprove() {
        return $this->_approve;
    }

    public function setBookingDate($bookingDate) {
        $this->_bookingDate = (int) $bookingDate;
        return $this;
    }

    public function getBookingDate() {
        return $this->_bookingDate;
    }

    public function setDoctorId($doctorId) {
        $this->_doctorId = (int) $doctorId;
        return $this;
    }

    public function getDoctorId() {
        return $this->_doctorId;
    }

    public function setReasonForVisit($reasonForVisit) {
        $this->_reasonForVisit = (int) $reasonForVisit;
        return $this;
    }

    public function getReasonForVisit() {
        return $this->_reasonForVisit;
    }

    public function setNeeds($needs) {
        $this->_needs = (string) $needs;
        return $this;
    }

    public function getNeeds() {
        return $this->_needs;
    }

    public function setInsurance($insurance) {
        $this->_insurance = (int) $insurance;
        return $this;
    }

    public function getInsurance() {
        return $this->_insurance;
    }

    public function setPlan($plan) {
        $this->_plan = (int) $plan;
        return $this;
    }

    public function getPlan() {
        return $this->_plan;
    }

    public function setAppointmentType($appointmentType) {
        $this->_appointmentType = (int) $appointmentType;
        return $this;
    }

    public function getAppointmentType() {
        return $this->_appointmentType;
    }

    public function setUpdateDate($updateDate) {
        $this->_updateDate = (int) $updateDate;
        return $this;
    }

    public function getUpdateDate() {
        return $this->_updateDate;
    }

    public function setMonthDob($month) {
        $this->_monthDob = (int) $month;
        return $this;
    }

    public function getMonthDob() {
        return $this->_monthDob;
    }

    public function setDateDob($date) {
        $this->_dateDob = (int) $date;
        return $this;
    }

    public function getDateDob() {
        return $this->_dateDob;
    }

    public function setYearDob($year) {
        $this->_yearDob = (int) $year;
        return $this;
    }

    public function getYearDob() {
        return $this->_yearDob;
    }

    public function setCancelledBy($cancelledBy) {
        $this->_cancelledBy = $cancelledBy;
        return $this;
    }

    public function getCancelledBy() {
        return $this->_cancelledBy;
    }

    public function setCalledStatus($calledStatus) {
        $this->_calledStatus = $calledStatus;
        return $this;
    }

    public function getCalledStatus() {
        return $this->_calledStatus;
    }

    public function setDeleted($deleted) {
        $this->_deleted = $deleted;
        return $this;
    }

    public function getDeleted() {
        return $this->_deleted;
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
            $this->setMapper(new Application_Model_AppointmentMapper());
        }
        return $this->_mapper;
    }

    private function setModel($row) {
        $model = new Application_Model_Appointment();
        $model->setId($row->id)
                ->setReferenceCode($row->reference_code)
                ->setUserId($row->user_id)
                ->setFname($row->fname)
                ->setLname($row->lname)
                ->setZipcode($row->zipcode)
                ->setPhone($row->phone)
                ->setEmail($row->email)
                ->setAge($row->age)
                ->setGender($row->gender)
                ->setFirstVisit($row->first_visit)
                ->setPatientStatus($row->patient_status)
                ->setNotes($row->notes)
                ->setAppointmentDate($row->appointment_date)
                ->setAppointmentTime($row->appointment_time)
                ->setApprove($row->approve)
                ->setBookingDate($row->booking_date)
                ->setDoctorId($row->doctor_id)
                ->setReasonForVisit($row->reason_for_visit)
                ->setNeeds($row->needs)
                ->setInsurance($row->insurance)
                ->setPlan($row->plan)
                ->setAppointmentType($row->appointment_type)
                ->setUpdateDate($row->update_date)
                ->setMonthDob($row->month_dob)
                ->setDateDob($row->date_dob)
                ->setYearDob($row->year_dob)
                ->setMailCounterForDoctor($row->mail_counter_for_doctor)
                ->setCancelledBy($row->cancelled_by)
                ->setcalledStatus($row->called_status)
                ->setDeleted($row->deleted)
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
            'reference_code' => $this->getReferenceCode(),
            'user_id' => $this->getUserId(),
            'fname' => $this->getFname(),
            'lname' => $this->getLname(),
            'zipcode' => $this->getZipcode(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'age' => $this->getAge(),
            'gender' => $this->getGender(),
            'first_visit' => $this->getFirstVisit(),
            'patient_status' => $this->getPatientStatus(),
            'notes' => $this->getNotes(),
            'appointment_date' => $this->getAppointmentDate(),
            'appointment_time' => $this->getAppointmentTime(),
            'booking_date' => $this->getBookingDate(),
            'doctor_id' => $this->getDoctorId(),
            'reason_for_visit' => $this->getReasonForVisit(),
            'needs' => $this->getNeeds(),
            'insurance' => $this->getInsurance(),
            'plan' => $this->getPlan(),
            'appointment_type' => $this->getAppointmentType(),
            'month_dob' => $this->getMonthDob(),
            'date_dob' => $this->getDateDob(),
            'year_dob' => $this->getYearDob(),
            'mail_counter_for_doctor' => $this->getMailCounterForDoctor(),
            'cancelled_by' => $this->getCancelledBy(),
            'called_status' => $this->getCalledStatus()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['approve'] = 0;
            $data['deleted'] = 0;
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['approve'] = $this->getApprove();
            $data['deleted'] = $this->getDeleted();
            $data['update_date'] = time();
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

    public function getAppointmentStatus($where=null, $order=null) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);
        $return = "";
        if (!empty($row)) {
            switch ($row->approve) {
                case '-1':
                    $return = "not approved yet";
                    break;
                case '0':
                case '3':
                    $return = "new appointment";
                    break;
                case '1':
                    $return = "confirmed";
                    break;
                case '2':
                    $return = "cancelled";
                    break;
            }
            return $return;
        }
        else
            return false;
    }

    public function getNewAppointmentStatus($where=null, $order=null) {
        $row = $this->fetchRow($where, $order);
        $return = "";
        if (!empty($row)) {
            switch ($row->getApprove()) {
                case '-1':
                    $return = "Not approved";
                    break;
                case '0':
                case '3':
                    $return = "waiting approval";
                    break;
                case '1':
                    $return = "approved";
                    break;
                case '2':
                    if ($row->getCancelledBy() == 3)
                        $return = "Cancelled by patient";
                    else
                        $return="Cancelled";

                    break;
            }
            return $return;
        }
        else
            return "";
    }

    public function getFullGender($where=null, $order=null) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);
        if (!empty($row)) {
            switch ($row->gender) {
                case 'm':
                    $return = "Male";
                    break;
                case 'f':
                    $return = "Female";
                    break;
            }
            return $return;
        }
        else
            return false;
    }

    public function getFullPatientStatus($where=null, $order=null) {
        $row = $this->getMapper()->getDbTable()->fetchRow($where, $order);
        if (!empty($row)) {
            switch ($row->patient_status) {
                case 'n':
                    $return = "New";
                    break;
                case 'e':
                    $return = "Old";
                    break;
            }
            return $return;
        }
        else
            return false;
    }
    
   
}

?>