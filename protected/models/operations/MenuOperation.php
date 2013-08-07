<?php 

class MenuOperation extends BaseGameOperation {
	public $name = "Menu";
	
	public function actionStart() {
		$view = new CMap( array(
				'message' => 'ポーカーゲーム',
				'template' => '_start',
				'text' => 'よろしければ、OKを押してください'
		));
		$this->setCurrentView(  $view );
		$this->setNextProcess( "Main/gameInitialize" );
	}
	
	public function actionFormPlay() {
		$view = new CMap( array(
				'message' => 'ポーカーゲーム',
				'template' => '_start',
				'text' => 'よろしければ、リンクを押してください',
				'buttons' => array()
		));
		$this->setCurrentView(  $view );
		$this->currentView["buttons"] = array(
			array( '続きから' , "doContinue" ),
			array( 'はじめから' , "doRestart")	
		);
		$this->setNextProcess( "doPlay" );
	}
	
	public function actionDoPlay() {
		if ($this->input('answer') == 'doContinue' ) {
			$this->engine->popStack();
			return;
		}
		$this->engine->popStack();
		$this->setCurrentView( null );
		$this->setNextProcess( "Main/gameInitialize" );
		
	}
	
	
}

?>