<?php 

class PokerHand extends CComponent {
	
	public $cards;
	public $level;
	public $name;
	public $supplement;
	
	public static $names = array(
			10 => "royal_straight_flush",
			9 => "straight_flush",
			8 => "four_cards",
			7 => "full_house",
			6 => "flush",
			5 => "royal_straight",
			4 => "straight",
			3 => "three_cards",
			2 => "two_pairs",
			1 => "one_pair" ,
			0 => "high_card"
	 );			
				
			
	
	
	public function __construct( $cards ) {
		$this->cards = $cards;
		$this->level = null;
		$this->name = null;
		$this->supplement = array();
	}
	
	public function level() {
		if (isset($this->level)) {
			return $this->level;
		}
		$this->calc();
		return $this->level;
	}

	public function levelName() {
		return self::$names[$this->level()];		
	}
	
	public function name() {
		if (isset($this->name)) {
			return $this->name;
		}
		$this->calc();
		return $this->name;
	}
	
	public function analyse($checkType = "combination",$report = null) {
		if (!isset($report)) {
			$report = new CMap();
		}
		switch ($checkType) {
			case "basic":
				$report["marks"] = new CMap();
				$report["numbers"] = new CMap();
				for ($i=1;$i<=4;$i++) {
					$report["marks"][$i] = 0;
				}
				for ($i=1;$i<=13;$i++) {
					$report["numbers"][$i] = 0;
				}
				foreach ($this->cards as $card) {
					$report["marks"][$card->mark] += 1;
					$report["numbers"][$card->number] += 1;
				}
				$report["checked"] = new CMap();
				$report["checked"]["basic"] = true;
				$report["flags"] = new CMap();
				break;
				
			case "marks":
				if (! $report["checked"]["basic"]) {
					$this->analyse("basic",$report);
				}
				for ($i=1;$i<=4;$i++) {
					if ($report["marks"][$i] == 5) {
						$report["flags"]["flash"] = true;
					}
				}
				$report["checked"]["marks"] = true;
				break;
				
			case "numbers":
				if (! $report["checked"]["basic"]) {
					$this->analyse("basic",$report);
				}
				$numbers = $report["numbers"];
				$marks   = $report["marks"];
				
				$last = 0;
				$count = 0;
				for ($i=1;$i<=13;$i++) {
					if ($numbers[$i]>=1) {
						if ($last == $i - 1 && $count>0 ) {
							$count += 1;
							if ($count>=5) {
								$report["flags"]["straight"] = true;
//								$flag_straight = true;
							}
						} else {
							$count = 1;
						}
						$last = $i;
					} else {
						$count = 0;
					}
				
					if ($numbers[$i] == 4) {
						$report["flags"]["fourcard"] = true;
//						$flag_fourcard = true;
					}
					if ($numbers[$i] == 3) {
						$report["flags"]["threecard"] = true;
//						$flag_threecard = true;
					}
					if ($numbers[$i] == 2) {
						if ($report["flags"]["one_pair"]) {
							$report["flags"]["two_pairs"] = true;
//							$flag_two_pair = true;
						} else {
							$report["flags"]["one_pair"] = true;
//							$flag_one_pair = true;
						}
					}
				}
				if ($last = 13 && $count == 4 && $numbers[1] >= 1) {
					$report["flags"]["royal_straight"] = true;
//					$flag_royal_straight = true;
				}
				$report["last"] = $last;
				$report["numbers"] = $numbers; 
				$report["marks"] = $marks;
				
				$report["checked"]["numbers"] = true;
				break;
			case "combination":
				if (! $report["checked"]["marks"]) {
					$this->analyse("marks",$report);
				}
				if (! $report["checked"]["numbers"]) {
					$this->analyse("numbers",$report);
				}
				if ($report["flags"]["one_pair"] && $report["flags"]["threecard"]) {
					$report["flags"]["fullhouse"] = true;
				}
				if ($report["flags"]["flash"] && $report["flags"]["straight"]) {
					$report["flags"]["straight_flash"] = true;
				}
				if ($report["flags"]["flash"] && $report["flags"]["royal_straight"]) {
					$report["flags"]["royal_straight_flash"] = true;
				}
				$report["checked"]["combination"] = true;
				break;
		}
		return $report;
	}
	
