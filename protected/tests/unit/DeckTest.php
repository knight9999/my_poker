<?php 

class DeckTest extends CTestCase {
	
	public function testDeck() { // デッキの基本的なテスト
		
		$deck = new Deck();
		$deck->init();
		
		$this->assertEquals( $deck->count , 52 );
		
		$deck->clearDeck();
		
		$this->assertEquals( $deck->count , 0 );
		
		$deck->setDeck();

		$this->assertEquals( $deck->count , 52 );
		
		$deck->shuffle();
		
		$list = array( array(),array(),array(),array() );
		foreach ($deck->data as $card) {
			$list[$card->mark - 1 ][$card->number - 1] = true; 
		}
		for ($i=1;$i<=4;$i++) {
			for ($j=1;$j<=13;$j++) {
				$this->assertTrue( $list[$i-1][$j-1] );
			}
		}
		
	}
	
	public function testDelete() { // デッキからカードを削除する場合のテスト
		
		$deck = new Deck();
		$deck->init();
		$deck->shuffle();
		
		$card = $deck->delete( new Card( array( 'mark' => 3 , 'number' => 5 )));
		$this->assertTrue( $card->equals( new Card( array( 'mark' => 3 , 'number' => 5 ))) );

		$list = array( array(),array(),array(),array() );
		foreach ($deck->data as $tcard) {
			$list[$tcard->mark - 1 ][$tcard->number - 1] = true;
		}
		
		for ($i=1;$i<=4;$i++) {
			for ($j=1;$j<=13;$j++) {
				if ($card->equals( new Card( array( 'mark' => $i , 'number' => $j )))) {
					$this->assertTrue( ! isset( $list[$i-1][$j-1] ) );
				} else {
					$this->assertTrue( $list[$i-1][$j-1] );
				}
			}
		}
		
	}	
}

?>