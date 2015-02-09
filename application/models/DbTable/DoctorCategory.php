<?php

/**
 * Article table data gateway
 *
 * @uses   Zend_Db_Table_Abstract
 * @author 	
 * @package QuickStart
 * @subpackage Model
 */

class Application_Model_DbTable_DoctorCategory extends Zend_Db_Table_Abstract {
	/**
     * @var string Name of the database table
     */
	protected $_name = 'doctor_categories';
}
