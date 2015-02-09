<?php

/**
 * User table data gateway
 *
 * @uses   Zend_Db_Table_Abstract
 * @author 	Avadhesh
 * @package Eulogy
 * @subpackage Model
 */

class Application_Model_DbTable_PatientComment extends Zend_Db_Table_Abstract {
	/**
     * @var string Name of the database table
     */
	protected $_name = 'patient_comments';

}