	public function calc() {
		
		/*
		$marks = new CMap();
		$numbers = new CMap();
		for ($i=1;$i<=4;$i++) {
			$marks[$i] = 0;
		}
		for ($i=1;$i<=13;$i++) {
			$numbers[$i] = 0;
		}
		foreach ($this->cards as $card) {
			$marks[$card->mark] += 1;
			$numbers[$card->number] += 1;
		}

		$flag_royal_straight_flash = false; 
		$flag_straight_flash = false;
		$flag_fourcard = false;
		$flag_fullhouse = false;
		$flag_flash = false;
		$flag_royal_straight = false;
		$flag_straight = false;
		$flag_threecard = false;
		$flag_two_pair = false;
		$flag_one_pair = false;
		
		for ($i=1;$i<=4;$i++) {
			if ($marks[$i] == 5) {
				$flag_flash = true;
			}
		}

		$last = 0;
		$count = 0;
		for ($i=1;$i<=13;$i++) {
			if ($numbers[$i]>=1) {
				if ($last == $i - 1 && $count>0 ) {
					$count += 1;
					if ($count>=5) {
						$flag_straight = true;
					}
				} else {
					$count = 1;
				} 
				$last = $i;
			} else {
				$count = 0;
			}
				
			if ($numbers[$i] == 4) {
				$flag_fourcard = true;
			}
			if ($numbers[$i] == 3) {
				$flag_threecard = true;
			}
			if ($numbers[$i] == 2) {
				if ($flag_one_pair) {
					$flag_two_pair = true;
				} else {
					$flag_one_pair = true;
				}
			}
		}
		if ($last = 13 && $count == 4 && $numbers[1] >= 1) {
			$flag_royal_straight = true;
		}
		
		if ($flag_one_pair && $flag_threecard) {
			$flag_fullhouse = true;
		}
		if ($flag_flash && $flag_straight) {
			$flag_straight_flash = true;
		}
		if ($flag_flash && $flag_royal_straight) {
			$flag_royal_straight_flash = true;
		}
		*/

		$report = $this->analyse();
		$numbers = $report["numbers"];
			
		if ($report["flags"]["royal_straight_flash"]) {
			$this->level = 10;
			$this->name = "ロイヤルストレートフラッシュ";
			$this->supplement = array(
				'mark' => $this->cards[0]->mark
			);
		} elseif ($report["flags"]["straight_flash"]) {
			$this->level = 9;
			$this->name = "ストレートフラッシュ";
			$this->supplement = array( 
				'mark' => $this->cards[0]->mark,
				'number' => $report["last"]
			);
		} elseif ($report["flags"]["fourcard"]) {
			$this->level = 8;
			$this->name = "フォーカード";
			$fourcard_number = 0;
			for ($i=1;$i<=13;$i++) {
				if ($numbers[$i] == 4) {
					$fourcard_number = $i;
				}
			}
			$this->supplement = array(
				'number' => $fourcard_number
			);
		} elseif ($report["flags"]["fullhouse"]) {
			$this->level = 7;
			$this->name = "フルハウス";
			$threecard_number = 0;
			for ($i=1;$i<=13;$i++) {
				if ($numbers[$i] == 3) {
					$threecard_number = $i;
				}
			}
			$this->supplement = array(
					'number' => $threecard_number
			);
		} elseif ($report["flags"]["flash"]) { 
			$this->level = 6;
			$this->name = "フラッシュ";
			$list = array();
			for ($i=14;$i--;$i>=2) {
				$k = $i;
				if ($i==14) {
					$k = 1;
   				}
				if ($numbers[$k] == 1) {
					array_push($list,$i);
				}
			}
			$this->supplement = array(
					'numbers' => $list
			);
		} elseif ($report["flags"]["royal_straight"]) {
			$this->level = 5;
			$this->name = "ストレート";
			$this->supplement = array();
		} elseif ($report["flags"]["straight"]) {
			$this->level = 4;
			$this->name = "ストレート";
			$this->supplement = array(
				'number' => $report["last"]
			);
		} elseif ($report["flags"]["threecard"]) {
			$this->level = 3;
			$this->name = "スリーカード";
			$threecard_number = 0;
			for ($i=1;$i<=13;$i++) {
				if ($numbers[$i] == 3) {
					$threecard_number = $i;
				}
			}
			$this->supplement = array(
					'number' => $threecard_number
			);
		} elseif ($report["flags"]["two_pairs"]) {
			$this->level = 2;
			$this->name = "ツーペア";
			$pair_numbers = array();
			$other_number = 0;
			for ($i=14;$i>=2;$i--) {
				$k = $i;
				if ($i==14) {
					$k = 1;
				}
				if ($numbers[$k]==2) {
					array_push($pair_numbers,$i);
				} elseif ($numbers[$k]==1) {
					$other_number = $i;
				}
			}
			$this->supplement = array(
				'pair_numbers' => $pair_numbers,
				'other_number' => $other_number	
			);
		} elseif ($report["flags"]["one_pair"]) {
			$this->level = 1;
			$this->name = "ワンペア";
			$pair_number = null;
			$other_numbers = array();
			for ($i=14;$i>=2;$i--) {
				$k = $i;
				if ($i==14) {
					$k = 1;
				}
				if ($numbers[$k]==2) {
					$pair_number = $i;
				} elseif ($numbers[$k]==1) {
					array_push( $other_numbers , $i );
				}
			}
			$this->supplement = array(
				'pair_number' => $pair_number,
				'other_numbers' => $other_numbers
			);
		} else {
			$this->level = 0;
			$this->name = "ハイカード";
			$other_numbers = array();
			for ($i=14;$i>=2;$i--) {
				$k = $i;
				if ($i==14) {
					$k = 1;
				}
				if ($numbers[$k]==1) {
					array_push( $other_numbers , $i );
				}
			}
			$this->supplement = array(
				'numbers' => $other_numbers
			);
		}
	}
	
