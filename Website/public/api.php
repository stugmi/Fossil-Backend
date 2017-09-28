<?hh
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$crack = new fossil();
$api = $_GET;

switch (TRUE) {

  case $api['panel']:

    switch ($api['panel']) {

      case "login":
        echo $crack->userLogin($_POST['username'], $_POST['password']);
        break;

      case "logout":
        echo $crack->userLogout();
        break;

      default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo "I'm not sure if you know what you're doing.";
        break;
    }
    break;

  case $api['HWID']:

    switch ($api['action']) {

      // User information
      case "userinfo":
        echo $crack->userInfo($api['HWID']);
        break;

        // update checking
      case "md5":
        echo $crack->userUpdate("md5");
        break;

      case "download":
        echo $crack->userUpdate("download");
        break;

        // Config managment
      case "load":
        echo $crack->userConfig("load", $api['HWID']);
        break;

      case "format":
        echo $crack->userConfig("format", $api['HWID']);
        break;

      case "save":
        echo $crack->userConfig("save", $api['HWID'], $_POST['json']);
        break;

      default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo "404 action not found";

    }
    break;

  default:
    header("HTTP/1.0 405 Method Not Allowed");
    echo "Ah bah ah, you didn't say magic word";

}
