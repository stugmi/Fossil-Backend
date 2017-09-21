<?hh

// Initial setup for all classes
include_once $_SERVER["DOCUMENT_ROOT"].'/includes/includes.php';
new loadClasses("main");

$site = new site();
$crack = new fossil();

// First off all we want headers on top
$site->header();

// Not logged in? Just kill it, not giving him anything.
if (!isset($_SESSION['loggedIn'])) {
  $site->loginForm();
  die();
}

// Setting up variables once we know they are logged in
$user = $_SESSION['username'];
$hwid = $_SESSION['hwid'];

echo
  <body>
  Hello {$user},

  Your config is:
    <pre>
    {$crack->config("load", $hwid)}
    </pre>
  </body>;

$site->logout();
