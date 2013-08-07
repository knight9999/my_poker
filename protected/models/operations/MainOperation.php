<?php 

class MainOperation extends BaseGameOperation {
	public $name = "Main";
	
	public function actionGameInitialize() {
	
		$charman = new Charman();
		$charman->round = 1;
		$charman->deck = null; // ここではまだ未設定
		$charman->players = array();  // ここではまだ未設定
	
		$com_player = new PokerPlayer();
		$com_player->coins = 20;
		$com_player->cards = array();
		$com_player->fieldCards = array();
		$com_player->fieldCoins = 0;
		$com_player->playerType = "COM";
		$com_player->name = "COM";
		$com_player->flagDefault = false;
	
		array_push( $charman->players , $com_player );
	
		$person_player = new PokerPlayer();
		$person_player->coins = 20;
		$person_player->cards = array();
		$person_player->fieldCards = array();
		$person_player->fieldCoins = 0;
		$person_player->playerType = "PERSON";
		$person_player->name = "あなた";
		$person_player->flagDefault = false;
	
		array_push( $charman->players , $person_player );
	
		$charman->dealerId = 0; // とりあえず、0:COM 1:YOU でどちらも可能。
		$charman->currentPlayerId = 1; // dealerの次の人から始めるので、これでよい。
	
		$this->main["charman"] = $charman;
	
		$this->setCurrentView( null );
		$this->setNextProcess( "startRound" );
	}
	
	public function actionStartRound() {
	
		$charman = $this->main["charman"];
	
		$deck = new Deck();
		$deck->init();
		$deck->shuffle();
		$charman->deck = $deck;
		$charman->lastBetPlayer= null; // これはいらないが、一応つけておく。
	
		$default_players = array(); // 今回デフォルトしたユーザー（のみ）を記録する
		foreach ($charman->players as $player) {
			$player->flagFold = false;
			$player->flagFinalCall = false;
			if ($player->coins == 0) { //場代がない場合
				$player->flagDefault = true;
				array_push( $default_players , $player );
			} else {
			}
		}
		if (count($default_players)>0) {
			$this->showView();
			$text = "";
			foreach($default_players as $dp) {
				$text .= $dp->name . "は場代がないため、ゲームから脱落しました。\n";
			}
			$this->currentView['text'] = $text;
		} else {
			$this->setCurrentView( null );
		}
		if ($charman->countPlayablePlayers <= 1) {
			$this->setNextProcess( "gameover" );
			return ;
		}
	
		$this->setNextProcess( "dealCards" );
	}
	
	public function actionDealCards() { // カードを5枚引くアクション
		$charman = $this->main["charman"];
		$deck = $charman->deck;
	
		if ($deck->count > 5) {
				
			for ($n=0;$n<count( $charman->players );$n++ ) {
				$player = $charman->players[$n];
				$player->drawCards5( $deck );
			}
				
			$this->showView();
			$this->currentView["buttons"] = array(
					array( '場代を払う' , "rake" )
			);
			$this->setNextProcess( "rake");
			return;
		}
		$this->setCurrentView( null );
		$this->setNextProcess( "endV" );
	}
	
	public function actionRake() {
		$charman = $this->main["charman"];
	
		foreach ($charman->players as $player) {
			if ($player->coins > 0) { //場代がある場合
				$player->betCoin(1);
			}
		}
		$this->setCurrentView( null );
		$this->setNextProcess( $this->externalStartChangeCardTurn() );
	}
	
	
	
