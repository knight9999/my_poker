<?php 

class ChangeCardsOperation extends BaseGameOperation {
	public $name = "ChangeCards";
	
	
	public function actionStartChangeCardTurn() {
		$charman = $this->main["charman"];
		$charman->currentFieldCoins = 1;
		$charman->turnStart();
	
		$this->setCurrentView( null );
		$this->setNextProcess( "changeCard" );
	}
		
	public function actionChangeCard() {
	
		$charman = $this->main["charman"];
	
		$player = $charman->currentPlayer;
		if ($player->flagDefault) {
			$this->showView();
			$this->currentView['text'] = $player->name . "はゲームから脱落しているため、パスします。";
			if ($charman->isTurnEnd()) {
				$charman->goNextPlayer();
				$charman->turnStart();
				$this->setNextProcess( $this->externalStartBetTurn() );
			} else {
				$charman->goNextPlayer();
				$this->setNextProcess( "changeCard" );
			}
			return;
		}
		if ($player->isPerson()) {
			$this->showView();
			$this->currentView['text'] = "交換するカードを選択してください。";
			$this->currentView['select_card'] = true;
			$this->currentView['form'] = array(
					array( 'type'=>'submit' , 'name'=>"bet")
			);
			$this->setNextProcess( "changeCardDo");
		} elseif ($player->isCom()) {
			$this->showView();
				
			$com = new ComLogic( $charman , $player );
			$player->selectedCards = $com->changeCards();
			$this->currentView['com_select_card'] = $player->selectedCards;
				
			$this->currentView['text'] = "COMが交換するカードが決まりました";
			$this->setNextProcess( "comChangeCardDo");
		}
	}
	
	public function actionChangeCardDo() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
		if ($player->isPerson()) {
			$select = $this->input('select_card');
			if (! isset($select)) {
				$this->falseResult("入力値が不正です。");
				return ;
			}
			// TODO カード交換中に、デッキがなくなった場合の処理を追加する。
			$deck = $charman->deck;
			$arr = explode( "," , $select );
			$ct = 0;
			foreach ($arr as $snum) {
				if (preg_match("/\d+/",$snum)) {
					$num = (int) $snum;
					if ($num>=0 && $num<5) {
						$player->exchangeCard($num,$deck);
						$ct += 1;
					}
				}
			}
			$this->showView();
			if ($ct>0) {
				$this->currentView['text'] = $ct . "枚のカードを交換しました。";
			} else {
				$this->currentView['text'] = "カードは交換しません。";
			}
				
			if ($charman->isTurnEnd()) {
				$charman->goNextPlayer();
				$charman->turnStart();
				$this->setNextProcess( $this->externalStartBetTurn() );
			} else {
				$charman->goNextPlayer();
				$this->setNextProcess( "changeCard" );
			}
		}
	}
	
	public function actionComChangeCardDo() {
		$charman = $this->main["charman"];
		$player = $charman->currentPlayer;
	
		if ($player->isCom()) {
			$deck = $charman->deck;
				
			// TODO カード交換中に、デッキがなくなった場合の処理を追加する。
			$exchanges = $player->selectedCards;
			$player->selectedCards = array();
				
			foreach ($exchanges as $i) {
				$player->exchangeCard($i , $deck);
			}
				
			$this->showView();
			$num = count( $exchanges );
				
			if ($num>0) {
				$this->currentView['text'] = $player->name . "が" . $num . "枚のカードを交換しました";
			} else {
				$this->currentView['text'] = $player->name . "はカードの交換をしません";
			}
				
			if ($charman->isTurnEnd()) {
				$charman->goNextPlayer();
				$charman->turnStart();
				$this->setNextProcess( $this->externalStartBetTurn() );
			} else {
				$charman->goNextPlayer();
				$this->setNextProcess( "changeCard" );
			}
		}
	}
	
	public function externalStartBetTurn() {
		return "Bet/startBetTurn";
	}
	
	public function showView() {
		$this->engine->showView();
	}
	
	
}

?>