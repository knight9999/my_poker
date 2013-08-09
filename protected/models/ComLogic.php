<?php 

class ComLogic extends CComponent {
	public $charman;
	public $player;
	
	function __construct($m,$p) {
		$this->charman = $m;
		$this->player = $p;
	}
	
	public function decidePlan1() {
		$cards = $this->player->cards;
		$hand = new PokerHand($cards);
		$level = $hand->level();
		
		$fault_value = array( 90 , 10 , 3 , 1 , 0 , 0 , 0 , 0 , 0 , 0 , 0 );
		
		$r = GRand::rand(1,100);
		if ($fault_value[$level] > $r ) { 
			$p = 3; // フォールト
		} else {
			$p = 1; // ベット
		} 

		return $p;
	}
	
	public function decidePlan2() {
		$cards = $this->player->cards;
		$hand = new PokerHand($cards);
		$level = $hand->level();
		$fault_value = array( 40 , 5  , 1  , 0  , 0  , 0  , 0  , 0  , 0  , 0  ,   0 );
		$call_value = array( 100 , 50 , 30 , 25 , 25 , 20 , 15 , 15 , 10 , 10 ,   5 );
		
		$r = GRand::rand(1,100);
		if ($fault_value[$level] >= $r ) {
			$p = 3; // フォールト
		} elseif ($call_value[$level] >= $r ) {
			$p = 1; // コール
		} else {
			$p = 2; // レイズ
		}
//		$p=2; //  強制的にレイズ
		return $p;
	}
	
	public function selectCommand1($p = null) {
		$charman = $this->charman;
		if (isset($p)) {
			$plan = $p;
		} else {
			$plan = $this->decidePlan1();
		}
		$action = null;
		switch ($plan) {
			case 1: // ベット
				$needCoins = 1;
				if ($needCoins <= $this->player->coins) {
					$action = $this->actionBet();
				} else { // 残りコインがない場合はファイナルコールにする
					$action = $this->actionFinalBet();
				}
				break;
			case 3: // フォールド
				$action = $this->actionFold();
				break;
		}
		return $action;
		
	}
	
	
	public function selectCommand2($p = null) {
		$charman = $this->charman;
		if (isset($p)) {
			$plan = $p;
		} else {
			$plan = $this->decidePlan2();
		}
		$action = null;
		switch ($plan) {
			case 1: // コール
				$needCoins = $charman->currentFieldCoins - $this->player->fieldCoins; 
				if ($needCoins <= $this->player->coins) {
					$action = $this->actionCall();
				} else { // 残りコインがない場合はファイナルコールにする
					$action = $this->actionFinalCall();
				}
				break;
			case 2: // レイズ
				$needCoins = $charman->currentFieldCoins - $this->player->fieldCoins;
				if ($needCoins < $this->player->coins) {
					$action = $this->actionRaise();
				} elseif ($needCoins == $this->player->coins) {
					$action = $this->actionCall(); // コインがぴったりしかない場合はコールに変更
				} else { // 残りコインがない場合はファイナルコールにする
					$action = $this->actionFinalCall();
				}
				break;
			case 3: // フォールド
				$action = $this->actionFold();
				break;
		}
		return $action;
	}

	public function actionBet() {
		$coins = rand(1,5);
		if ($coins > $this->player->coins) {
			$coins = $this->player->coins;
		}
		$action = array( "action" => "bet" , "coins" => $coins );
		return $action;
	}
	
	public function actionFinalBet() {
		$action = array( "action" => "f-bet" );
		return $action;
	}
	
	public function actionCall() {
		$action = array( "action" => "call" );
		return $action;
	}
	
	public function actionFinalCall() {
		$action = array( "action" => "f-call" );
		return $action;
	}
	
	public function actionFold() {
		$action = array( "action" => "fold" );
		return $action;
	}
	
	public function actionRaise() {
		$charman = $this->charman;
		$needCoins = $charman->currentFieldCoins - $this->player->fieldCoins;
		$coins = rand(1,5);
		if ($needCoins + $coins > $this->player->coins) {
			$coins = $this->player->coins - $needCoins; // 持ちコインより多い場合は、持ちコインを上限にする
		}
		$action  = array( "action" => "raise" , "coins" => $coins);
		return $action;
	}
	
