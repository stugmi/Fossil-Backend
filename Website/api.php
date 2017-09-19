<?php

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
    			header('Content-Description: File Transfer');
    			header('Content-Type: application/octet-stream');
    			header('Content-Transfer-Encoding: binary');

    			echo $crack->update("download");
    			break;

    		// Config managment
    		case "load":
    			echo $crack->config("load", $api['HWID']);
    			break;

    		case "save":
    			//die(var_dump($_FILES['json']));
    			echo $crack->config("save", $api['HWID'], $_POST['json']);
    			break;

    		default:
    			echo "Fuck you nigger";
    	}
    	
    	break;

    default:
    	echo "Ah bah ah, you didn't say magic word";
}