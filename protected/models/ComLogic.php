<?php 

class ComLogic extends CComponent {
	public $charman;
	public $player;
	
	function __construct($m,$p) {
		$this->charman = $m;
		$this->player = $p;
	}
	
	public function decidePlan1() {
		$r = rand(1,4);
		$p = 3; // フォールド
		switch ($r) {
			case 1:
			case 2:
			case 3:
				$p=1; // ベット
				break;
			default :
		
		}
		return $p;
	}
	
	public function decidePlan2() {
		$r = rand(1,4);
		$p = 3; // フォールド
		switch ($r) {
			case 1:
			case 2:
				$p=1; // コール
			 	break;
			case 3:
				$p=2; // レイズ
				break;
			default :
				
		}
		$p=2; //  強制的にレイズ
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
		$n = rand(0,5); // 何枚交換するか
		$r = array();
		for ($i=0;$i<$n;$i++) {
			$num = rand(0,4);
			if (!in_array( $num , $r) ) {
				array_push( $r , $num );
			}
		}  
		return $r;
	}
	
}


?>