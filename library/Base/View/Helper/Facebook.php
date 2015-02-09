<?php
class Base_View_Helper_Facebook extends Zend_View_Helper_Abstract
{
	
	public function facebook($params=array())
	{
		if(empty($params))
		{
			$params=array(
			  'appId'  => '102609193129645',
			  'secret' => 'b8ba71904d48df9f719be20a39e87764',
			  'cookie' => true
			);
		}
		
		$facebook = new Base_Facebook_Facebook($params);
		return $facebook;
	}
}