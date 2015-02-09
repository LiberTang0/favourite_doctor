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
class Application_Model_DoctorReview {

    /**
     * @var int
     */
    protected $_id;
    protected $_doctorId;
    protected $_title;
    protected $_review;
    protected $_username;
    protected $_email;
    protected $_ip;
    protected $_addedOn;
    protected $_vote;
    protected $_status;
    protected $_adminApproved;
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

    public function setDoctorId($doctorId) {
        $this->_doctorId = (int) $doctorId;
        return $this;
    }

    public function getDoctorId() {
        return $this->_doctorId;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function setTitle($title) {
        $this->_title = (string) $title;
        return $this;
    }

    public function getReview() {
        return $this->_review;
    }

    public function setReview($review) {
        $this->_review = (string) $review;
        return $this;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setUsername($username) {
        $this->_username = (string) $username;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setEmail($email) {
        $this->_email = (string) $email;
        return $this;
    }

    public function getIp() {
        return $this->_ip;
    }

    public function setIp($ip) {
        $this->_ip = (string) $ip;
        return $this;
    }

    public function getAddedOn() {
        return $this->_addedOn;
    }

    public function setAddedOn($addedOn) {
        $this->_addedOn = $addedOn;
        return $this;
    }

    public function getVote() {
        return $this->_vote;
    }

    public function setVote($vote) {
        $this->_vote = (int) $vote;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        return $this;
    }

    public function getAdminApproved() {
        return $this->_adminApproved;
    }

    public function setAdminApproved($adminApproved) {
        $this->_adminApproved = (int) $adminApproved;
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
            $this->setMapper(new Application_Model_DoctorReviewMapper());
        }
        return $this->_mapper;
    }

    private function setModel($row) {
        $model = new Application_Model_DoctorReview();
        $model->setId($row->id)
                ->setDoctorId($row->doctor_id)
                ->setTitle($row->title)
                ->setReview($row->review)
                ->setUsername($row->username)
                ->setIp($row->ip)
                ->setAddedOn($row->added_on)
                ->setVote($row->vote)
                ->setStatus($row->status)
                ->setAdminApproved($row->admin_approved);

        return $model;
    }

    /**
     * Save the current entry
     *
     * @return void
     */
    public function save() {


        $data = array(
            'doctor_id' => $this->getDoctorId(),
            'title' => $this->getTitle(),
            'review' => $this->getReview(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'ip' => $this->getIp(),
            'added_on' => $this->getAddedOn(),
            'vote' => $this->getVote()
        );
        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['status'] = 0;
            $data['admin_approved'] = 0;
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['status'] = $this->getStatus();
            $data['admin_approved'] = $this->getAdminApproved();
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
        foreach ($resultSet as $row) {
            $res = $this->setModel($row);
            $entries[] = $res;
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

    public function getRatingReviews($drid) {
        $object = $this->fetchAll("doctor_id='{$drid}' AND status=1");
        $total = count($object);
        $return['reviews'] = $total;
        $votes = 0;
        foreach ($object as $obj) {
            $votes = $votes + $obj->getVote();
        }
        $return['votes'] = ($total > 0) ? ($votes / $total) : 0;
        $return['image'] = $this->ratingImage($return['votes']);
        return $return;
    }

    public function getSeoRichSnippetsReview($drid) {
        $return = array();
        if((int)$drid > 0){
            $db = Zend_Registry::get('db');
            $query = "SELECT SUM( vote ) AS votes, `title` , SUM( `status` ) AS
                        voters , review, username, added_on
                        FROM `doctor_review`
                        WHERE `doctor_id` ={$drid}
                        GROUP BY `doctor_id`";
            $select = $db->query($query);
            $docRwObject = $select->fetch();
            $avgRate = 0;
            $rateCount = 0;
            if(!empty($docRwObject)){
                $return['title'] = $docRwObject->title;
                $return['review'] = $docRwObject->review;
                $return['username'] = $docRwObject->username;
                $return['avgRate'] = $docRwObject->votes/$docRwObject->voters;
                $return['rateCount'] = $docRwObject->voters;
                $return['added_on'] = date('Y-m-d H:i:s',$docRwObject->added_on);
            }
        }
        return $return;
    }

    public function ratingImage($vote) {
        if ($vote == 0) {
            return "00-0";
        }
        if ($vote > 0 and $vote <= .5) {
            return "00-5";
        }
        if ($vote > .5 and $vote <= 1) {
            return "01-0";
        }
        if ($vote > 1 and $vote <= 1.5) {
            return "01-5";
        }
        if ($vote > 1.5 and $vote <= 2) {
            return "02-0";
        }
        if ($vote > 2 and $vote <= 2.5) {
            return "02-5";
        }
        if ($vote > 2.5 and $vote <= 3) {
            return "03-0";
        }
        if ($vote > 3 and $vote <= 3.5) {
            return "03-5";
        }
        if ($vote > 3.5 and $vote <= 4) {
            return "04-0";
        }
        if ($vote > 4 and $vote <= 4.5) {
            return "04-5";
        }
        if ($vote > 4.5 and $vote <= 5) {
            return "05-0";
        }
    }

}

?>