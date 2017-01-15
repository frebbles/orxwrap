<?php

# OpenWebRX Wrapper/Launcher tool
# Enable OpenWebRX to be re-configured remotely, or on an as using basis.
#
# Author: Farran Rebbeck
# Date  : 2017

# Configuration Variables

define ("OPENWEBRX_DIR", "/home/pi/openwebrx/");
define ("PYTHON_DIR", `which python`);
define ("OPENWEBRX_WEB", "/orx/");
define ("OPENWEBRX_PID", "/tmp/orx_web.pid");
define ("OPENWEBRX_LOG", "/tmp/orx.log");
include_once("./functions.php");

$configs = load_orx_config_files();

$orxPid = file_get_contents(OPENWEBRX_PID);
$orxNumProcs = `ps -h $orxPid | wc -l`;
$numUsersConnected = `netstat -n | grep ":8073 " | grep EST | wc -l` /2;

if (isset($_POST['submit'])) {
  foreach ($configs as $n=>$c) {
    if ($_POST['cfgChosen'] == sha1($n)) {
      if ($orxNumProcs > 0) {
        print exec('kill -2 '.$orxPid);
        print exec('killall openwebrx rtl_mus csdr rtl_sdr');
      }
      $wT = time() + 5;
      while (`ps -ef | grep -v 'grep' | egrep "csdr|openwebrx|rtl_sdr" | wc -l` > 0) {
        sleep(1);
        if (time() > $wT) {
          die('Old OPENWEBRX Processes wont die...');
        }
      }
      copy ($n, OPENWEBRX_DIR."config_webrx.py");
      chdir(OPENWEBRX_DIR);
      $cmd = PYTHON_DIR." ".OPENWEBRX_DIR."openwebrx.py";
      exec(sprintf("%s > %s 2>&1 & echo $! > %s", $cmd, OPENWEBRX_LOG, OPENWEBRX_PID));
      sleep(5);
      header("location:".OPENWEBRX_WEB."\n");
      die();
    }
  } 
}

print "<html><head></head><body>\n";
print "<h1>OpenWebRX Launcher</h1>\n";

print "<div id=\"openwebrx_running_status\">OpenWebRX is currently: ";
if ($orxNumProcs <= 1) {
  print "Not running";
} else {
  print "<a href=\"".OPENWEBRX_WEB."\">Running</a>";
}
print "</div>\n";

print "<div id=\"numusers\">Number of users currently connected: ".$numUsersConnected."</div>";


if ($numUsersConnected == 0) {
  print "<div id=\"control\">";
  print "Please select a configuration to use:<br>";
  print "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">";
  print "<table>\n";
  print "<tr><th></th><th>Profile Name</th><th>Center Frequency</th></tr>\n";
  foreach ($configs as $n=>$cfg) {
    print "<tr><td>\n";
    print "<input type=\"radio\" name=\"cfgChosen\" value=\"".sha1($n)."\">\n";
    print "</td><td>\n";
    print $cfg['receiver_name']; 
    print "</td><td>\n";
    $nfr = preg_split("/(\s|\+)/", $cfg['center_freq']);
    $freq = $nfr[0];;
    if ($freq > 1000000) {
      print ($freq/1000000)."MHz";
    } else {
      print ($freq/1000)."KHz";
    }
    if (isset($nfr[1])) {
      print "[Upconverted]";
    }
    print "</td></tr>\n";
  }
  print "</table>\n";
  print "<input type=\"submit\" name=\"submit\" value=\"Launch with profile\">";
  print "</div>\n";
}

print "</body></html>";

