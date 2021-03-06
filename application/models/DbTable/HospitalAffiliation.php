<?php

/**
 * Award table data gateway
 *
 * @uses   Zend_Db_Table_Abstract

 * @package QuickStart
 * @subpackage Model
 */

class Application_Model_DbTable_HospitalAffiliation extends Zend_Db_Table_Abstract {
	/**
     * @var string Name of the database table
     */
	protected $_name = 'hospital_affiliations';
}