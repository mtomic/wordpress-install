<?php

function exc($cmd) {
	if (defined('DEBUG')) { echo "executing: $cmd: "; }
	passthru($cmd,$retcode);
	if ($retcode != 0) { die("ERROR: execution returned non-zero code: $retcode. cmd was:\n$cmd\n"); }
}

function msg($txt) {
	echo "MSG: $txt\n";
}

//#######################################################################
//# Function: Prompt user and get user input, returns value input by user
//#           Or if return pressed returns a default if used e.g usage
//# $name = promptUser("Enter your name");
//# $serverName = promptUser("Enter your server name", "localhost");
//# Note: Returned value requires validation 
//#.......................................................................
function promptUser($promptStr,$defaultVal=false){;

 if($defaultVal) {                          // If a default set
  echo $promptStr. "[". $defaultVal. "] : ";// print prompt and default
 }
 else {                                     // No default set
  echo $promptStr. ": ";                    // print prompt only
 } 
 $name = chop(fgets(STDIN));                // Read input. Remove CR
 if(empty($name)) {                         // No value. Enter was pressed
  return $defaultVal;                       // return default
 }
 else {                                     // Value entered
  return $name;                             // return value
 }
}
//========================================= End promptUser ============