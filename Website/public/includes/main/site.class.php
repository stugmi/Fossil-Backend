<?hh

/**
 * Class used for building the pages for the website
 * since this is based on Hack (see Facebook's Hack language)
 * it's easier to make pages through functions
 *
 * @author h
 * @version 0.8
 * @abstract
 * @copyright never
 */

class site {

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
   * Prints basic header to each page
   * @param $title (optional) string
   * @return void
   */

  public function siteHead(?string $title = "FOSSIL :: "): void {
    print
      (
      "<head>
        <title>{$title} {$_SESSION['username']}</title>
        <link rel='shortcut icon' type='image/png' href='../assets/img/favicon.ico'/>
        <link href='../../assets/css/main.css' rel='stylesheet' />
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js'></script>
      </head>
    ");
  }


  /**
   * Prints basic header to each page
   * @param $title (optional) string
   * @return void
   */

  public function siteHeader(): void {
    print ("
      <div id='content'>
      <h1 class='htp'>
      Welcome <a id='user'>{$_SESSION['username']}</a>
      </h1>
    ");
  }



  /**
  * Prints user details
  * @return void
  */

  public function userDetail(): void {
    $do =
      $this->db
        ->prepare(" SELECT
                      users.id         AS id,
                      users.username   AS username,
                      users.password   AS password,
                      users.admin      AS admin,
                      users.status     AS status,
                      users.hwid       AS hwid,
                      users.config     AS config,
                      users.lastip     AS lastip,
                      users.lastlogin  AS lastlogin,
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

    $do->bindParam(":username", $_SESSION['username']);
    $do->execute();
    $result = $do->fetchAll(PDO::FETCH_CLASSTYPE);

    $plans = array();
    foreach($result as $plan){
          $plans[] = array( $plan['plan_id'],
                            $plan['plan_name'],
                            $plan['expire']
                        );
    }

    print("<div id='userInfo'>
          <div class='userDetail'>
            <h5 id='userDetail'>Username</h5>
            <a id='user'>{$result[0]['username']}</a>
          </div>
          <div class='userDetail'>
          <h5 id='userDetail'>Admin</h5>
          {$result[0]['admin']}
          </div>
          <div class='userDetail'>
          <h5 id='userDetail'>HWID</h5>
          {$result[0]['hwid']}
          </div>
          <div class='userDetail'>
          <h5 id='userDetail'>Last Login</h5>
          {$result[0]['lastlogin']}
          </div>
          ");

          $plans = array();
          print("<div id='plans'>");
          foreach($result as $plan){
                $expire = gmdate('r',$plan['expire']);
                print("
                <h5 id='userDetail'>Plan</h5>
                {$plan['plan_name']}
                <h5 id='userDetail'>Until</h5>
                {$expire}
                ");
          } print("</div>");

    print("</div>");
  }

  /**
   * Login page and form
   *
   * @return void
   */

  public function loginForm(): void {
    print ("
     <div id='login'>
       <form method='post' action='../api/panel/login'>
         <input type='text' name='username' placeholder='Username...' />
         <br />
         <input
           type='password'
           name='password'
           placeholder='Password...'
         />
         <br />
         <input type='submit' value='Login' />
       </form>
     </div>
    ");
  }

  /**
   * Logout button, probably doesn't even
   * need it's own function
   *
   * @return void
   */

  public function logoutForm(): void {
    print ("
      <div id='logout'>
        <form  method='post' action='../api/panel/logout'>
          <input type='submit' name='logout' value='Logout' />
        </form>
      </div>
    ");
  }

  /**
   * The amazing web-based config editor that i spent
   * way too long making, amazing this is.
   * @param string $config
   * @return mixed
   */
  public function panelEditor(string $config): mixed {

    $config = json_decode($config, TRUE);
    print
      ("
      <div id='config'>
        <div id='config-header'>
          <h2> Config Editor </h2>
          <form action='../api_info/hardwareid/{$_SESSION['hwid']}/action/save' method='post' id='configPost'>
        </div>
      <div id='config-content'>
    ");

    foreach ($config as $cat => $cats) {

      // Hitting each main category (Visual, Aim, Settings)
      print ("
        <div class='header' id='{$cat}'>
          <i class='arrow down' id='$cat'></i><a>{$cat}</a>
        </div>
        ");

      print ("
        <div id='{$cat}Tab'
        class='main-cat'>
      ");
      foreach ($cats as $cat2 => $settings) {

        // Hitting sub categories (Items, Players, Misc)
        if (is_array($settings)) {

          print ("
            <div class='header' id='$cat2'>
              <i class='arrow down' id='$cat2'>
              </i><a> $cat2 </a>
            </div>
          ");

          print ("<div id='{$cat2}Tab' class='sub-cat'>");
          foreach ($settings as $cat3 => $settings) {

            // Hitting Sub Sub category if there is one (looking at you radar you fucking spastic)
            if (is_array($settings)) {

              print ("
                <div class='header' id='$cat3'>
                  <i class='arrow down' id='$cat3'></i>
                  <a> $cat3 </a>
                </div>
              ");

              print ("<div id='{$cat3}Tab' class='submissive-as-fuck-cat'>");
              foreach ($settings as $cat4 => $settings) {
                if (is_bool($settings)) {
                  if ($settings === true) {
                    $settings = "checked";
                  } else {
                    $settings = "";
                  }
                  print
                    ("
                    <input type='checkbox' class='styled-checkbox'id='{$cat4}' name='{$cat4}' $settings>
                    <label for='{$cat4}'>{$cat4}</label>
                    </br>
                  ")
                  ;
                } else {
                  print
                    ("
                    <input max='1000' type='range' name='{$cat4}' value='{$settings}' class='sliders' oninput='$(\"#{$cat4}Out\").val(parseInt(this.value))'>
                    <output id='{$cat4}Out'>{$settings}</output>
                    <label>{$cat4}</label>
                    </br>
                  ")
                  ;
                }
              }
              print ("</div>");
              // Settings under 2 dimensional categories
            } else {
              if (is_bool($settings)) {
                if ($settings === true) {
                  $settings = "checked";
                } else {
                  $settings = "";
                }
                print
                  ("
                  <input type='checkbox' class='styled-checkbox'id='{$cat3}' name='{$cat3}' $settings>
                  <label for='{$cat3}'>{$cat3}</label>
                  </br>
                ")
                ;
              } else {
                print
                  ("
                  <input max='1000' type='range' name='{$cat3}' value='{$settings}' class='sliders' oninput='$(\"#{$cat3}Out\").val(parseInt(this.value))'>
                  <output id='{$cat3}Out'>{$settings}</output>
                  <label>{$cat3}</label></br>
                ")
                ;
              }
            }
          }
          print ("</div>");
          // Settings under 1 dimensional Categories
        } else {
          if (is_bool($settings)) {
            if ($settings === true) {
              $settings = "checked";
            } else {
              $settings = "";
            }
            print
              ("
              <input type='checkbox' class='styled-checkbox'id='{$cat2}' name='{$cat2}' $settings>
              <label for='{$cat2}'>{$cat2}</label>
              </br>
            ")
            ;
          } else {
            print
              ("
              <input max='1000' type='range' name='{$cat2}' value='{$settings}' class='sliders' oninput='$(\"#{$cat2}Out\").val(parseInt(this.value))'>
              <output id='{$cat2}Out'>{$settings}</output>
              <label>{$cat2}</label>
              </br>
            ")
            ;
          }
        }
      }
      print ("</div></form>");
    }
    print ("</div></div></div>");
    print ('
      <script>
      $(".header").click(function(e){
          $("div#" + e.target.closest("div").id + "Tab").toggle();
          $("i#" + e.target.closest("div").id ).toggleClass("right down");
        });
        $("#config").draggable({
            handle: "h2"
        });
      </script>
    ');
  }
}
