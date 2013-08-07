<?php 
class Charman extends CComponent {
	public $round;
	public $deck;
	public $players;
	public $currentPlayerId;
	public $dealerId;
	public $currentFieldCoins; // 現在の場代+掛け金（一人あたり）
	public $winnerId;
	public $lastBetPlayer;
	
	public function getCurrentPlayer() {
		return $this->players[ $this->currentPlayerId ];
	}

	public function getDealer() {
		return $this->players[ $this->dealerId ];
	}
	
	public function isTurnEnd() {
		if ($this->currentPlayerId == $this->dealerId) {
			return true;
		}
		return false;
	}
	
	public function isTurnStart() { // ディーラーの次の人の順番のとき「ターンスタート」状態
		$nextToDealerId = $this->dealerId + 1;
		if ($nextToDealerId >= count($this->players) ) {
			$nextToDealerId = 0;
		}
		if ($nextToDealerId == $this->currentPlayerId) {
			return true;
		}
		return false;
	}
	
	public function goNextPlayer() {
		$this->currentPlayerId = $this->nextPlayerId;
	}
	
	public function getNextPlayerId() {
		$nextPlayerId = $this->currentPlayerId + 1;
		if ($nextPlayerId >= count($this->players) ) {
			$nextPlayerId = 0;
		}
		return $nextPlayerId;
	}
	
	public function getNextPlayer() {
		return $this->players[ $this->nextPlayerId];
	}
	
	public function playerIdFor($player) {
		return array_search( $player , $this->players );
	}
	
	public function turnStart() {
		$nextToDealerId = $this->dealerId + 1;
		if ($nextToDealerId >= count($this->players) ) {
			$nextToDealerId = 0;
		}
		$this->currentPlayerId = $nextToDealerId;
	}
	
	public function judge() {
		$winnerId = null; // $this->dealer;
		$currentId = $this->dealerId;
		do {
			$currentId = $currentId + 1;
			if ($currentId >= count( $this->players ) ) {
				$currentId = 0;
			}
		
			$currentPlayer = $this->players[$currentId];
			if ( $currentPlayer->isPlayableRound() ) { 
				if (isset($winnerId)) {
 					$winner = $this->players[$winnerId];
					$winnerHand = new PokerHand( $winner->cards );
 					$currentPlayerHand = new PokerHand( $currentPlayer->cards );
					if ($currentPlayerHand->win_for($winnerHand)) {
						$winnerId = $currentId;
					}
				} else {
					$winnerId = $currentId;
				}		
			}
		} while ($currentId != $this->dealerId);
		
		$this->winnerId = $winnerId;
	}
	
	public function getCountPlayablePlayers() {
		$count = 0;
		foreach ( $this->players as $player ) {
			if (! $player->flagDefault) {
				$count += 1;
			}
		}
		return $count;
	}
	
	public function getGameWinner() {
		$winner = null;
		$coins = 0;
		foreach ( $this->players as $player ) {
			if ($player->coins > $coins) {
				$coins = $player->coins;
				$winner = $player;
			}
		}
		return $winner;
	}
}

?>

