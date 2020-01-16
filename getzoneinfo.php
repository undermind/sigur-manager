<?php
  require_once 'vendor/autoload.php';
  include('config.php');
  $client = Graze\TelnetClient\TelnetClient::factory();
  $promptError = 'ERROR (?<errorNum>[0-9]) (?<errorDesc>.*)';
  $lineEnding = "\r\n";
  $client->connect($dsn, "", $promptError,$lineEnding);
#  $id = isset($_GET['id']) ?" OBJECTID " . $_GET['id'] : "ALL";
  $cmd= "GETZONEINFO";
  echo "Login: ";
  print_r($client->execute($auth_string)->getResponseText());
  echo "<br/> Command: $cmd <br/>";
  print_r($client->execute($cmd)->getResponseText());

?>