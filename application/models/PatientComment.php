<?php
/**
 * User model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single
 * user entry.
 *
 * @uses       Application_Model_PatientComment
 * @package    Directory
 * @subpackage Model
 */

class Application_Model_PatientComment {

    /**
     * @var int
     */
    protected $_id;
    protected $_comment;
    protected $_patientId;
    protected $_doctorId;
    protected $_status;
	protected $_commentDate;
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
     * @return Directory_Model_DirectoryPatientComment
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
     * @return Application_Model_PatientComment
     */
    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

   
    public function setComment($comment) {
        $this->_comment = (string) $comment;
        return $this;
    }

     public function getComment() {
        return $this->_comment;
    }

    public function getPatientId() {
        return $this->_patientId;
    }

    public function setPatientId($patientId) {
        $this->_patientId = (string) $patientId;
        return $this;
    }
    public function getDoctorId() {
        return $this->_doctorId;
    }

    public function setDoctorId($doctorId) {
        $this->_doctorId = (int) $doctorId;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    /**
     * Get data mapper
     *
     * Lazy loads Directory_Model_DirectoryPatientCommentMapper instance if no mapper registered.
     *
     * @return Directory_Model_DirectoryPatientComment
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Application_Model_PatientCommentMapper());
        }
        return $this->_mapper;
    }


    private function setModel($row) {
        $model=new Application_Model_PatientComment();
        $model->setId($row->id)
                ->setComment($row->comment)
                ->setPatientId($row->patientId)
                ->setDoctorId($row->doctorId)
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
                'comment'   => stripslashes($this->getComment()),
                'patientId'   => $this->getPatientId(),
				'doctorId'   => $this->getDoctorId(),
				'commentDate'   => time()
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

   
}

?>