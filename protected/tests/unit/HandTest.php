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
		
		$this->assertEquals( PokerHand::$names[1],  $hand->levelName()  ); // one pair
		
		$this->assertEquals( 3, $hand->supplement['pair_number']  );
		$this->assertEquals( 9, $hand->supplement['other_numbers'][0]  );
		$this->assertEquals( 8, $hand->supplement['other_numbers'][1]  );
		$this->assertEquals( 5, $hand->supplement['other_numbers'][2]  );
		
		// two-pairs test
		$cards = array(
			new Card( array( 'mark' => 1 , 'number' => 3 ) ),
			new Card( array( 'mark' => 2 , 'number' => 3 ) ),
			new Card( array( 'mark' => 1 , 'number' => 5 ) ),
			new Card( array( 'mark' => 3 , 'number' => 5 ) ),
			new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$this->assertEquals( PokerHand::$names[2] , $hand->levelName()  ); // two pairs

		$this->assertEquals( 5, $hand->supplement['pair_numbers'][0]  );
		$this->assertEquals( 3, $hand->supplement['pair_numbers'][1]  );
		$this->assertEquals( 9, $hand->supplement['other_number']  );
		
		
		// three test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 5 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$hand = new PokerHand( $cards );
		
		$this->assertEquals( PokerHand::$names[3] , $hand->levelName()  ); // three card
		
		$this->assertEquals( 3 , $hand->supplement['number']  );
		
		// straight test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 1 ) ), // スペード1
				new Card( array( 'mark' => 2 , 'number' => 2 ) ), // ハート2
				new Card( array( 'mark' => 3 , 'number' => 3 ) ), // ダイア3
				new Card( array( 'mark' => 2 , 'number' => 4 ) ), // ハート4
				new Card( array( 'mark' => 4 , 'number' => 5 ) )  // クラブ5
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[4] , $hand->levelName()  ); // straight
		
		$this->assertEquals( 5, $hand->supplement['number']  );
		
		// royal straight test
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 12 ) ), // スペード12
				new Card( array( 'mark' => 2 , 'number' => 1 ) ),  // ハート1
				new Card( array( 'mark' => 3 , 'number' => 13 ) ), // ダイア13
				new Card( array( 'mark' => 2 , 'number' => 11 ) ), // ハート11
				new Card( array( 'mark' => 4 , 'number' => 10 ) )  // クラブ10
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[5] , $hand->levelName()  ); // royal straight
		
		// flush test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),  // ハート3
				new Card( array( 'mark' => 2 , 'number' => 7 ) ),  // ハート7
				new Card( array( 'mark' => 2 , 'number' => 8 ) ),  // ハート8
				new Card( array( 'mark' => 2 , 'number' => 9 ) ),  // ハート9
				new Card( array( 'mark' => 2 , 'number' => 12 ) )  // ハート12
		);
		$hand = new PokerHand( $cards );
		$report = $hand->analyse("combination");
		$this->assertTrue( $report["flags"]["flash"] );

		$this->assertEquals(  PokerHand::$names[6] , $hand->levelName() ); // flush test
		$this->assertEquals(  $hand->supplement['numbers'][0] , 12 );
		$this->assertEquals(  $hand->supplement['numbers'][1] , 9 );
		$this->assertEquals(  $hand->supplement['numbers'][2] , 8 );
		$this->assertEquals(  $hand->supplement['numbers'][3] , 7 );
		$this->assertEquals(  $hand->supplement['numbers'][4] , 3 );
		
		
		// fullhouse test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ), // ハート3
				new Card( array( 'mark' => 1 , 'number' => 3 ) ), // スペード3
				new Card( array( 'mark' => 1 , 'number' => 8 ) ), // スペード8
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 4 , 'number' => 8 ) )  // クラブ8
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[7] , $hand->levelName()  ); // fullhouse test
		
		$this->assertEquals( $hand->supplement['number'] , 8 );
		
		// four cards test
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 8 ) ), // ハート8
				new Card( array( 'mark' => 1 , 'number' => 3 ) ), // スペード3
				new Card( array( 'mark' => 1 , 'number' => 8 ) ), // スペード8
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 4 , 'number' => 8 ) )  // クラブ8
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[8] , $hand->levelName()  ); // four cards test
		
		$this->assertEquals( $hand->supplement['number'] , 8 );
		
		// straight flush test
		$cards = array(
				new Card( array( 'mark' => 3 , 'number' => 7 ) ), // ダイア7
				new Card( array( 'mark' => 3 , 'number' => 8 ) ), // ダイア8
				new Card( array( 'mark' => 3 , 'number' => 9 ) ), // ダイア9
				new Card( array( 'mark' => 3 , 'number' => 10 ) ), // ダイア10
				new Card( array( 'mark' => 3 , 'number' => 11 ) )  // ダイア11
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[9] , $hand->levelName()  ); // straight flush test

		$this->assertEquals( $hand->supplement['mark'] , 3 );
		$this->assertEquals( $hand->supplement['number'] , 11 );
		
		// royal straight flush test
		$cards = array(
				new Card( array( 'mark' => 4 , 'number' => 10 ) ), // クラブ10
				new Card( array( 'mark' => 4 , 'number' => 11 ) ), // クラブ11
				new Card( array( 'mark' => 4 , 'number' => 12 ) ), // クラブ12
				new Card( array( 'mark' => 4 , 'number' => 13 ) ), // クラブ13
				new Card( array( 'mark' => 4 , 'number' => 1 ) )   // クラブ1
		);
		$hand = new PokerHand( $cards );
		$this->assertEquals( PokerHand::$names[10] , $hand->levelName()  ); // royal straight flush test
		
		$this->assertEquals( $hand->supplement['mark'] , 4 );
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
		
		$report = $hand->analyse("combination");
		$this->assertTrue( $report["checked"]["combination"]  );
		
		
	}
}

?>