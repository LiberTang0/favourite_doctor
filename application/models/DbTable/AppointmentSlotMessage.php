<?php

/**
 * Article table data gateway
 *
 * @uses   Zend_Db_Table_Abstract
 * @author 
 * @package QuickStart
 * @subpackage Model
 */

class Application_Model_DbTable_AppointmentSlotMessage extends Zend_Db_Table_Abstract {
	/**
     * @var string Name of the database table
     */
	protected $_name = 'appointment_slot_message';
}
