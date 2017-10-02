<?hh
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$fossil = new fossil();

if (isset($_GET['action'])) {
  $page = 'api';
  $api = $_GET;
} else if (isset($_GET['panel'])) {
  $page = 'panel';
  $api = $_GET;
} else {
  die("Code: 404");
}

switch ($page) {

  case 'panel':

    switch ($api["panel"]) {

      case "login":
        if ($fossil->userLogin($_POST['username'], $_POST['password'])) {
          header("Code: 302");
          header("Location: ../../../SQU/");
        } else {
          header("Code: 302");
          header("Location: ../../../SQU/index.php#fail");
        }
        break;

      case "logout":
        session_destroy();
        header("Code: 200");
        header("Location: ../../../SQU/");
        break;

      default:
        header("Code: 405");
        echo "I'm not sure if you know what you're doing.";
        break;
    }
    break;

  case 'api':

    switch ($api['action']) {

      // User information
      case "login":
        echo $fossil->cheatLogin($api['usuario'], $api['pwd'], $api['hwid']);
        break;

        // update checking
      case "download_launcher":
        echo $fossil->cheatDownload("md5");
        break;

      case "select_cheat":
        $fossil->cheatDownload("select_cheat", $api['hwid'], $api['plan']);
        break;

      case "check_plan":
        $fossil->cheatCheckPlan($api['hwid']);
        break;

        // Config managment
      case "load":
        echo $fossil->userConfig("load", $api['hwid']);
        break;

      case "format":
        echo $fossil->userConfig("format", $api['hwid']);
        break;

      case "save":
        echo $fossil->userConfig("save", $api['hwid'], $_POST['json']);
        break;

      default:
        header("Code: 404");
        echo "404 action not found";
        break;

    }
    break;

  default:
    header("Code: 405");
    echo "Ah bah ah, you didn't say magic word";
    break;

}
