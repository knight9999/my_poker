<?php 

class HandTest extends CTestCase {
	
	public function testHand() {

		// one-pair test
		$cards = array( 
			new Card( array( 'mark' => 1 , 'number' => 3 ) ),
			new Card( array( 'mark' => 2 , 'number' => 3 ) ),
			new Card( array( 'mark' => 1 , 'number' => 5 ) ),
			new Card( array( 'mark' => 3 , 'number' => 8 ) ),
			new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$this->assertTrue( $hand->levelName() == PokerHand::$names[1] ); // one pair

		// two-pairs test
		$cards = array(
			new Card( array( 'mark' => 1 , 'number' => 3 ) ),
			new Card( array( 'mark' => 2 , 'number' => 3 ) ),
			new Card( array( 'mark' => 1 , 'number' => 5 ) ),
			new Card( array( 'mark' => 3 , 'number' => 5 ) ),
			new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$this->assertTrue( $hand->levelName() == PokerHand::$names[2] ); // two pairs

		// three test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 5 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$this->assertTrue( $hand->levelName() == PokerHand::$names[3] ); // three card
		
//		$this->assertTrue( true );
	}
	
}

?>