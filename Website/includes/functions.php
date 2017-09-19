<?php

require_once 'includes/config.inc.php';


// Class to sort all the emulating part
// Name scheme "crack pipe"

class pipe {

	function userInfo($hwid) {

		// We are setting up our own DB with user details later
		global $db;

		$do = $db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
		$do->bindParam(":hwid", $hwid);
		$do->execute();
		$result = $do->fetch();

		// Making sure this is setup before php freaks out
		$time = date("D M, Y H:i:s", strtotime("now"));
		$userIP = $_SERVER['REMOTE_ADDR'];
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";

		if(empty($result['Lastip'])) {
			$result['Lastip'] = $userIP;
		}


		 // We real big Pringles lovers here, which one is your favorite?
		switch($result['Plan']){

			// Normie Pringles Originals flavour ( ESP only )
			case "1":
				$plan_name = "Originals";
				$result['Plan'] = "3";
				$level = "1";
				break;

			// Hotboy Sour Cream & Onion flavour ( ESP, Aimbot with preidciton, everything you'd want )
			case "2":
				$plan_name = "Sour Cream & Onion";
				$result['Plan'] = "11";
				$level = "2";
				break;

			// idk BBQ flavored pringles?
			default:
				$result['Status'] = NULL;
		}


		if(!$result['Status'] || time() > $result['Expire'] ) {
			
			// This nerd expired, throw him out.
			$do = $db->prepare("UPDATE users SET Status = (:status) WHERE HWID = (:hwid)");
			$do->bindParam(":hwid", $hwid);
			$do->bindParam(":status", NULL);
			$do->execute();

			// b y e  b y e 
			return die("Bruh, this nerd isn't on the list.");
		}

		// Everything seems fine, let's return your account information and log your 
		// login request, make sure you aren't a dirty account sharing cunt.

		if( geoip_asnum_by_name($result['Lastip']) != geoip_asnum_by_name($userIP) ) {

				$Failedip = json_encode(array(
					"Date" => $time,
					"IP Adress" => $userIP,
					"Username" => $result['username'],
					"HWID" => $result['HWID'],
					"User-Agent" => $_SERVER['HTTP_USER_AGENT']
				), JSON_PRETTY_PRINT );

				$do = $db->prepare("UPDATE users SET Failedip = (:Failedip) WHERE HWID = (:hwid)");
				$do->bindParam(":hwid", $hwid);
				$do->bindParam(":Failedip", $Failedip);
				$do->execute();

			return die("Sorry, your IP does not match your previous login, contact dad to fix this.");
		}
		
		$do = $db->prepare("UPDATE users SET Lastlogin = (:lastlogin), Lastip = (:lastip) WHERE HWID = (:hwid)");
		$do->bindParam(":hwid", $hwid);
		$do->bindParam(":lastlogin", $time);
		$do->bindParam(":lastip", $userIP);
		$do->execute();

		return json_encode(array(
				"md5_launcher" => "6",
				"hwid" => $result['HWID'],
				"expire" => $result['Expire'],
				"plan" => $result['Plan'],
				"plan_name" => "[FOSSIL] " . $plan_name,
				"status" => $result['Status'],
				"lvl" => $level
			), JSON_PRETTY_PRINT);
	}

	function update($info){

		// TODO: Make this somewhat automated, idk how yet maybe proxy?

		/*

		$context  = stream_context_set_default(
		  array(
		    'http'=>array(
		    	'proxy' => "tcp://$PROXY_HOST:$PROXY_PORT", // JIRX's proxy
		    	'request_fulluri' => true,
		    	'header' =>'Proxy-Authorization: Basic ' . base64_encode('username'.':'.'userpass')
		    )
		  )
		);

		*/

		switch($info) {
			case "md5":
				return "24ba961e78869bb69dfa18219a387ee2"; // Until jirx fies
				//file_get_contents("https://85.253.210.228/api_info/hardwareid/67a6be7105064ed7bb6933dc40cce4e2/action/md5");
				
				break;

			case "download":
				header("x-accel-redirect: /assets/bins/stream/update6.dll");
				
				// Until jirx fies
				// file_get_contents("https://85.253.210.228/api_info/hardwareid/67a6be7105064ed7bb6933dc40cce4e2/action/download");
				break;

			default:
				echo "Hmm, something seems to be missing here...";
		}
	}

	function config($action, $hwid, $data = NULL ) {
		global $db;

		// Setting up variables for logging
		$time = date("D M, Y H:i:s", strtotime("now"));
		$userIP = $_SERVER['REMOTE_ADDR'];
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";

		$do = $db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
		$do->bindParam(":hwid", $hwid);
		$do->execute();
		$result = $do->fetch();
		
		if(!$result['Status']) {
			return die("yooo bruh this nignog aint on the list, get the bouncers.");
		}

		switch($action) {

			case "load":
				if(empty($result['Config'])){
					return file_get_contents("assets/default.config.json");
				}
				return $result['Config'];		
				break;

			case "save":

				// Let's first verify that we are dealing with JSON here.
				// If not it might be a possible hacking attempting, log data of precautions.

				if(json_decode($data) === NULL){
					
					$FailedConfig = json_encode(array(
						"Date" => $time,
						"IP Adress" => $userIP,
						"HWID" => $result['HWID'],
						"Username" => $result['username'],
						"User-Agent" => $userAgent,
						"Request-Data" => $data
					), JSON_PRETTY_PRINT);

					$do = $db->prepare("UPDATE users SET FailedConfig = (:Failedconfig) WHERE HWID = (:hwid)");
					$do->bindParam(":hwid", $hwid);
					$do->bindParam(":Failedconfig", $FailedConfig);
					$do->execute();

					echo $FailedConfig;
					die("AhAhahaHAhaha no. all your shit is  logged, get out.");
				}

				$do = $db->prepare("UPDATE users SET Config = (:config) WHERE HWID = (:hwid)");
				$do->bindParam(":hwid", $hwid);
				$do->bindParam(":config", $data);
				$do->execute();
				break;

			default:
				echo "Hmm, something seems to be missing here...";

		}

	}
}