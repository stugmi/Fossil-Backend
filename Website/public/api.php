<?hh
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$fossil = new fossil();
$api = $_GET;

switch (TRUE) {

  case $api['panel']:

    switch ($api['panel']) {

      case "login":
        if($fossil->userLogin($_POST['username'], $_POST['password'])){
          header("HTTP/1.1 302 Moved Temporarily");
          header("Location: ../../../SQU/");
        } else {
          header("HTTP/1.1 302 Moved Temporarily");
          header("Location: ../../../SQU/index.php#fail");
        }
        break;

      case "logout":
        echo $fossil->userLogout();
        break;

      default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo "I'm not sure if you know what you're doing.";
        break;
    }
    break;

  case $api['action']:

    switch ($api['action']) {

      // User information
      case "login":
        echo $fossil->userInfo($api['usuario'], $api['pwd'], $api['hwid']);
        break;

        // update checking
      case "md5":
        echo $fossil->cheatDownload("md5");
        break;

      case "select_cheat":
        $fossil->cheatDownload("select_cheat", $api['hwid'], $api['plan']);
        break;

        // Config managment
      case "load":
        echo $fossil->userConfig("load", $api['HWID']);
        break;

      case "format":
        echo $fossil->userConfig("format", $api['HWID']);
        break;

      case "save":
        echo $fossil->userConfig("save", $api['HWID'], $_POST['json']);
        break;

      default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo "404 action not found";
        break;

    }
    break;

  default:
    header("HTTP/1.0 405 Method Not Allowed");
    echo "Ah bah ah, you didn't say magic word";
    break;

}
