<?hh

/**
 * Class to handle almost if not everything regarding users of this service
 *
 * @author h
 * @version 0.8
 * @abstract
 * @copyright never
 */

class fossil {

  private $db;
  public function __construct() {

    // Make use of __DIR__ to make sure we are always getting the correct
    // directory for include.
    include_once (__DIR__.'/../config.inc.php');
    try {
      $this->db = new PDO(HTP_DB, HTP_USER, HTP_PASS);
    } catch (PDOException $e) {
      die('Connection failed: '.$e->getMessage());
    }
  }

  /**
   * Used for login if we ever need it. Panel is in work.
   * @param string $username (optional)
   * @param string $password (optional)
   * @return string
   */
  public function userLogin(string $username, string $password): void {

    $do =
      $this->db->prepare("SELECT * FROM users WHERE Username = (:username)");
    $do->bindParam(":username", $username);
    $do->execute();
    $result = $do->fetch();

    if (empty($result['Username']) || empty($result['Password'])){
      header("HTTP/1.1 302 Moved Temporarily");
      header("Location: ../../../SQU/index.php#fail");
    }

    if (password_verify($password, $result['Password'])) {
      $_SESSION['loggedIn'] = TRUE;
      $_SESSION['username'] = $username;
      $_SESSION['hwid'] = $result['HWID'];
      header("HTTP/1.1 302 Moved Temporarily");
      header("Location: ../../../SQU/");
    } else {
      header("HTTP/1.1 302 Moved Temporarily");
      header("Location: ../../../SQU/index.php#fail");
    }

  }

  public function userLogout(): void {
    session_destroy();
    header("HTTP/1.1 302 Moved Temporarily");
    header("Location: ../../../SQU/");
  }

  /**
   * Validate a user's HWID, if active return level of access.
   * @param string $hwid
   * @return mixed
   */

