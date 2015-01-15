<?php
 ini_set('display_errors', true);

require_once('TwitterAPIExchange.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gethours
 *
 * @author Zeeshan Hadis
 */
class Gethours {
    //put your code here
    
   public static function gettweets()
    {
        
      /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
'oauth_access_token' => "2981876900-gpJhV5uC0QN7cbJ1Smm3jyRZBcU6ZDurtbWKrS5",
'oauth_access_token_secret' => "xs2aOKlVpsQMQpBhiLrqxRpXNOxvqSU9uKFYx4yM41afa",
'consumer_key' => "7HXloiAbZSS5Th45NBiP9ABJj",
'consumer_secret' => "o4ZhAR05nwcUgbKduAhmYZVBIfMvjxx6yNzRVfFsgFFEkGFF1V"
);
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$requestMethod = "GET";
if (isset($_GET['user'])) {$user = $_GET['user'];} else {$user = "93Cummins";}
if (isset($_GET['count'])) {$user = $_GET['count'];} else {$count = 500;}
$getfield = "?screen_name=$user&count=$count";


$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);

if(!empty($string["errors"][0]["message"])) {
    echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";
    exit();
    
}
$value_r=array();
foreach($string as $items)
{
//echo "Time and Date of Tweet: ".$items['created_at']."<br />";

            array_push($value_r,date('H', strtotime($items['created_at'])));
    
}


return $value_r;
       
        
    }

    
    
    
}
