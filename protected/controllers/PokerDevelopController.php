<?php 

require_once('PokerController.php');

class PokerDevelopController extends PokerController {
	
	
	public function actionIndex($page = null) {
		$engine = $this->engine();
		if (isset($page)) {
			switch ($page) {
				case "gameover":
					$text = "優勝者は" . "○○○" . "です。";
					$view = new CMap( array(
							'message' => 'ゲームオーバー',
							'text' => '勝敗がつきました。' . $text,
							'template' => 'application.views.poker._gameover',
							'flag_win' => true,
							'buttons' => array(
									array( '最初からゲームをやる' , 's1' )
							)
					));
					$engine->setCurrentView( $view );
					$this->render('application.views.poker.play' , array( 'engine' => $engine ) );
					break;
				case "gameover2":
					$text = "優勝者は" . "○○○" . "です。";
					$view = new CMap( array(
							'message' => 'ゲームオーバー',
							'text' => '勝敗がつきました。' . $text,
							'template' => 'application.views.poker._gameover',
							'flag_win' => false,
							'buttons' => array(
									array( '最初からゲームをやる' , 's1' )
							)
					));
					$engine->setCurrentView( $view );
					$this->render('application.views.poker.play' , array( 'engine' => $engine ) );
					break;
				case "start":
					$view = new CMap( array(
							'message' => 'ポーカーゲーム',
							'template' => 'application.views.poker._start',
							'text' => 'よろしければ、OKを押してください'
					));
					$engine->setCurrentView(  $view );
					$this->render('application.views.poker.play' , array( 'engine' => $engine ) );
					break;
			}	
		} else {
			$this->render( 'index' );
		}
	}
	
	public function actionPlay() {
		$this->redirect( array( $this->id . '/index'));
	}
	
}

?>