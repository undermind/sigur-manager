<?php
  require_once 'vendor/autoload.php';
  include('config.php');
  $client = Graze\TelnetClient\TelnetClient::factory();
  $promptError = 'ERROR (?<errorNum>[0-9]) (?<errorDesc>.*)';
  $lineEnding = "\r\n";
  $client->connect($dsn, "", $promptError,$lineEnding);
  echo "Login: ";
  print_r($client->execute($auth_string)->getResponseText());
  echo "<br>Object: ";

  $cmd= "GETZONEINFO";
  $result=$client->execute($cmd)->getResponseText();
  $result= str_replace ( "ZONEINFO " , "" , $result );
  $result=explode(", ",$result);
  foreach($result as $z)
  {
    $tmp=explode(" ",$z,4);
//    print_r($tmp);
    $zones[$tmp[1]]= $tmp[3];
  }
//  print_r($zones);

  $id = isset($_GET['id']) ? $_GET['id'] : "10";

  $cmd= "GETOBJECTINFO OBJECTID $id";
  $theobject=$client->execute($cmd)->getResponseText();

  $theobject = substr($theobject, strpos($theobject,'"')+1);
  $theobject = substr($theobject, 0, strpos($theobject,'"')-1);
  print_r($theobject);






  $cmd= "GETLOCATION $id";

  echo "<br/> Command: $cmd <br/>";
  $loc=$client->execute($cmd)->getResponseText();
//  print_r($client->execute($cmd)->getResponseText());
  $aps=explode(" ",$loc);
//  print_r($aps);
  echo "Location: ";
  print_r(substr($zones[$aps[2]],1,-1));
  echo "<br/>Time: ";
  print_r(substr($aps[4]." ".$aps[5],1,-1));


?>