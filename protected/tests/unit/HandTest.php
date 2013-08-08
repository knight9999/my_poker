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
		
		// straight test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 1 ) ), // スペード1
				new Card( array( 'mark' => 2 , 'number' => 2 ) ), // ハート2
				new Card( array( 'mark' => 3 , 'number' => 3 ) ), // ダイア3
				new Card( array( 'mark' => 2 , 'number' => 4 ) ), // ハート4
				new Card( array( 'mark' => 4 , 'number' => 5 ) )  // クラブ5
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[4] ); // straight
		
		// royal straight test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 12 ) ), // スペード12
				new Card( array( 'mark' => 2 , 'number' => 1 ) ), // ハート1
				new Card( array( 'mark' => 3 , 'number' => 13 ) ), // ダイア13
				new Card( array( 'mark' => 2 , 'number' => 11 ) ), // ハート11
				new Card( array( 'mark' => 4 , 'number' => 10 ) )  // クラブ10
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[5] ); // royal straight
		
		
		// flush test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ), // ハート3
				new Card( array( 'mark' => 2 , 'number' => 7 ) ), // ハート7
				new Card( array( 'mark' => 2 , 'number' => 8 ) ), // ハート8
				new Card( array( 'mark' => 2 , 'number' => 9 ) ), // ハート9
				new Card( array( 'mark' => 2 , 'number' => 12 ) )  // ハート12
		);
		$hand = new PokerHand( $cards );
		$report = $hand->analyse("combination");
//		print $report["flags"]["flash"];
		$this->assertTrue( $report["flags"]["flash"] );

		$this->assertEquals( $hand->levelName() ,  PokerHand::$names[6] ); // flush test

		
		
		// fullhouse test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ), // ハート3
				new Card( array( 'mark' => 1 , 'number' => 3 ) ), // スペード3
				new Card( array( 'mark' => 1 , 'number' => 8 ) ), // スペード8
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 4 , 'number' => 8 ) )  // クラブ8
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[7] ); // fullhouse test
		
		
		// four cards test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 8 ) ), // ハート8
				new Card( array( 'mark' => 1 , 'number' => 3 ) ), // スペード3
				new Card( array( 'mark' => 1 , 'number' => 8 ) ), // スペード8
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 4 , 'number' => 8 ) )  // クラブ8
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[8] ); // four cards test

		// straight flush test
		$cards = array(
				new Card( array( 'mark' => 3 , 'number' => 7 ) ), // ダイア7
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 3 , 'number' => 9 ) ), // ダイア9
				new Card( array( 'mark' => 3 , 'number' => 10 ) ), // ダイア10
				new Card( array( 'mark' => 3 , 'number' => 11 ) )  // ダイア11
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[9] ); // straight flush test

		// royal straight flush test
		$cards = array(
				new Card( array( 'mark' => 4 , 'number' => 10 ) ), // クラブ10
				new Card( array( 'mark' => 4 , 'number' => 11 ) ), // クラブ11
				new Card( array( 'mark' => 4 , 'number' => 12 ) ), // クラブ12
				new Card( array( 'mark' => 4 , 'number' => 13 ) ), // クラブ13
				new Card( array( 'mark' => 4 , 'number' => 1 ) )   // クラブ1
		);
		$hand = new PokerHand( $cards );
		$this->assertTrue( $hand->levelName() == PokerHand::$names[10] ); // royal straight flush test
		
		
//		$this->assertTrue( true );
	}
	
	public function testAnalyse() {
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 1 , 'number' => 5 ) ),
				new Card( array( 'mark' => 3 , 'number' => 8 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$report = $hand->analyse("basic");
		$this->assertTrue( $report["checked"]["basic"]  );
		$this->assertEquals( $report["numbers"][3] , 2);
		$this->assertEquals( $report["marks"][1] , 2);
		$this->assertEquals( $report["marks"][4] , 1);
		
		
		$report = $hand->analyse("marks");
		$this->assertTrue( $report["checked"]["marks"]  );

		$report = $hand->analyse("numbers");
		$this->assertTrue( $report["checked"]["numbers"]  );
		
	}
}

?>