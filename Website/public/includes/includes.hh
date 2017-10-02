<?hh

// making sure we have composer addons
include_once $_SERVER["DOCUMENT_ROOT"] . '/../vendor/autoload.hh';

// Hope this works on all now
session_start();

/**
* Really ugly method to load all classes needed when specified.
* @author H
* @copyright no
* @version last
*/
class loadClasses {

  public function __construct($classes = "main") {
    spl_autoload_register($this->autoLoad($classes));
  }

  public function autoLoad($group) {
    $folder = __dir__."/".$group;
    foreach (glob($folder."/*.hh", GLOB_BRACE) as $class) {
      if (is_file($class)) {
        include ($class);
      }
    }
  }

}
