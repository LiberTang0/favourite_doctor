<?php
class Base_View_Helper_MetaImage extends Zend_View_Helper_Abstract
{
    public function toString()
    {
        parent::toString();
        return $this->getBaseMetaImage();
    }
    public function metaImage(){

        $site_url = Zend_Registry::get('siteurl');
		$db = Zend_Registry::get('db');
		
    	$Seourl = new Application_Model_SeoUrl();

        $records_seo = $Seourl->fetchRow("status=1 AND (seo_url = ".$db->quote($_SERVER['REQUEST_URI'])." OR actual_url = ".$db->quote($_SERVER['REQUEST_URI']).")");
        $return = "<meta property=\"og:image\" content=\"".$site_url."images/fb_like.jpg\"/>";
        if(!empty($records_seo)){
            $t = $records_seo->getActualUrl();
            
        }
        if(isset($t) && $t!='')
        {
        list($string,$id) = explode("/profile/index/id/",$t);
        //echo "string=".$string." and id=".$id;
        //die();
            if(!empty($id) && $id>0)
            {
                //Getting users picuture
                $Doctor = new Application_Model_Doctor();
		$profileobject = $Doctor->fetchRow("status=1 AND id=$id");
                $profileImage = "/images/doctor_image/" . $profileobject->getCompanylogo();
                if (!file_exists(getcwd() . $profileImage) || $profileobject->getCompanylogo()=='')
                {

                }
                else
                {
                    $image_url  = $site_url."images/doctor_image/" . $profileobject->getCompanylogo();
                    $return = "<meta property=\"og:image\" content=\"".$image_url."\"/>";
                }
                
            }
        }
        
        
       return $return;

    }
}
