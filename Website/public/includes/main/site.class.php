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
         <script
           src=
             "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
         </script>
       </head>
      )
    ;
  }

  public function loginForm(): void {
    print
      (
       <div id="login">
         <form method="post" action="../api/panel/login">
           <input type="text" name="username" placeholder="Username..." />
           <br />
           <input
             type="password"
             name="password"
             placeholder="Password..."
           />
           <br />
           <input type="submit" value="Login" />
         </form>
       </div>)
    ;
  }

  public function logout(): void {
    print
      (
       <form method="post" action="../api/panel/logout">
         <input type="submit" name="logout" value="Logout" />
       </form>
      )
    ;
  }

  public function panelDefault($username, $hwid, $config): void {
    $config = json_decode($config, TRUE);

    print ('<div id="content">');
    print
      (
       <h1 class="htp">
         Welcome <a id="user">{$username}</a>
       </h1>
      )
    ;

    print
      ("<div id='config'>
    <h2> Config Editor </h2>
    <form action='../api_info/hardwareid/{$hwid}/action/save' method='post' id='configPost'>")
    ;

    foreach ($config as $cat => $cats) {

      // Hitting each main category (Visual, Aim, Settings)
      print
        ("<div class='header' id='$cat'><i class='arrow down' id='$cat'></i><a> $cat </a></div>")
      ;
      print ("<div id='{$cat}Tab' class='main-cat'>");
      foreach ($cats as $cat2 => $settings) {

        // Hitting sub categories (Items, Players, Misc)
        if (is_array($settings)) {

          print
            ("<div class='header' id='$cat2'><i class='arrow down' id='$cat2'></i><a> $cat2 </a></div>")
          ;
          print ("<div id='{$cat2}Tab' class='sub-cat'>");
          foreach ($settings as $cat3 => $settings) {

            // Hitting Sub Sub category if there is one (looking at you radar you fucking spastic)
            if (is_array($settings)) {

              print
                ("<div class='header' id='$cat3'><i class='arrow down' id='$cat3'></i><a> $cat3 </a></div>")
              ;
              print ("<div id='{$cat3}Tab' class='submissive-as-fuck-cat'>");
              foreach ($settings as $cat4 => $settings) {
                if (is_bool($settings)) {
                  if ($settings === true) {
                    $settings = "checked";
                  } else {
                    $settings = "";
                  }
                  print
                    ("<input type='checkbox' class='styled-checkbox'id='{$cat4}' name='{$cat4}' $settings>
                      <label for='{$cat4}'>{$cat4}</label></br>")
                  ;
                } else {
                  print
                    ("<input max='1000' type='range' name='{$cat4}' value='{$settings}' class='sliders' oninput='$(\"#{$cat4}Out\").val(parseInt(this.value))'>
                        <output id='{$cat4}Out'>{$settings}</output>
                        <label>{$cat4}</label></br>")
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
                  ("<input type='checkbox' class='styled-checkbox'id='{$cat3}' name='{$cat3}' $settings>
                    <label for='{$cat3}'>{$cat3}</label></br>")
                ;
              } else {
                print
                  ("<input max='1000' type='range' name='{$cat3}' value='{$settings}' class='sliders' oninput='$(\"#{$cat3}Out\").val(parseInt(this.value))'>
                      <output id='{$cat3}Out'>{$settings}</output>
                      <label>{$cat3}</label></br>")
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
              ("<input type='checkbox' class='styled-checkbox'id='{$cat2}' name='{$cat2}' $settings>
                <label for='{$cat2}'>{$cat2}</label></br>")
            ;
          } else {
            print
              ("<input max='1000' type='range' name='{$cat2}' value='{$settings}' class='sliders' oninput='$(\"#{$cat2}Out\").val(parseInt(this.value))'>
                  <output id='{$cat2}Out'>{$settings}</output>
                  <label>{$cat2}</label></br>")
            ;
            ;
          }
        }
      }
      print ("</div></form>");
    }

    print ('</div></div>
    <script>
    $(".header").click(function(e){
        $("div#" + e.target.closest("div").id + "Tab").toggle();
        $("i#" + e.target.closest("div").id ).toggleClass("right down");
      });
    </script>
    ');

  }
}
