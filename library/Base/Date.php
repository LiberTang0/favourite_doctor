<?php
class Base_Date extends Zend_Date 
{

	protected $_sysDateFormat;
	
	public function getSysDate($timestamp)
	{
		$timestamp=(int)$timestamp;
		$this->setOptions(array('format_type' => 'php'));
		$this->setTimestamp($timestamp);
		return $this->toString($this->getSysDateFormat());
	}
	
	public function getSysDateFormat()
	{
		if (null === $this->_sysDateFormat)
		{
			$this->setSysDateFormat("M d Y h:i:s a");
		}
		return $this->_sysDateFormat;
	}
	
	public function setSysDateFormat($format)
	{
		$this->_sysDateFormat=$format;
	}
}
?>