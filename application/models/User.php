<?php

class Application_Model_User {

    protected $_id;
    protected $_firstName;
    protected $_lastName;
    protected $_email;
    protected $_username;
    protected $_password;
    protected $_userLevelId;
    protected $_sendEmail;
    protected $_registerDate;
    protected $_lastVisitDate;
    protected $_lastUpdated;
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
            $this->setMapper(new Application_Model_UserMapper());
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

    public function getFirstName() {
        return $this->_firstName;
    }

    public function setFirstName($firstName) {
        $this->_firstName = (string) $firstName;
        return $this;
    }

    public function getLastName() {
        return $this->_lastName;
    }

    public function setLastName($lastName) {
        $this->_lastName = (string) $lastName;
        return $this;
    }

    public function setEmail($email) {
        $this->_email = (string) $email;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setUsername($username) {
        $this->_username = (string) $username;
        return $this;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setPassword($password) {
        $this->_password = (string) $password;
        return $this;
    }

    public function getUserLevelId() {
        return $this->_userLevelId;
    }

    public function setUserLevelId($userLevelId) {
        $this->_userLevelId = (int) $userLevelId;
        return $this;
    }

    public function getSendEmail() {
        return $this->_sendEmail;
    }

    public function setSendEmail($sendEmail) {
        $this->_sendEmail = (int) $sendEmail;
        return $this;
    }

    public function getRegisterDate() {
        return $this->_registerDate;
    }

    public function setRegisterDate($registerDate) {
        $this->_registerDate = (int) $registerDate;
        return $this;
    }

    public function getLastVisitDate() {
        return $this->_lastVisitDate;
    }

    public function setLastVisitDate($lastVisitDate) {
        $this->_lastVisitDate = (int) $lastVisitDate;
        return $this;
    }

    public function getLastUpdated() {
        return $this->_lastUpdated;
    }

    public function setLastUpdated($lastUpdated) {
        $this->_lastUpdated = (int) $lastUpdated;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (string) $status;
        return $this;
    }

    /* ----Data Manupulation functions ---- */

    private function setModel($row) {
        $model = new Application_Model_User();
        $model->setId($row->id)
                ->setFirstName($row->first_name)
                ->setLastName($row->last_name)
                ->setEmail($row->email)
                ->setUsername($row->username)
                ->setPassword($row->password)
                ->setUserLevelId($row->user_level_id)
                ->setSendEmail($row->send_email)
                ->setRegisterDate($row->register_date)
                ->setLastVisitDate($row->last_visit_date)
                ->setLastUpdated($row->last_updated)
                ->setStatus($row->status)
                ->setUserLevelId($row->user_level_id)
        ;

        return $model;
    }

    public function save() {
        $data = array(
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'user_level_id' => $this->getUserLevelId(),
            'send_email' => $this->getSendEmail(),
            'last_visit_date' => $this->getLastVisitDate(),
            'status' => $this->getStatus()
        );

        if (null === ($id = $this->getId())) {
            unset($data['id']);
            $data['register_date'] = time();
            $data['last_updated'] = time();
            return $this->getMapper()->getDbTable()->insert($data);
        } else {
            $data['last_updated'] = time();
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

    public function isExist($where) {
        $res = $this->fetchRow($where);

        if ($res === false) {
            return false;
        } else {
            return true;
        }
    }

    /* ----Data Manupulation functions ---- */

    public function getDataByUsername($username) {
        $user = new Application_Model_User();
        $user = $user->fetchRow("username='{$username}'");
        return $user;
    }

    public function getCountryName() {
        $country = new Application_Model_Country();
        $country = $country->find($this->getCountryPassport());
        if (false !== $country)
            return $country->getName();
        else
            return false;
    }

    public function setDefaultPermissions($user_id) {
        $permissionM = new Application_Model_Permission();
        $permissions = $permissionM->fetchAll();
        if (count($permissions) > 0) {
            /* -- add default user friend group -- */
            /* $friend_group=new Application_Model_FriendGroup();
              $defaultG=$friend_group->getDefaultFriendGroups();
              $friend_group->setName($defaultG[0]);
              $friend_group->setUserId($user_id);
              $friend_group_id=$friend_group->save(); */
            /* ----------------------------- */


            foreach ($permissions as $_permission) {
                $userPermission = new Application_Model_UserPermission();
                $userPermission->setPermissionId($_permission->getId());
                $userPermission->setFriendGroupId(1);
                $userPermission->setUserId($user_id);
                $userPermission->save();
            }

            /* --- set all default friend group ---- */
            /* for($i=1;$i<count($defaultG);$i++)
              {
              $friend_group->setName($defaultG[$i]);
              $friend_group->setUserId($user_id);
              $friend_group->save();
              } */
            /* -------------------------------------- */
        }
    }

    public function listAllMonths()
    {
        $arMonths = array(
            ''=>'Month',
			'1'=>'January',
            '2'=>'February',
            '3'=>'March',
            '4'=>'April',
            '5'=>'May',
            '6'=>'June',
            '7'=>'July',
            '8'=>'August',
            '9'=>'September',
            '10'=>'October',
            '11'=>'November',
            '12'=>'December'
        );
      return $arMonths;
    }

    public function listAllDates()
    {
        $arrDates = array(''=>'Day');

        for($i=1;$i<=31;$i++)
        {
          $arrDates[$i]=$i;
        }
        return $arrDates;
    }

    public function listAllYear()
    {
        $current_year = date('Y');
        $arrYear = array(''=>'Year');
        $last_year = $current_year-100;
        for($i=$current_year;$i>$last_year;$i--)
        {
            $arrYear[$i]=$i;
        }
        return $arrYear;
    }
    public function getAge($array){
        $year_diff  = date("Y") - $array['year'];
        $month_diff = date("m") - $array['month'];
        $day_diff   = date("d") - $array['day'];
        if ($day_diff < 0 || $month_diff < 0)
          $year_diff--;
        return $year_diff;
    }
}