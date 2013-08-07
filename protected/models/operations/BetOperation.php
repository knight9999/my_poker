<?php 

class BetOperation extends BaseGameOperation {
	public $name = "Bet";

	public function actionStartBetTurn() {
		$this->setCurrentView( null );
		$this->setNextProcess( "selectCommand");
	}

	public function actionSelectCommand() {
	
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
	
		if ( ! $player->isPlayableRound() || $player->flagFinalCall ) { 
			$this->setCurrentView( null );
			$this->setNextProcess( "goNextBet");
			return;
		}
	
		if ($player->isPerson()) {
			if ($charman->lastBetPlayer==null) { 
				$this->showView();
				$this->currentView['text'] = "どうしますか？";
				$buttons = array();
				if ($player->coins>0) {
					array_push( $buttons , array('ベット（掛ける)' , "bet") );
				} else {
					array_push( $buttons , array('ファイナルベット' , "f-bet") );
				}
				array_push( $buttons , array( 'フォールド（降りる)' , "fold") );
				$this->currentView["buttons"] = $buttons;
				$this->setNextProcess( "commandDo" );
			} else {
				$needCoins = $charman->currentFieldCoins - $player->fieldCoins;
				$buttons = array();
				if ($player->coins > $needCoins) {
					array_push( $buttons , array( 'レイズ（上乗せ）' , "raise") );
				}
				if ($player->coins >= $needCoins) {
					array_push( $buttons , array( 'コール（勝負）' , "call" ) );
				} else {
					array_push( $buttons , array( 'ファイナルコール（勝負）' , "f-call" ) );
				}
				array_push( $buttons , array( 'フォールド（降りる）' , "fold") );
				
				$this->showView();
				$this->currentView['text'] = "どうしますか？";
				$this->currentView["buttons"] = $buttons;
				$this->setNextProcess( "commandDo2" );
			}
		} elseif ($player->isCom()) {
			if ($charman->lastBetPlayer==null) { // COMから最初にかける場合
				$com = new ComLogic( $charman , $player );
				$hash = $com->selectCommand1();
				if ($hash["action"] == "bet") {
					$how = $hash["coins"];
					$player->betCoin( $how );
					$this->showView();
					$charman->lastBetPlayer = $player;
					$charman->currentFieldCoins += $how;
					$this->currentView["text"] = $player->name . "はベットしました。\n" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";;
					$this->setNextProcess("goNextBet");
					return;
				} elseif ($hash["action"] == "f-bet") {
					$how = $player->coins;
					$player->betCoin( $how );
					$this->showView();
					$player->flagFinalCall = true;
					$charman->lastBetPlayer = $player;
					$charman->currentFieldCoins += $how;
					$this->currentView["text"] = $player->name . "はファイナルベットしました。\n" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";;
					$this->setNextProcess("goNextBet");
					return;
				} elseif ($hash["action"] == "fold") {
					$player->flagFold = true;
					$this->showView();
					$this->currentView["text"] = $player->name . "はフォールドします。";
					$this->setNextProcess( "goNextBet");
					return;
				}
			} else {
				$com = new ComLogic( $charman , $player );
				$hash = $com->selectCommand2();
				if ($hash["action"] == "call") { // コールの場合  TODO コインが足りない場合の処理（ComLogic側で、コインが足りない場合はコールしないようになっているが、、、）
					$coins = $charman->currentFieldCoins - $player->fieldCoins;
					$player->betCoin( $coins );
					$this->showView();
					$this->currentView["text"] = $player->name . "はコールしました。\n" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";;
					$this->setNextProcess("goNextBet");
					return;
					
				} elseif ($hash["action"] == "f-call") { 
					$coins = $player->coins;
					$player->betCoin( $coins );
					$player->flagFinalCall = true;
					$this->showView();
					$this->currentView["text"] = $player->name . "はファイナルコールしました。\n" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";;
					$this->setNextProcess("goNextBet");
					return;
				} elseif ($hash["action"] == "fold") {
					$player->flagFold = true;
					$this->showView();
					$this->currentView["text"] = $player->name . "はフォールドします。";
					$this->setNextProcess( "goNextBet");
					return;
				} elseif ($hash["action"] == "raise") {
					$coins = $charman->currentFieldCoins - $player->fieldCoins + $hash["coins"];
					$player->betCoin( $coins );
					$charman->lastBetPlayer = $player;
					$charman->currentFieldCoins += $hash["coins"];
					$this->showView();
					$this->currentView["text"] = $player->name . "はレイズ(" . $hash["coins"] . ")しました。\n" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";;
					$this->setNextProcess("goNextBet");
					return;
				}
			}
				
			$this->setCurrentView(null);
			$this->setNextProcess( $this->externalError() );
		}
	}
	
	
	
