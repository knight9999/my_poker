<?php 

class ComLogicTest extends CTestCase {
	public $charman;
	public $com_player;
	
	public function prepare() {
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
		
		$this->charman = $charman;
		$this->com_player = $com_player;
	}
	
	public function testChangeCardsByRandom() {
		$this->prepare();
		$logic = new ComLogic( $this->charman, $this->com_player );

		$condition = null;
		GRand::ready( array( 100 ,  0 , 1 , 2, 3, 4) );
		$res = $logic->changeCardsByRandom( $condition );
		
		$this->assertEquals( 0 , $res[0] );
		$this->assertEquals( 1 , $res[1] );
		$this->assertEquals( 2 , $res[2] );
		$this->assertEquals( 3 , $res[3] );
		$this->assertEquals( 4 , $res[4] );
		
		$condition = array( "exceptions" => array( 1 ) , "force"=>false );
		Grand::ready( array( 1 , 1 , 3 , 0 , 1 , 2 ) );
		$res = $logic->changeCardsByRandom( $condition );
		
		$this->assertEquals( 0 , $res[0] );
		$this->assertEquals( 2 , $res[1] );
		$this->assertEquals( 3 , $res[2] );

		$condition = array( "exceptions" => array( 1 , 2 ) , "force"=>false );
		GRand::ready( array( 100 , 0 , 1 , 2 ) );
		$res = $logic->changeCardsByRandom( $condition );
		
		$this->assertEquals( 0 , $res[0] );
		$this->assertEquals( 3 , $res[1] );
		$this->assertEquals( 4 , $res[2] );
		
	}
	
	public function testChangeCards() {
		$this->prepare();
		
		// ワンペアの場合のテスト
		$cards = array( 
			new Card( array( 'mark' => 1 , 'number' => 3 ) ),
			new Card( array( 'mark' => 2 , 'number' => 3 ) ),
			new Card( array( 'mark' => 1 , 'number' => 5 ) ),
			new Card( array( 'mark' => 3 , 'number' => 8 ) ),
			new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );

		GRand::ready( array(100 , 0 , 1, 2) );
		$res = $logic->changeCards();
		$this->assertEquals( 2 , $res[0] );
		$this->assertEquals( 3 , $res[1] );
		$this->assertEquals( 4 , $res[2] );
		

		GRand::ready( array(100 , 0 , 2, 2) );
		$res = $logic->changeCards();
		$this->assertEquals( 2 , $res[0] );
		$this->assertEquals( 4 , $res[1] );

		// ツーペアの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 1 , 'number' => 8 ) ),
				new Card( array( 'mark' => 3 , 'number' => 8 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(100 , 0 ) );
		$res = $logic->changeCards();
		$this->assertEquals( 4 , $res[0] );

		// スリーカードの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 4 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 8 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(100 , 1, 0 ) );
		$res = $logic->changeCards();
		$this->assertEquals( 4 , $res[0] );
		$this->assertEquals( 3 , $res[1] );

		// フォーカードの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 4 , 'number' => 3 ) ),
				new Card( array( 'mark' => 3 , 'number' => 8 ) ),
				new Card( array( 'mark' => 3 , 'number' => 3 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(100 , 0 ) );
		$res = $logic->changeCards();
		$this->assertEquals( 3 , $res[0] );
		
		// フラッシュくずれの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 1 ) ),
				new Card( array( 'mark' => 2 , 'number' => 7 ) ),
				new Card( array( 'mark' => 2 , 'number' => 6 ) ),
				new Card( array( 'mark' => 3 , 'number' => 10 ) ),
				new Card( array( 'mark' => 2 , 'number' => 12 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(100 , 0 ) );
		$res = $logic->changeCards();

		$this->assertEquals( 3 , $res[0] );
		
		// ストレートくずれの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 1 ) ),
				new Card( array( 'mark' => 1 , 'number' => 2 ) ),
				new Card( array( 'mark' => 4 , 'number' => 4 ) ),
				new Card( array( 'mark' => 3 , 'number' => 5 ) ),
				new Card( array( 'mark' => 2 , 'number' => 9 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array( 0 ) );
		$res = $logic->changeCards();
		
		$this->assertEquals( 4 , $res[0] );
		
		// ロイヤルストレートくずれの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 1 , 'number' => 11 ) ),
				new Card( array( 'mark' => 4 , 'number' => 12 ) ),
				new Card( array( 'mark' => 3 , 'number' => 13 ) ),
				new Card( array( 'mark' => 2 , 'number' => 10 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array( 0 ) );
		$res = $logic->changeCards();
		
		$this->assertEquals( 0 , $res[0] );
		

		//ハイカードの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 1 , 'number' => 10 ) ),
				new Card( array( 'mark' => 4 , 'number' => 4 ) ),
				new Card( array( 'mark' => 3 , 'number' => 9 ) ),
				new Card( array( 'mark' => 2 , 'number' => 7 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(100 ,  100 , 0 , 1 , 2 , 3 ) );
		$res = $logic->changeCards();
		
		$this->assertEquals( 0 , $res[0] ); // 一番大きい10のカード以外を交換になるはず
		$this->assertEquals( 2 , $res[1] );
		$this->assertEquals( 3 , $res[2] );
		$this->assertEquals( 4 , $res[3] );
		
	}
	
	public function testDecidePlan2() {
		$this->prepare();
		
		// ワンペアの場合のテスト
		$cards = array(
				new Card( array( 'mark' => 1 , 'number' => 3 ) ),
				new Card( array( 'mark' => 2 , 'number' => 3 ) ),
				new Card( array( 'mark' => 1 , 'number' => 5 ) ),
				new Card( array( 'mark' => 3 , 'number' => 8 ) ),
				new Card( array( 'mark' => 4 , 'number' => 9 ) )
		);
		$this->com_player->cards = $cards;
		$logic = new ComLogic( $this->charman, $this->com_player );
		
		GRand::ready( array(3) );
		$res = $logic->decidePlan2();
		$this->assertEquals( 3 , $res );
		
		GRand::ready( array(10) );
		$res = $logic->decidePlan2();
		$this->assertEquals( 1 , $res );

		GRand::ready( array(90) );
		$res = $logic->decidePlan2();
		$this->assertEquals( 2 , $res );
		
	}
}

?>