	public function actionJudge() {
		$charman = $this->main["charman"];
	
		$charman->judge();
		$winner = $charman->players[ $charman->winnerId ];
	
		$result = null;
		if ($winner->isPerson()) {
			$result = true;
		} elseif ($winner->isCom()) {
			$result = false;
		}
	
		$message1 = "";
		$message2 = "";
		$charman->turnStart();
		while (true) {
			$currentPlayer = $charman->currentPlayer;
			$currentPlayerHand = new PokerHand( $currentPlayer->cards );
			if ($currentPlayer->isPlayableRound() ) {
				if ($currentPlayer->isPerson()) {
					$message1 .= $currentPlayer->name . "の手役：" . $currentPlayerHand->name() . "。";
				} elseif ($currentPlayer->isCom()) {
					$message2 .= $currentPlayer->name . "の手役:" . $currentPlayerHand->name() . "。";
				}
			}
			if ($charman->isTurnEnd()) { break; }
			$charman->goNextPlayer();
		};
		$message = $message1.$message2;
		if ($result==true) {
			$message .= "あなたの勝ちです。";
		} elseif ($result==false) {
			$message .= "あなたの負けです。";
		} else {
			$message .= "引き分けです。コインはディーラーに没収になります。";
		}
		$this->main['winnerId'] = $charman->winnerId;
		$this->showView();
		$this->currentView['text'] = $message;
		$this->setNextProcess("clearUp");
	}
	
	public function actionClearUp() {
		$charman = $this->main["charman"];
	
		$coinTrans = array();
		foreach ($charman->players as $player) {
			$coinTrans[ $charman->playerIdFor( $player ) ] = array( 'before' => $player->coins + $player->fieldCoins );
		}
		$winner = $charman->players[ $this->main['winnerId'] ];
		$this->calcCoins( $winner );
		foreach ($charman->players as $player) {
			$coinTrans[ $charman->playerIdFor( $player ) ]['after'] = $player->coins;
		}
		$this->showView();
		unset( $this->main['winnerId'] );
		$text = "清算しました\n\n";
		foreach ($charman->players as $player) {
			$ct = $coinTrans[ $charman->playerIdFor( $player ) ];
			$text .= $player->name . " : " . $ct['before'] . " => " . $ct['after'] . "\n";
		}
		$this->currentView['text'] = $text;
		$this->setNextProcess("roundEnd");
	}
	
	public function actionRoundEnd() {
		$charman = $this->main["charman"];
		$charman->round += 1;
		if ($charman->round > 4) {
			$this->currentView = null;
			$this->setNextProcess("gameOver");
			return;
		}
		$this->currentView = null;
		$this->setNextProcess( "startRound" );
	}
	
	
	public function actionEndV() {
		$view = new CMap( array(
				'message' => 'ポーカーゲーム',
				'text' => 'カードがなくなりました',
				'buttons' => array(
						array( '了解' , 's1' )
				)
		));
		$this->setCurrentView(  $view );
		$this->setNextProcess( "endF" );
	}
	
	public function actionEndF() {
		$this->setCurrentView(  null );
		$this->setNextProcess( $this->externalStart() );
	}
	
	public function actionGameOver() {
		$charman = $this->main["charman"];
	
		$this->backCoins();
	
		$winner = $charman->gameWinner;
		$text = "優勝者は" . $winner->name . "です。";
		$view = new CMap( array(
				'message' => 'ゲームオーバー',
				'text' => '勝敗がつきました。' . $text,
				'buttons' => array(
						array( '最初からゲームをやる' , 's1' )
				)
		));
		$this->setCurrentView(  $view );
		$this->setNextProcess( "endF" );
	}
	
	public function externalStart() {
		return "Menu/start";
	}
	
	public function externalStartChangeCardTurn() {
		return "ChangeCards/startChangeCardTurn";
	}
	
	public function calcCoins($winner) { //TODO このメソッドは、charmanに移行すべき
		$charman = $this->main["charman"];
	
		$fieldCoins = 0;
		foreach ($charman->players as $player) {
			$fieldCoins += $player->fieldCoins;
			$player->fieldCoins = 0;
		}
	
		$winner->coins += $fieldCoins;
		$charman->currentFieldCoins = 0;
	
	}
	
	public function backCoins() { //TODO このメソッドは、charmanに移行すべき
		$charman = $this->main["charman"];
		foreach ($charman->players as $player) {
			$player->coins += $player->fieldCoins;
			$player->fieldCoins = 0;
		}
	}
	
	public function showView() {
		$this->engine->showView();
	}
	
	
	
}

?>