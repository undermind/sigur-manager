<?php
  require_once 'vendor/autoload.php';
  include('config.php');


echo '<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
function sayHi() {
  $("#div1").html("ready to serve");
}
    $(document).ready(function(){
	$(\'.actionopen\').click(function(){
	    var id = $(this).attr(\'id\');
	    ajaxOpen(id);
	})

	$(\'.actionlock\').click(function(){
	    var id = $(this).attr(\'id\');
	    ajaxLock(id);
	})
	$(\'.actionunlock\').click(function(){
	    var id = $(this).attr(\'id\');
	    ajaxUnlock(id);
	})
	$(\'.actionreset\').click(function(){
	    var id = $(this).attr(\'id\');
	    ajaxReset(id);
	})

	function ajaxOpen(param){
	    $.get(
		\'openap.php\',
        	{\'id\':param},
		function(data){
		    $("#div1").html(data);setTimeout(sayHi, 2000);
		});
	    }

	function ajaxLock(param){
	    $.get(
		\'setap.php\',
        	{\'id\':param,\'state\':"LOCKED"},
		function(data){
		    $("#div1").html(data);setTimeout(sayHi, 2000);
		});
	    }

	function ajaxUnlock(param){
	    $.get(
		\'setap.php\',
        	{\'id\':param,\'state\':"UNLOCKED"},
		function(data){
		    $("#div1").html(data);setTimeout(sayHi, 2000);
		});
	    }

	function ajaxReset(param){
	    $.get(
		\'setap.php\',
        	{\'id\':param},
		function(data){
		    $("#div1").html(data);setTimeout(sayHi, 2000);
		});
	    }


function sayHi() {
	  $("#div1").html("ready to serve");
	}
    });
</script>
</head>
<body>

<div id="div1"><h2>Let SKUD open the doors</h2></div>';



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


  $output = "<TABLE border=1><TR><TH>ID</TH><TH>ACCESS POINT</TH><TH>FROM</TH><TH>TO</TH><TH>STATUS</TH><TH>STATE</TH><TH>ACTION</TH></TR>";
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
    $apid=-1;
    for ($i = 0; $i < $length; $i++) {
        switch ($apinfo[$i])
          {
            case "ID": $output .= "<TD>".$apinfo[$i+1]."</TD>"; $apid=$apinfo[$i+1]; break;
            case "NAME": $output .= "<TD>".$apinfo[$i+1]."</TD>"; break;
            case "ZONEA": $output .= "<TD>".$zones[$apinfo[$i+1]]."</TD>"; break;
            case "ZONEB": $output .= "<TD>".$zones[$apinfo[$i+1]]."</TD>"; break;
            case "STATE": $output .= "<TD>".$apinfo[$i+1]."</TD><TD>".$apinfo[$i+2]."</TD>"; break;
          }
    }
#    $output .= "<TD><button id='b".$apid.
#"' onclick='{ $.ajax({url: \"openap.php?id=".$apid.
#"\", success: function(result){ $(\"#div1\").html(result);setTimeout(sayHi, 2000);}});}'>Open</button></TD>";
    $output .= "<TD><button id='".$apid."' class='actionopen'>Open</button> <button id='".$apid."' class='actionlock'>Lock</button><button id='".$apid."' class='actionreset'>NORMAL</button><button id='".$apid."' class='actionunlock'>Unlock</button></TD>";
    $output .= "</TR>";
   }
    $output .= "</TABLE>";
print_r($output);
?>