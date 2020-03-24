<?php
require('../vendor/autoload.php');

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

/*
  Print help information
*/
function help() {
  echo __FILE__ . " [hHpwc]" . PHP_EOL;
  echo " -H print this help message" . PHP_EOL;
  echo " -h host of server to query" . PHP_EOL;
  echo " -p port pf server to query" . PHP_EOL;
  echo " -w warning threshold" . PHP_EOL;
  echo " -c critical threshold" . PHP_EOL;
}

$host = "localhost";
$port = 25565;
$warningThreshold = -1;
$criticalThreshold = -1;
for($i=1; $i < $argc; $i++) {
  switch($argv[$i]) {
    case "-H":
      help();
      exit(3);

    break; case "-h":
      $host = $argv[++$i];

    break; case "-p":
      $port = $argv[++$i];

    break; case "-w":
      $warningThreshold = $argv[++$i];

    break; case "-c":
      $criticalThreshold = $argv[++$i];

    break; default:
      echo "Unknown arguemnt " . $argv[$i] . PHP_EOL;
      help();
      exit(3);
    break;
  }
}

if(strlen($host) === 0) {
  echo "the hostname argument (-h) is a required argument." . PHP_EOL;
  exit(3);
}

if($port < 1 || $port > 65535) {
  echo "Invalid port. Must be between 1 and 65535" . PHP_EOL;
  exit(3);
}

if($warningThreshold < 0) {
  echo "Invalid warning threshold. Must be > 0" . PHP_EOL;
  exit(3);
}

if($criticalThreshold < 0) {
  echo "Invalid critical threshold. Must be > 0" . PHP_EOL;
  exit(3);
}

try {
  $ping = new MinecraftPing($host, $port);
  $res = $ping->Query();
} catch(MinecraftPingException $ex) {
  echo "Server Unreachable:" . PHP_EOL;
  print_r($ex->getMessage() . PHP_EOL);
  exit(2);
}

$exit = 0;
$ret = "OK " . $res["players"]["online"] . "/" . $res["players"]["max"] . " players online.";
if($res["players"]["online"] > $warningThreshold) {
  $exit = 1;
  $ret = "WARNING: " . $res["players"]["online"] . "/" . $res["players"]["max"] . " players online.";
}

if($res["players"]["online"] > $criticalThreshold) {
  $exit = 2;
  $ret = "CRITICAL: " . $res["players"]["online"] . "/" . $res["players"]["max"] . " players online.";
}

echo $ret . PHP_EOL;
exit($exit);