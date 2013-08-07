<?php  
class PokerPlayer extends CComponent {

	public $name;
	public $coins;
	public $cards;
	public $fieldCards;
	public $fieldCoins;
	public $playerType;
	public $algorithm;
	public $selectedCards;
	
	public $flagFold;
	public $flagDefault; // デフォルト（以後のラウンドでゲームに参加出来ない）
	public $flagFinalCall;
	
	public function isCom() {
		return ($this->playerType == "COM");
	}
	
	public function isPerson() {
		return ($this->playerType == "PERSON");
	}
	
	public function drawCards5($deck) { // カードを5毎引く
		for ($i=0;$i<count($this->cards);$i++) {
			$this->dropCard($i);
		}	
		// ここで、カードをクリアする
		$this->cards = array();
		for ($i=0;$i<5;$i++) {
			array_push( $this->cards , $deck->drawCard() );
		}
	}
	
	public function dropCard($i) {
		$card = $this->cards[$i];
		if ($card) {
			array_push( $this->fieldCards , $card );
			$this->cards[$i] = null;
		}
	}
	
	public function exchangeCard($i,$deck) {
		$this->dropCard($i);
		$this->cards[$i] = $deck->drawCard();
	}
	
	public function betCoin($n) { // $n: コインの枚数
		$this->coins -= $n;
		$this->fieldCoins += $n;
	}
	
	public function isPlayableRound() { // ラウンドに参加出来るかどうか
		return (! $this->flagFold && ! $this->flagDefault);
	}
	
	public function getTotalCoins() {
		return $this->coins + $this->fieldCoins;
	}
	
}
	
?>