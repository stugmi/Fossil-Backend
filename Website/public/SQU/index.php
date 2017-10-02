<?hh

// Initial setup for all classes
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$site = new site();
$fossil = new fossil();


//setting up the standard head for every page
$site->siteHead();


// Not logged in? Just kill it, not giving him anything.
if (!isset($_SESSION['loggedin'])) {
  $site->loginForm();
  die();
}

$site->userDetail();
$site->siteHeader();
$site->panelEditor($fossil->userConfig("format", $_SESSION['hwid']));
$site->logoutForm();
