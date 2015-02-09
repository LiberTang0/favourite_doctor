<?php
/**
 * User model
 *
 * Utilizes the Data Mapper pattern to persist data. Represents a single
 * user entry.
 *
 * @uses       Application_Model_Category
 * @package    Directory
 * @subpackage Model
 */

class Application_Model_Category {

    /**
     * @var int
     */
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_metatitle;
    protected $_metadescription;
    protected $_metakeywords;
    protected $_parentId;
    protected $_status;

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

   
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

     public function getName() {
        return $this->_name;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setDescription($description) {
        $this->_description = (string) $description;
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
    public function getParentId() {
        return $this->_parentId;
    }

    public function setParentId($parentid) {
        $this->_parentId = (int) $parentid;
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
     * Lazy loads Directory_Model_DirectoryCategoryMapper instance if no mapper registered.
     *
     * @return Directory_Model_DirectoryCategory
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Application_Model_CategoryMapper());
        }
        return $this->_mapper;
    }


    private function setModel($row) {
        $model=new Application_Model_Category();
        $model->setId($row->id)
                ->setName($row->name)
                ->setDescription($row->description)
                ->setMetadescription($row->metadescription)
                ->setMetatitle($row->metatitle)
                ->setMetakeywords($row->metakeywords)
                ->setParentId($row->parent_id)
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
                'name'   => $this->getName(),
                'description'   => $this->getDescription(),
                'metatitle'   => $this->getMetatitle(),
                'metakeywords'   => $this->getMetakeywords(),
                'metadescription'   => $this->getMetadescription()
        );
        $data['parent_id'] = 0; // by default it is 0, becouse right now there is no use of parent id in this application
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

    public function getCategories($where=null, $option=null,$forall=null) {
        $obj=new Application_Model_Category();
        $entries=$obj->fetchAll($where, 'name ASC');
        $arrCountry=array();
        if(!is_null($option))
            $arrCountry['']=$option;
       
        if(!is_null($forall))
        $arrCountry[-1]= 'All Speciality';
        foreach($entries as $entry) {
            $arrCountry[$entry->getId()]=$entry->getName();
        }
        return $arrCountry;
    }
}

?>