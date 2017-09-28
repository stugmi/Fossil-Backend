<?hh
class bench
{

	private float $start;
  public function __construct(){
    $this->start = microtime(true);
  }

	/**
  * Get time in microseconds
  * @return float
  */
	public function timeGetUMS(): mixed {
	   return print_r(number_format(microtime(true) - $this->start));
	}

  /**
  * Get time in milliseconds
  * @return float
  */
	public function timeGetMS(): mixed {
	   return print_r(number_format(microtime(true) - $this->start));
	}

  /**
  * Get time in seconds
  * @return float
  */
	public function timeGetS(): mixed {
	   return print_r(number_format(microtime(true) - $this->start));
	}

}
echo "dongs";
$t = new bench();
$t->timeGetUMS;
usleep(5);
$t->timeGetUMS;
usleep(100);
$t->timeGetS;
