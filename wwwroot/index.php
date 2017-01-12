<?php

# OpenWebRX Wrapper/Launcher tool
# Enable OpenWebRX to be re-configured remotely, or on an as using basis.
#
# Author: Farran Rebbeck
# Date  : 2017

# Configuration Variables

define ("OPENWEBRX_DIR", "/home/pi/openwebrx/");
define ("PYTHON_DIR", `which python`);

print "<html><head></head><body>\n";
print "<h1>OpenWebRX Launcher</h1>\n";

$orx_status = `ps -ef | grep openwebrx.pyi wc -l`;
$numUsersConnected = `netstat -n | grep 8073 | wc -l`;

print "<div id=\"openwebrx_running_status\">OpenWebRX is currently: ";
if ($orx_status == 0) {
  print "Not running";
}
print "</div>\n";

print "<div id=\"numusers\">Number of users currently connected: ".$numUsersConnected."</div>";

if ($numUsersConnected == 0) {
  print "Please select a configuration to use:<br>";

}

print "</body></html>";

