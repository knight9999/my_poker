<?php 

class GRand extends CComponent {
	public static $counter = 0;
	public static $list = null;
	
	static public function ready( $list ) {
		self::$list = $list;
		self::$counter = 0;
	}

	static public function rand($min,$max) {
		if ($min>$max) {
			throw "min is larger than max";
		}
		if (isset(self::$list)) {
			$x = self::$list[self::$counter++];
			while ($max<$x) {
				$x -= ($max-$min+1);
			}
			while ($x<$min) {
				$x += ($max-$min+1);
			}
			return $x;
		}
		return rand($min,$max);
	}

	
	
}


?>