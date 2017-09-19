<?php

require_once 'includes/config.inc.php';


// Class to sort all the emulating part
class pipe {

	function userInfo($hwid) {

		// We are setting up our own DB with user details later
		global $db;

		$do = $db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
		$do->bindParam(":hwid", $hwid);
		$do->execute();
		$result = $do->fetch();

		//print_r($result);


		 // We real big Pringles lovers here, which one is your favorite?
		switch($result['Plan']){

			// Normie Originals ( ESP only )
			case "1":
				$plan_name = "Originals";
				$result['Plan'] = "3";
				$level = "1";
				break;

			// Hotboy Sour Cream & Onion ( ESP, Aimbot with preidciton, everything you'd want )
			case "2":
				$plan_name = "Sour Cream & Onion";
				$result['Plan'] = "11";
				$level = "2";
				break;

			// idk BBQ flavored pringles?
			default:
				$result['Status'] = "0";
		}


		if(!$result['Status'] || time() > $result['Expire'] ) {
			
			// This nerd expired, throw him out.
			$do = $db->prepare("UPDATE Status FROM users WHERE HWID = (:hwid)");
			$do->bindParam(":hwid", $hwid);
			$do->execute();

			// b y e  b y e 
			return die("yooo bruh this nignog aint on the list, get the bouncers.");
		}

		return json_encode(array(
				"md5_launcher" => "6",
				"hwid" => $result['HWID'],
				"expire" => $result['Expire'],
				"plan" => $result['Plan'],
				"plan_name" => "[FOSSIL] " . $plan_name,
				"status" => $result['Status'],
				"lvl" => $level
			));
	}

	function update($info){

		// TODO: Make this somewhat automated, idk how yet maybe proxy?

		switch($info) {
			case "md5":
				return file_get_contents("http://85.253.210.228/api_info/hardwareid/67a6be7105064ed7bb6933dc40cce4e2/action/md5");
				break;

			case "download":
				return file_get_contents("http://85.253.210.228/api_info/hardwareid/67a6be7105064ed7bb6933dc40cce4e2/action/download");
				break;

			default:
				echo "Hmm, something seems to be missing here...";
		}

	}

	function config($action, $hwid, $data = NULL ) {
		global $db;

		$do = $db->prepare("SELECT Config,Status FROM users WHERE HWID = (:hwid)");
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

				if(json_decode($data) === NULL)
					die("AhAhahaHAhaha no. IP logged get out.");

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