	/*
	 * 人間の手番で、「かける」か「降りるか」の判断用。
	*/
	public function actionCommandDo() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		if ($player->isPerson()) {
			if ($this->input('answer') == 'bet') {
				$this->setCurrentView( null );
				$this->setNextProcess( "bet" );
				return;
			} elseif ($this->input('answer') == 'f-bet') {
				$this->setCurrentView( null );
				$this->setNextProcess( "finalBet" );
				return;
			} elseif ($this->input('answer') == 'fold') {
				$player->flagFold = true;
				$this->setCurrentView( null );
				$this->setNextProcess( "goNextBet");
				return;
			}
		}
	}
	
	/*
	 * 人間の手番で、「コール」か「フォールド」「レイズ」
	*　の判断用。
	*/
	public function actionCommandDo2() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		if ($player->isPerson()) {
			if ($this->input('answer') == 'call') { //TODO コインが足りない場合は、コールは出来ないようにする。（代わりにファイナルコールは可能）
				$this->setCurrentView( null );
				$this->setNextProcess( "call" );
				return;
			} elseif ($this->input('answer') == 'f-call') { //TODO コインがないときのみ使用可能
				$this->setCurrentView( null );
				$this->setNextProcess( "finalCall" );
				return;
			} elseif ($this->input('answer') == 'fold') {
				$player->flagFold = true;
				$this->setCurrentView( null );
				$this->setNextProcess( "goNextBet");
				return;
			} elseif ($this->input('answer') == 'raise') { //TODO コインが足りない場合は、コールは出来ないようにする。（代わりにファイナルコールは可能）
				$this->setCurrentView( null );
				$this->setNextProcess( "raise");
			}
		}
	}
	
	public function actionBet() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
	
		if ($player->isPerson()) {
			$this->showView();
			$this->currentView['text'] = "いくら掛けますか？";
			$this->currentView['form'] = array(
					array( 'type'=>'input' , 'name'=>'how', 'before' => '' , 'after' =>'枚' , 'size' => 4 )	,
					array( 'type'=>'submit' , 'name'=>"bet")
			);
			$this->currentView["buttons"] = array(
					array( 'やっぱり降りる' , "fold")
			);
			$this->setNextProcess( "betDo");
		}
	}
	
	public function actionBetDo() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
	
		if ($player->isPerson()) {
			if ($this->input('answer') == 'fold') {
				$player->flagFold = true;
				$this->setCurrentView( null );
				$this->setNextProcess( "goNextBet");
				return;
			}
				
			$how = (string) $this->input('how');
			if ($how == 0) {
				$this->showView();
				$this->currentView['text'] = "1枚～5枚の範囲で掛けてください";
				$this->setNextProcess("bet");
				return;
			} elseif ($how > 5) {
				$this->showView();
				$this->currentView['text'] = "1枚～5枚の範囲で掛けてください";
				$this->setNextProcess("bet");
				return;
			}
				
			if ($how > $player->coins) {
				$this->showView();
				$this->currentView['text'] = "そんなにコインがありません";
				$this->setNextProcess("bet");
				return;
			}
			$player->betCoin($how);
			$charman->lastBetPlayer = $player;
			$charman->currentFieldCoins += $how;
	
			$this->setCurrentView( null );
			$this->setNextProcess( "goNextBet");
		}
	}
	
	public function actionFinalBet() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		
		if ($player->isPerson()) {
			$how = $player->coins;
			$player->betCoin( $how );
			$player->flagFinalCall = true;
			$charman->lastBetPlayer = $player;
			$charman->currentFieldCoins += $how;
			$this->setCurrentView( null );
			$this->setNextProcess( "goNextBet");
		}
	}
	
	public function actionCall() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		$coins = $charman->currentFieldCoins - $player->fieldCoins;
		$player->betCoin( $coins );
		$this->showView();
		$this->currentView["text"] =  $player->name . "はコールしました。" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";
		$this->setNextProcess("goNextBet");
	}

	public function actionFinalCall() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		$coins = $player->coins;
		$player->betCoin( $coins );
		$player->flagFinalCall = true;
		$this->showView();
		$this->currentView["text"] =  $player->name . "はファイナルコールしました。" . "掛け金はコイン" . ($player->fieldCoins ) . "枚です。\n";
		$this->setNextProcess("goNextBet");
	}
	
	public function actionRaise() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		$this->showView();
		$this->currentView['text'] = "いくら掛けますか？";
		$this->currentView['form'] = array(
				array( 'type'=>'input' , 'name'=>'how', 'before' => '' , 'after' =>'枚' , 'size' => 4 )	,
				array( 'type'=>'submit' , 'name'=>"bet")
		);
			$this->currentView["buttons"] = array(
					array( 'コールする' , "call"),
					array( 'フォールドする' , "fold")
			);
		$this->setNextProcess( "raiseDo");
	}
	
	public function actionRaiseDo() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		
		if ($player->isPerson()) {
			if ($this->input('answer') == 'fold') {
				$player->flagFold = true;
				$this->setCurrentView( null );
				$this->setNextProcess( "goNextBet");
				return;
			} elseif ($this->input('answer') == 'call' ) {
				$this->setCurrentView( null );
				$this->setNextProcess("call");
				return;
			}
			$how = (string) $this->input('how');
			if ($how == 0) {
				$this->showView();
				$this->currentView['text'] = "1枚～5枚の範囲で掛けてください";
				$this->setNextProcess("raise");
				return;
			} elseif ($how > 5) {
				$this->showView();
				$this->currentView['text'] = "1枚～5枚の範囲で掛けてください";
				$this->setNextProcess("raise");
				return;
			}
		
			$coins = $charman->currentFieldCoins - $player->fieldCoins + $how;
			if ($coins > $player->coins) {
				$this->showView();
				$this->currentView['text'] = "そんなにコインがありません";
				$this->setNextProcess("raise");
				return;
			}
			$player->betCoin( $coins );
			$charman->lastBetPlayer = $player;
			$charman->currentFieldCoins += $how;
			$this->showView();
			$this->currentView["text"] = $player->name . "はレイズ(" . $how . ")しました。\n" . "掛け金はコイン" . ($player->fieldCoins  ) . "枚です。\n";;
			$this->setNextProcess( "goNextBet");
		}
	}
	
	public function actionGoNextBet() {
		$charman = $this->main["charman"];
	
		$flagEnd = false;
	
		if ($charman->lastBetPlayer == $charman->nextPlayer) {
			$flagEnd = true;
		}
		$aliveNum = 0;
		foreach ($charman->players as $player) {
			if ( $player->isPlayableRound() &&  ! $player->flagFinalCall) {
				$aliveNum += 1;
			}
		}
		if ($aliveNum == 1) {
			$flagEnd = true;
		}
	
		if ($flagEnd) {
			$charman->goNextPlayer();
			$this->setCurrentView( null );
			$this->setNextProcess( $this->externalJudge() );
		} else {
			$charman->goNextPlayer();
			$this->setCurrentView( null );
			$this->setNextProcess( "selectCommand" );
		}
	}
	
	public function externalJudge() {
		return "Main/judge";
	}
	
	public function externalError() { //TODO とりあえず、不整合がおこったらroundの開始に戻させる。
		return "Main/startRound";
	}

	public function showView() {
		$this->engine->showView();
	}
	
}

?>