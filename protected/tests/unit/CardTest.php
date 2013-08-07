<?php 

class CardTest extends CTestCase {
	
	public function testCard() {
		$card = new Card( array('mark' => 1, 'number' => 3 ));
		$this->assertEquals( $card->mark , 1);
		$this->assertEquals( $card->number, 3);
		
		$card2 = new Card( array('mark'=>1,'number'=>3));
		$this->assertTrue( $card->equals($card2) );
		
		$card3 = new Card( array('mark'=>2,'number'=>3));
		$this->assertFalse( $card->equals($card3) );
	}
	
}

?>