	public function win_for($hand) {
		$judge = null;
		$my_level = $this->level();
		$you_level = $hand->level();
		if ($my_level > $you_level) {
			$judge = true; // 勝ち
		} elseif ($my_level < $you_level) {
			$judge = false; // 負け
		} else {
			// 引き分けの場合は、詳細を比較
			$my_supplement = $this->supplement;
			$you_supplement = $hand->supplement;
			switch ($my_level) {
				case 10:
					if ($my_supplement['mark'] < $you_supplement['mark']) {
						$judge = true;					
					} elseif ($my_supplement['mark'] > $you_supplement['mark']) {
						$judge = false;
					}
					break;
				case 9:	
					if ($my_supplement['number'] > $you_supplement['number']) {
						$judge = true;
					} elseif ($my_supplement['number'] < $you_supplement['number'] ) {
						$judge = false;
					} else {
						if ($my_supplement['mark'] < $you_supplement['mark']) {
							$judge = true;
						} elseif ($my_supplement['mark'] > $you_supplement['mark']) {
							$judge = false;
						}
					}
					break;
				case 8:
					if ($my_supplement['number'] > $you_supplement['number']) {
						$judge = true;
					} elseif ($my_supplement['number'] < $you_supplement['number'] ) {
						$judge = false;
					}
					break;
				case 7:
					if ($my_supplement['number'] > $you_supplement['number']) {
						$judge = true;
					} elseif ($my_supplement['number'] < $you_supplement['number'] ) {
						$judge = false;
					}
					break;
				case 6:
					for ($i=0;$i<5;$i++) {
						$my_number = $my_supplement['numbers'][$i];
						$you_number = $you_supplement['numbers'][$i];
						if ($my_number > $you_number ) {
							$judge = true;
							break;
						} elseif ($my_number < $you_number ) {
							$judge = false;
							break;
						}
					}
					break;
				case 5:
					break;
				case 4:
					if ($my_supplement['number'] > $you_supplement['number']) {
						$judge = true;
					} elseif ($my_supplement['number'] < $you_supplement['number'] ) {
						$judge = false;
					}
					break;
				case 3:
					if ($my_supplement['number'] > $you_supplement['number']) {
						$judge = true;
					} elseif ($my_supplement['number'] < $you_supplement['number'] ) {
						$judge = false;
					}
					break;
				case 2:
					for ($i=0;$i<2;$i++) {
						$my_number = $my_supplement['pair_numbers'][$i];
						$you_number = $you_supplement['pair_numbers'][$i];
						if ($my_number > $you_number ) {
							$judge = true;
							break;
						} elseif ($my_number < $you_number ) {
							$judge = false;
							break;
						}
					}
					if (!isset($judge)) {
						if ($my_supplement['other_number'] > $you_supplement['other_number']) {
							$judge = true;
						} elseif ($my_supplement['other_number'] < $you_supplement['other_number'] ) {
							$judge = false;
						}
					}
					break;
				case 1:
					if ($my_supplement['pair_number'] > $you_supplement['pair_number']) {
						$judge = true;
					} elseif ($my_supplement['pair_number'] < $you_supplement['pair_number'] ) {
						$judge = false;
					} else {
						for ($i=0;$i<3;$i++) {
							$my_number = $my_supplement['other_numbers'][$i];
							$you_number = $you_supplement['other_numbers'][$i];
							if ($my_number > $you_number ) {
								$judge = true;
								break;
							} elseif ($my_number < $you_number ) {
								$judge = false;
								break;
							}
						}
					}
					break;
				case 0:
					for ($i=0;$i<5;$i++) {
						$my_number = $my_supplement['numbers'][$i];
						$you_number = $you_supplement['numbers'][$i];
						if ($my_number > $you_number ) {
							$judge = true;
							break;
						} elseif ($my_number < $you_number ) {
							$judge = false;
							break;
						}
					}
					break;
			}
		}
		return $judge;
	}
	
}

?>