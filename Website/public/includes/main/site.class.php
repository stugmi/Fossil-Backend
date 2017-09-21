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
   * Prints basic header to each page
   * @param $title (optional) string
   * @return void
   */

  public function header(?string $title = "FOSSIL :: Pringles"): void {
    print
      (
       <head>
         <title>{$title}</title>
         <link
           rel="shortcut icon"
           type="image/png"
           href="../assets/img/favicon.ico"
         />
         <link href="../../assets/css/main.css" rel="stylesheet" />
       </head>
      )
    ;
  }

  public function loginForm(): void {
    print
      (
       <form method="post" action="../api/panel/login">
         You have to sign in to view this page<br />
         <input type="text" name="username" placeholder="Username..." />
         <br />
         <input type="password" name="password" placeholder="Password..." />
         <br />
         <input type="submit" />
       </form>)
    ;
  }

  public function logout(): void {
    print(
    <form method="post" action="../api/panel/logout">
    <input type="submit"  name="logout" value="Logout"/>
    </form>
    );
  }
}
