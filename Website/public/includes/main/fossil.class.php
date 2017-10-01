<?hh
use GeoIp2\Database\Reader;

/**
 * Class to handle almost if not everything regarding users of this service
 *
 * @author h
 * @version 0.8
 * @abstract
 * @copyright never
 */

class fossil
{

  /**
   * Once class is used, make sure we have DB connection.
   * @return void
   */
  private $db;
  public function __construct(): void
  {
    include_once (__DIR__.'/../config.inc.php');
    try {
      $this->db = new PDO(HTP_DB, HTP_USER, HTP_PASS);
    } catch (PDOException $e) {
      die('Connection failed: '.$e->getMessage());
    }
  }

  /**
   * Used for login if we ever need it. Panel is in work.
   * @param string $username
   * @param string $password
   * @param ?bool $panel (optional)
   * @return bool
   */
  public function userLogin(string $username, string $password, bool $panel = TRUE): bool
  {

    $do =
      $this->db->prepare("SELECT * FROM users WHERE Username = (:username)");
    $do->bindParam(":username", $username);
    $do->execute();
    $result = $do->fetch();

    if (empty($result['Username'])){
      return FALSE;
    }

    // If user doesn't have a password but exist, set the password.
    if(empty($result['Password'])){
      $password = $result['Password'];
      $do = $this->db->prepare("UPDATE users SET Password = (:password) WHERE Username = (:username)");
      $do->bindParam(":username", $username);
      $do->bindParam(":password", password_hash($password, PASSWORD_BCRYPT));
      $do->execute();
      return TRUE;
    }

    if (password_verify($password, $result['Password'])) {

      // Dumb check for when the config wants to use it.
      if($panel){
        $_SESSION['loggedIn'] = TRUE;
        $_SESSION['username'] = $username;
        $_SESSION['hwid'] = "N/A" ?: $result['HWID'];

        return TRUE;
      }
      return TRUE;

    } else {
      return FALSE;
    }

  }

  /**
   * Validate a user, if active return level of access.
   * @param string $username
   * @param string $password
   * @param string $hwid
   * @return mixed
   */
  public function cheatLogin(string $username, string $password, string $hwid): string
  {
    // Verify that the account exist first
    $this->userLogin($username, $password, FALSE);
    $do =
      $this->db // still not working completly but we are getting further
        ->prepare(" SELECT
                      users.id         AS id,
                      users.username   AS username,
                    	users.password   AS password,
                    	users.admin      AS admin,
                    	users.status     AS status,
                    	users.hwid       AS hwid,
                    	users.config     AS config,
                    	users.lastip     AS lastip,
                      cheats.plan_id   AS plan_id,
                    	cheats.plan_name AS plan_name,
                    	cheats.plan_game AS plan_game,
                    	plans.expire     AS expire
                    FROM
                      users
                    INNER JOIN plans  ON plans.user_id = users.id
                    INNER JOIN cheats ON plans.plan_id = cheats.plan_id
                    WHERE
                    	users.username = (:username)
                ");

    $do->bindParam(":username", $username);
    $do->execute();
    $result = $do->fetch();

    // Making sure this is setup before php freaks out
    $time = date("D M, Y H:i:s", strtotime("now"));
    $userIP = inet_pton($_SERVER['REMOTE_ADDR']);
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";
    $plans = array();

    foreach($result as $plan){
      array_push($plans,
                 $plan['plan_id'],
                 $plan['plan_name'],
                 $plan['expire']
                );
    }

    // for new users
    if(!$result['0']['hwid']){
      $result['0']['hwid'] = $hwid;
    }

    if(!$result['0']['lastip']){
      $result['0']['lastip'] = $userIP;
    }

    if (!$result['0']['plan_id'] || time() > $result['0']['expire']) {
      // This nerd expired or got no cheat, throw him out.
      $do =
        $this->db->prepare(
          "UPDATE users SET status = (:status) WHERE id = (:id)",
        );
      $do->bindParam(":id", $result['0']['id']);
      $do->bindParam(":status", $result['0']['status']);
      $do->execute();

      // b y e  b y e
      return "It says 'do not let inn' on my paper, guess that means you're not getting in. b y e  b y e nerd ";
    }

    // Everything seems fine, let's return your account information and log your
    // login request, make sure you aren't a dirty account sharing cunt.
    if($this->userAccountSharing($hwid, $userIP) === TRUE){
      return header("Status: 403 Account sharing detected and logged please contact us to unlock your account");
    }

    $do =
      $this->db->prepare("UPDATE users SET
                            password = (:password),
                            lastlogin = (:lastlogin),
                            lastip = (:lastip),
                            hwid = (:hwid)
                          WHERE id = (:id)
                        ");
    $do->bindParam(":id", $result['0']['id']);
    $do->bindParam(":hwid", $hwid);
    $do->bindParam(":password", password_hash($password, PASSWORD_BCRYPT));
    $do->bindParam(":lastlogin", $time);
    $do->bindParam(":lastip", $userIP);
    $do->execute();

    return json_encode(
      array(
        "launcher_version" => "10",
        "hwid" => $result['0']['hwid'],
        "plans" => $plans,
        "status" => $result['0']['Status']
      ), JSON_PRETTY_PRINT
    );

  }

  /**
  * Check if the user's ISP matches the last logged in
  * ISP by comparing ASN.
  * @param string $hwid
  * @param string $userIP
  * @return bool TRUE if sharing
  */
  public function userAccountSharing(string $hwid, string $userIP): bool
  {

    $do = $this->db->prepare("SELECT * FROM users WHERE HWID = (:hwid)");
    $do->bindParam(":hwid", $hwid);
    $do->execute();
    $result = $do->fetch();

    // Convert binary to IP from DB
    $userIP = inet_ntop($userIP);
    $lastIP = inet_ntop($result['Lastip']);

    $time = date("D M, Y H:i:s", strtotime("now"));
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?: "N/A";
    if (file_get_contents("http://ipinfo.io/" . $lastIP . "/org")
        !== file_get_contents("http://ipinfo.io/" . $userIP . "/org")) {

      $Failedip = json_encode(
        array(
          "Date" => $time,
          "IP Adress" => $userIP,
          "Username" => $result['Username'],
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

      return TRUE;    //  User is sharing
    } else {         // else
      return FALSE; // User isnt sharing
    }
  }
  /**
   * Check if there is a new update avaliable.
   * @param string $action
   * @param string $hwid
   * @param string $plan
   * @return mixed
   */

  public function cheatDownload(string $action, string $hwid, string $plan = ""): mixed
  {


    /*

    // TODO: Make this somewhat automated, idk how yet maybe proxy?
     $context  = stream_context_set_default(
       array(
         'http'=>array(
           'proxy' => "82.131.46.130:8888", // JIRX's proxy
           'request_fulluri' => true
         )
       )
     );

     */


    switch ($action) {

      case "select_cheat":

        header("Content-Disposition: attachment; filename='GLPUBG.dll'");
        header("x-accel-redirect: /dl/GLPUBG.dll");
        break;

      case "download_launcher":
        return "loldongs";
        break;


      default:
        echo "Hmm, something seems to be missing here...";
        break;
    }

  }

  /**
   * Used for maintaing configs for each user, if none exist give default.
   * @param string $action
   * @param string $hwid
   * @param string $data (optional)
   * @return string
   */

  public function userConfig(string $action, string $hwid, string $data = ""): mixed
  {

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
        break;

    }

  }

  /**
   * Translates the config for better viewing on editor
   * @param string $info
   * @return mixed
   */

   public function configTranslate(string $key): mixed
   {
     $pretty_names = array();
   }
}
