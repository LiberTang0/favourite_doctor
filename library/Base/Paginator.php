<?php

class Base_Paginator extends Zend_Paginator {

    private $_total_count;

    public function __construct() {

    }

    public function fetchPageData($model, $page, $countPerPage=10, $where=null, $order=null,$count=null,$offset=null) {
        $zend = new Zend_Db_Table();
       
        $result = $model->getMapper()->getDbTable()->fetchAll($where, $order,$count,$offset);
                
        $this->setTotalCount(count($result));
        $paginator = Base_Paginator::factory($result);
        $paginator->setItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }
    public function arrayPaginator($array, $page, $countPerPage=10) {
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($array));
        $paginator->setCurrentPageNumber($page);

        $this->setTotalCount(count($array));
        $paginator->setItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }

    public function fetchPageDataRaw($sql, $page, $countPerPage=10) {
        $db = Zend_Registry::get('db');
        $result = $db->fetchAll($sql);
        $this->setTotalCount(count($result));
        $paginator = Base_Paginator::factory($result);
        $paginator->setItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }

     public function DbSelectPaginator($select, $page, $countPerPage=10) {
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paginator->setItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }


    public function fetchNumRows() {
   

        $db = Zend_Registry::get('db');
        $select = $db->select()
             ->from(array('d' => 'doctors'),
                    array('total' => 'count(id)'));
       
       
       $result_row = $db->fetchRow($select);
       
       return $result_row->total;

      

    }

    
    public function fetchPageDataResult($result, $page, $countPerPage=10) {
        $this->setTotalCount(count($result));
        $paginator = Base_Paginator::factory($result);
        $paginator->setItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    public function getTotalCount() {
        return $this->_total_count;
    }
   
    public function setTotalCount($total_count) {
        $this->_total_count = $total_count;
    }

}
