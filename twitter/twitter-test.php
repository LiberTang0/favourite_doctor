<?php
//www.webinfopedia.com
//http://www.webinfopedia.com/auto-tweet-with-oauth-in-php.html


$consumerKey    = 'e7yscx1y6aXLM6fbEzKLnw';
$consumerSecret = 'RweVxhwOmIWAA3rVrofnucwCJbqyRqsGuONd3N3I';
$oAuthToken     = '513425245-rQDjLMxZLO0I68LN9BKu69f1Dd2Rxl6pklXsCVq3';
$oAuthSecret    = 'omYfievhSjbDusLLIHRo5yUZrIB8AZhDwCNAwRGk4qo';

require_once('twitteroauth.php');

$tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
/* 
require_once("db.php");
$sql=mysql_query("select url,title from webinfo_pre_article order by rand()");
$showfetch=mysql_fetch_array($sql); */
$showfetch['title']="test";
$showfetch['url']="lala";
$tweet->post('statuses/update', array('status' => ''.$showfetch['title'].'
http://www.appointment-script.com/'.$showfetch['url'].''));
?>