<?hh

// Initial setup for all classes
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$site = new site();
$fossil = new fossil();

// Not logged in? Just kill it, not giving him anything.
if (!isset($_SESSION['loggedIn'])) {
  $site->loginForm();
  die();
}

// Setting up variables once we know they are logged in
$user = $_SESSION['username'];
$hwid = $_SESSION['hwid'];

$site->siteHead();
$site->siteHeader();
$site->panelDefault($user, $hwid, $fossil->userConfig("format", $hwid));
$site->logoutForm();
