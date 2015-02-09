<?php

/**
 * User table data gateway
 *
 * @uses   Zend_Db_Table_Abstract
 * @author 	Ravinesh
 * @package Eulogy
 * @subpackage Model
 */

class Application_Model_DbTable_ArticleCategory extends Zend_Db_Table_Abstract {
	/**
     * @var string Name of the database table
     */
	protected $_name = 'article_categories';

}
