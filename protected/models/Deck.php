<?php 

class Deck extends CComponent {
	public $data;
	
	public function init() {
		$this->setDeck();
	}
	
	public function setDeck() {
		$data = array();
		for ($m=1;$m<=4;$m++) {
			for ($i=1;$i<=13;$i++) {
				array_push( $data , new Card( array( 'mark' => $m , 'number' => $i ) ) );
			}
		}
		$this->data = $data;
	}
	
	public function clearDeck() {
		$this->data = array();
	}
	
	public function addCard( $card ) {
		array_push( $this->data , $card );
	}
	
	public function drawCard() {
		return array_pop( $this->data );
	}
	
	public function getCount() {
		return count( $this->data );
	}
	
	public function shuffle() {
		$tmp_data = $this->data;		
		$new_data = array();
		while ( count($tmp_data)>0 ) {
			$n = rand(1,count($tmp_data));
			$card = $tmp_data[$n-1];
			unset( $tmp_data[$n-1]);
			$tmp_data = array_values( $tmp_data );
			array_push( $new_data , $card );
		}
		$this->data = $new_data;
	}
	
	public function search( $card ) {
		
		for ($i=0;$i< count($this->data);$i++ ) {
			if ($this->data[$i]->equals($card) ) {
				return $i;
			}
		}
		return -1;
	}
	
	public function delete( $card ) {
		$i = $this->search($card);
		$target = null;
		if ($i>=0) {
			$target = $this->data[$i];
			array_splice( $this->data , $i , 1 );
		}
		return $target;
	}
	
	
}

?> 