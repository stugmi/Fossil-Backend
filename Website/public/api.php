<?hh
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

ob_start();

$crack = new fossil();
$api = $_GET;

switch (TRUE) {

  case $api['panel']:

    switch ($api['panel']) {

      case "login":
        if (!$crack->userLogin($_POST['username'], $_POST['password'])) {
          header("HTTP/1.0 405 Method Not Allowed");
          die("Wrong credentials");
        }

        echo "Successfully logged in";
        header("Location: ../../SQU/");
        break;

      case "logout":
        $crack->userLogout();
        echo "Successfully logged out";
        header("Location: ../../SQU/");
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
        header("HTTP/1.0 405 Method Not Allowed");
        echo "404 action not found";

    }
    break;

  default:
    header("HTTP/1.0 405 Method Not Allowed");
    echo "Ah bah ah, you didn't say magic word";

}
