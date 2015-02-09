<?php

class Base_CronJob {

    public static function run() {
        $db = Zend_Registry::get('db');
        /************* Start :: insert into cache_cities from all_cities if table is empty ***********/
        $query = "SELECT COUNT(*) AS CNT FROM `cache_cities`";
        $select = $db->query($query);
        $rs_city = $select->fetch();
        if($rs_city->CNT < 1){
            $query = "INSERT INTO `cache_cities` (SELECT id, city FROM `all_cities`)";
            $insert = $db->query($query);
        }
        /************* End ***********/

    }

}

?>