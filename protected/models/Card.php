<?php 

class Card extends CComponent {
	public $mark; // 1,2,3,4
	public $number; // 1,2,3,4,5,6,7,8,9,10,11,12,13;
	
	public function __construct( $map ) {
		$this->mark = $map['mark'];
		if (isset($map['number'])) {
			$this->number = $map['number'];
		} else {
			$this->number = 0;
		}
		
	}
	
	public function image() {
		if ($this->mark==0) {
			return $this->bg_image();
		}
		$str = null;
		if ($this->number <= 10) {
			$str = (string) $this->number;
		} else {
			switch ($this->number) {
				case 11:
					$str = "J";
					break;
				case 12:
					$str = "Q";
					break;
				case 13:
					$str = "K";
					break;
			}
		}
		return "/images/cards/card".$this->mark."_".$str.".png";
	}
	
	public function bg_image() {
		return "/images/cards/card_bg.png";
	}
	
	/*
	public function to_map() {
		return new CMap( array( 'mark' => $this->mark , 'number' => $this->number ) );
	}
	
	public static function create( $map ) {
		$card = new Card();
		$card->mark = $map['mark'];
		$card->number = $map['number'];
		return $card;
	}
	*/
	public function equals($other) {
		return ($this->mark == $other->mark && $this->number == $other->number);
	}
	
}

?>