  public function userInfo(string $hwid): string {

    $do = $this->db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
    $do->bindParam(":hwid", $hwid);
    $do->execute();
    $result = $do->fetch();

    // Making sure this is setup before php freaks out
    $time = date("D M, Y H:i:s", strtotime("now"));
    $userIP = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";
    $plan_name = "";
    $level = "";

    if (empty($result['Lastip'])) {
      $result['Lastip'] = $userIP;
    }

    // We real big Pringles lovers here, which one is your favorite?
    switch ($result['Plan']) {

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
        $result['Status'] = 0;
    }

    if (!$result['Status'] || time() > $result['Expire']) {

      // This nerd expired, throw him out.
      $do =
        $this->db->prepare(
          "UPDATE users SET Status = (:status) WHERE HWID = (:hwid)",
        );
      $do->bindParam(":hwid", $hwid);
      $do->bindParam(":status", $result['Status']);
      $do->execute();

      // b y e  b y e
      return "Bruh, this nerd isn't on the list.";
    }

    // Everything seems fine, let's return your account information and log your
    // login request, make sure you aren't a dirty account sharing cunt.


    $do =
      $this->db->prepare("UPDATE users SET Password = (:password),
                            Lastlogin = (:lastlogin),
                            Lastip = (:lastip) WHERE HWID = (:hwid)");
    $do->bindParam(":hwid", $hwid);
    $do->bindParam(":password", password_hash($hwid, PASSWORD_BCRYPT));
    $do->bindParam(":lastlogin", $time);
    $do->bindParam(":lastip", $userIP);
    $do->execute();

    return json_encode(
      array(
        "expire" => $result['Expire'],
        "hwid" => $result['HWID'],
        "lvl" => $level,
        "md5_launcher" => "6",
        "plan_name" => "[FOSSIL] ".$plan_name,
        "plan" => $result['Plan'],
        "status" => $result['Status'],
      ),
      JSON_PRETTY_PRINT,
    );

  }
  /**
  * Check if the user's ISP matches the last logged in
  * ISP by comparing ASN.
  * @param string $hwid
  * @param string $userIP
  * @return bool
  */

  public function userAccountSharing(string $hwid, string $userIP): bool {

    $do = $this->db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
    $do->bindParam(":hwid", $hwid);
    $do->execute();
    $result = $do->fetch();

    $time = date("D M, Y H:i:s", strtotime("now"));
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";

    if (geoip_asnum_by_name($result['Lastip']) !=
        geoip_asnum_by_name($userIP)) {

      $Failedip = json_encode(
        array(
          "Date" => $time,
          "IP Adress" => $userIP,
          "Username" => $result['username'],
          "HWID" => $result['HWID'],
          "User-Agent" => $_SERVER['HTTP_USER_AGENT'],
        ),
        JSON_PRETTY_PRINT,
      );

      $do =
        $this->db->prepare(
          "UPDATE users SET Failedip = (:Failedip) WHERE HWID = (:hwid)",
        );
      $do->bindParam(":hwid", $hwid);
      $do->bindParam(":Failedip", $Failedip);
      $do->execute();

      return TRUE;

    } else {
      return FALSE;
    }
  }
  /**
   * Check if there is a new update avaliable.
   * @param string $info
   * @return mixed
   */

  public function userUpdate($info): mixed {

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

    switch ($info) {
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

  /**
   * Used for maintaing configs for each user, if none exist give default.
   * @param string $action
   * @param string $hwid
   * @param string $data (optional)
   * @return string
   */

  public function userConfig(
    string $action,
    string $hwid,
    string $data = "",
  ): mixed {

    // Setting up variables for logging
    $time = date("D M, Y H:i:s", strtotime("now"));
    $userIP = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";

    $do = $this->db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
    $do->bindParam(":hwid", $hwid);
    $do->execute();
    $result = $do->fetch();

    if (!$result['Status']) {
      return "yooo bruh this nignog aint on the list, get the bouncers.";
    }

    switch ($action) {

      case "load":

        if (empty($result['Config'])) {
          return file_get_contents(
            $_SERVER["DOCUMENT_ROOT"]."/assets/default.config.json",
          );
        }
        return $result['Config'];

        break;

      case "save":

        // Let's first verify that we are dealing with JSON here.
        // If not it might be a possible hacking attempting, log data of precautions.
        if (json_decode($data) === NULL) {
          $FailedConfig = json_encode(
            array(
              "Date" => $time,
              "HWID" => $result['HWID'],
              "IP Adress" => $userIP,
              "Request-Data" => $data,
              "User-Agent" => $userAgent,
              "Username" => $result['username'],
            ),
            JSON_PRETTY_PRINT,
          );

          $do =
            $this->db
              ->prepare(
                "UPDATE users SET FailedConfig = (:Failedconfig) WHERE HWID = (:hwid)",
              );
          $do->bindParam(":hwid", $hwid);
          $do->bindParam(":Failedconfig", $FailedConfig);
          $do->execute();

          echo $FailedConfig;
          return "AhAhahaHAhaha no. all your shit is  logged, get out.";

        }

        $do =
          $this->db->prepare(
            "UPDATE users SET Config = (:config) WHERE HWID = (:hwid)",
          );
        $do->bindParam(":hwid", $hwid);
        $do->bindParam(":config", $data);
        $do->execute();
        echo "Config saved";
        break;

      case "format":
        if (empty($result['Config'])) {
          $result['Config'] = file_get_contents(
            $_SERVER["DOCUMENT_ROOT"]."/assets/default.config.json",
          );
        }
        $ugly = json_decode($result['Config'], TRUE);
        $pretty = json_encode(
          array(
            "Visuals" => array(
              "Items" => array(
                "ItemsEnabled" => $ugly['ItemsEnabled'],
                "MainWeapon" => $ugly['MainWeapon'],
                "Attachment" => $ugly['Attachment'],
                "Armor" => $ugly['Armor'],
                "Backpack" => $ugly['Backpack'],
                "Heal" => $ugly['Heal'],
                "Grenade" => $ugly['Grenade'],
                "MaxItemdistance" => $ugly['MaxItemdistance'],
              ),
              "Players" => array(
                "PlayersEnabled" => $ugly['PlayersEnabled'],
                //"PlayerName" => $ugly['PlayerName'],
                "PlayerDistance" => $ugly['PlayerDistance'],
                "PlayerHealth" => $ugly['PlayerHealth'],
                "PlayerSkeleton" => $ugly['PlayerSkeleton'],
                "DrawTeam" => $ugly['DrawTeam'],
                "ESPVisibleCheck" => $ugly['ESPVisibleCheck'],
                "MaxPlayerdistance" => $ugly['MaxPlayerdistance'],
                "MaxSkeletonDist" => $ugly['MaxSkeletonDist'],
                "Radar" => array(
                  "PlayerRadar" => $ugly['PlayerRadar'],
                  "RadarX" => $ugly['RadarX'],
                  "RadarY" => $ugly['RadarY'],
                  "RadarW" => $ugly['RadarW'],
                ),
              ),
              "Misc" => array(
                "AirDrop" => $ugly['AirDrop'],
                "Car" => $ugly['Car'],
                "DeathLoot" => $ugly['DeathLoot'],
                "MiscDistance" => $ugly['MiscDistance'],
              ),
            ),
            "Aim" => array(
              "aimEnabled" => $ugly['aimEnabled'],
              "drawFov" => $ugly['drawFov'],
              "noSway" => $ugly['noSway'],
              "aimPredict" => $ugly['aimPredict'],
              "aimVisibleCheck" => $ugly['aimVisibleCheck'],
              "aimBone" => $ugly['aimBone'],
              "aimFov" => $ugly['aimFov'],
              "aimKey" => $ugly['aimKey'],
            ),
            "Settings" => array(
              "menuKey" => $ugly['menuKey'],
              "itemKey" => $ugly['itemKey'],
            ),
          ),
          JSON_PRETTY_PRINT,
        );

        return $pretty;
        break;

      default:
        echo "Hmm, something seems to be missing here...";

    }

  }
}