	public function changeCards() {
		$condition = $this->calcCondition();		
		// その他の場合
		return $this->changeCardsByRandom($condition);
	}

	
	public function calcCondition() {
		$cards = $this->player->cards;
		$hand = new PokerHand($cards);
		$report = $hand->analyse();
		
		$exceptions = null;
		$force = false;
				
		if (count($report["flags"]) == 1 && $report["flags"]["one_pair"]) { // ワンペアしか役がない場合の戦略
			$exceptions = array();
			for($i=0;$i<5;$i++) {
				if ( $cards[$i]->number == $report["info"]["one_pair_number"] ) {
					array_push( $exceptions , $i );
				}
			}
		} elseif ($report["flags"]["two_pairs"]) { // ツーペアの場合の戦略
			$exceptions = array();
			for($i=0;$i<5;$i++) {
				if (in_array( $cards[$i]->number , $report["info"]["two_pairs_numbers"] ) ) {
					array_push( $exceptions , $i );
				}
			}
		} elseif (count($report["flags"]) == 1 && $report["flags"]["threecard"]) { // スリーカードのみの場合の戦略
			$exceptions = array();
			for($i=0;$i<5;$i++) {
				if ( $cards[$i]->number == $report["info"]["threecard_number"] ) {
					array_push( $exceptions , $i );
				}
			}
		} elseif ($report["flags"]["fourcard"]) { // フォーカードの場合の戦略
			$exceptions = array();
			for($i=0;$i<5;$i++) {
				if ( $cards[$i]->number == $report["info"]["fourcard_number"] ) {
					array_push( $exceptions , $i );
				}
			}
		} elseif ($report["flags"]["fullhouse"] || $report["flags"]["straight"] ||  // フルハウス、ストレート、フラッシュ、ロイヤルストレートフラッシュのときは交換しない。
				$report["flags"]["royal_straight"] || $report["flags"]["flash"] ||
				$report["flags"]["straight_flash"] || $report["flags"]["royal_straight_flash"]) {
			$exceptions = array();
			for($i=0;$i<5;$i++) {
				array_push( $exceptions , $i );
			}
		}
		
		if (!isset( $exceptions ) ) { // ハイカードの場合
			$aim_flash = null;
			$aim_straight = null;
			
			for ($k=1;$k<=4;$k++) {
				if ($report["marks"][$k] == 4) { // 4枚マークがそろっている場合 => フラッシュ狙い
					$aim_flash = array("mark" => $k);
				}
			}
			
			if ($report["continuous"][1] >= 4) { // あと一枚で（ロイヤル）ストレート => （ロイヤル）ストレート狙い	
				$aim_straight = array("number" => 1);
			} else {
				for ($k=13-4;$k>=1;$k--) {
					if ($report["continuous"][$k+4] >= 4) {// あと一枚でストレート => ストレート狙い	
						$aim_straight = array("number"=>$k+4);
					}
				}
			}
			
			
			
			
			if (isset($aim_flash)) {
				$exceptions = array();
				for($i=0;$i<5;$i++) {
					if ($cards[$i]->mark == $aim_flash["mark"]) {
						array_push( $exceptions , $i );
					}
				}
				$force = true;
			} elseif (isset($aim_straight)) {
				$exceptions = array();
				$l = array();
				
				if ($aim_straight["number"] == 1) { // ロイヤルストレートの場合
					$l = array( 10,11,12,13,1 );
				} else {
					for ($k=0;$k<5;$k++) {
						array_push( $l , $aim_straight["number"] - $k);
					}
				}
				for ($k=0;$k<5;$k++) {
					$t = $l[$k];
					for ($i=0;$i<5;$i++) {
						if ($cards[$i]->number == $t) {
							array_push( $exceptions , $i );
							break;								
						}
					}
				}
				$force = true;
			} else {
				if (GRand::rand(1,100)>50) { // 50パーセントの確率で、一番高いカード1毎を残す
					$exceptions = array();
					$highest = 0;
					for ($i=1;$i<5;$i++) {
						if ($cards[$i]->number > $cards[$highest]->number) {
							$highest = $i;
						}
					}
					array_push( $exceptions , $highest );
				}
			}
			
		}
		
		
		return array( "exceptions"=>$exceptions , "force" => $force);
	}
	
	public function changeCardsByRandom( $condition = null) { // exceptionsで指定したカード以外をランダムで交換
		$rcount = 0;
		
		$exceptions = null;
		$force = null;
		if (isset($condition)) {
			$exceptions = $condition["exceptions"];
			$force      = $condition["force"];
		}
		
		$cards = array();
		$changeCount = 5;
		if (isset($exceptions)) {
			$changeCount = 5 - count( $exceptions );
		}
		for ($i=0;$i<5;$i++ ) {
			if (! isset($exceptions) || ! in_array($i,$exceptions)) {
				array_push( $cards , $i );
			}
		}
		if ($force) {
			$n = $changeCount;
		} else {
			if (GRand::rand(1,100)>50) { // 50パーセントの確率で、全部交換 
				$n = $changeCount;
			} elseif (Grand::rand(1,100)>50) { // さらに50パーセントの確率で、１枚残して交換
				$n = $changeCount - 1;
				if ($n<0) { $n = 0; }
			} else { 
				$n = GRand::rand(0,$changeCount); // 何枚交換するか
			}
		}
		$res = array();
		for ($i=0;$i<$n;$i++) {
			$r = GRand::rand(0,$changeCount-1);  // どのカードを交換するか
			$num = $cards[$r];
			if (!in_array( $num , $res) ) {
				array_push( $res , $num );
			}
		}
		return $res;
	}
	
}


?>