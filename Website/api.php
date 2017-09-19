<?php

error_reporting(E_ERROR);

require_once('includes/functions.php');

$crack = new pipe();

$api = $_GET;
switch(true) {

    case $api['HWID']:
    	
    	switch($api['action']) {
    		
    		 // User information
    		case "userinfo":
    			echo $crack->userInfo($api['HWID']);
    			break;

    		 // update checking
    		case "md5":
    			echo $crack->update("md5");
    			break;

    		case "download":
    			echo $crack->update("download");
    			break;

    		// Config managment
    		case "load":
    			echo $crack->config("load", $api['HWID']);
    			break;

    		case "save":
    			echo $crack->config("save", $api['HWID'], $_POST['json']);
    			break;

    		default:
    			echo "404 action not found";
    	}
    	
    	break;

    default:
    	echo "Ah bah ah, you didn't say magic word";
}