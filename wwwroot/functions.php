<?php
# 
# Global operating system functions
#
# Author: Farran Rebbeck
#

function load_orx_config_files() {
  # Find config files
  $config_orx_files = glob(OPENWEBRX_DIR.'config_webrx.py-*');
  $config_own_files = glob("../openwebrx-configs/config_webrx.py-*");
  $config_files = array_merge($config_orx_files, $config_own_files);
  foreach ($config_files as $c) {
    $fTest = `file $c`;
    if (stristr($fTest, "text") !== false) {
      $fArr = file($c);
      $fV = preg_grep("/(^receiver_name|^center_freq)/",$fArr);
      foreach ($fV as $v) {
        list($n,$v) = preg_split("/=/", $v);
        $v = str_replace("\"", "", $v);
        $configs[$c][trim($n)] = trim($v);
      }
    }
  }
  return $configs;
}

