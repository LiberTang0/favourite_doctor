<?php
class Base_View_Helper_SearchUrl extends Zend_View_Helper_Abstract
{

	public function searchUrl($string, $arrParam=null)
	{
            $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', APPLICATION_ENV);
            if($config->seofriendlyurl=="1")
            {
                $seoUrlM=new Application_Model_SeoUrl();
                $string = $seoUrlM->retrieveSeoUrl($string);
                    
                   // }

                    if(!is_null($arrParam))
                    {
                        $params='';
                        foreach($arrParam as $key=>$val)
                        {
                            $params=$params.$key."=".$val."&";
                        }
                       $params=rtrim($params,'&');
                        return $string."/?".$params;
                    }
                    
              
            }
            return $string;
	}        
}