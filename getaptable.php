<?php
  require_once 'vendor/autoload.php';
  include('config.php');
  $client = Graze\TelnetClient\TelnetClient::factory();
  $promptError = 'ERROR (?<errorNum>[0-9]) (?<errorDesc>.*)';
  $lineEnding = "\r\n";
  $client->connect($dsn, "", $promptError,$lineEnding);
#  $id = isset($_GET['id']) ?" OBJECTID " . $_GET['id'] : "ALL";
  echo "Login: ";
  print_r($client->execute($auth_string)->getResponseText());


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


  $output = "<TABLE border=1><TR><TH>ID</TH><TH>ACCESS POINT</TH><TH>FROM</TH><TH>TO</TH><TH>STATUS</TH><TH>STATE</TH></TR>";
  $cmd= "GETAPLIST";
//  echo "<br/> Command: $cmd <br/>";
  $result=$client->execute($cmd)->getResponseText();
//  print_r($result);
  $aps=explode(" ",$result);
  unset($aps[0]);
  foreach($aps as $ap)
   {
    $cmd= "GETAPINFO $ap";
//    echo "<br/> Command: $cmd <br/>";
    $result=$client->execute($cmd)->getResponseText();
//    print_r($result);
    $apinfo=explode(" ",$result);unset($apinfo[0]);
//    print_r($apinfo);
    $output .= "<TR>";
    $length = count($apinfo);
    for ($i = 0; $i < $length; $i++) {
        switch ($apinfo[$i])
          {
            case "ID": $output .= "<TD>".$apinfo[$i+1]."</TD>"; break;
            case "NAME": $output .= "<TD>".$apinfo[$i+1]."</TD>"; break;
            case "ZONEA": $output .= "<TD>".$zones[$apinfo[$i+1]]."</TD>"; break;
            case "ZONEB": $output .= "<TD>".$zones[$apinfo[$i+1]]."</TD>"; break;
            case "STATE": $output .= "<TD>".$apinfo[$i+1]."</TD><TD>".$apinfo[$i+2]."</TD>"; break;
          }
    }
    $output .= "</TR>";
   }
    $output .= "</TABLE>";
print_r($output);
?>