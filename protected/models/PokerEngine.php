<?php 

class PokerEngine extends BaseGameEngine {
	
//	public $name = "Poker";
	public $input;
	
	public function input($key) {
		return call_user_func( $this->input , $key );
	}
	public function run() {
		$res = $this->onLoadData();
		if (! $res) {
			throw new GameException("ロードエラー");
			// ロードエラー
			return;
		}
		if (! $this->nextProcess) {
			$this->setNextProcess( 'Menu/start' );
			$this->clearCheckCode();
		}
		$safe_count = 0; // 無限ループにならないように
	
		$this->system->add("actionResult",true);
	
		do {
			$nextProcess = $this->getNextProcess();
			if (is_string($nextProcess)) {
				if (! $this->checkCheckCode()) {
					$this->falseResult("チェックコードが不正です");
				} else {
					$list = split( '/' , $nextProcess , 2);
					if (count($list)==1) {
						die("nextProcessが不正です");
					}
					$operationName = $list[0];
					$actionName = $list[1];
//					$targetManager = $this;
//					if ($managerName != $this->name) {
					$operationClass = $operationName . "Operation";
					$targetOperation = new $operationClass( $this );
//					}
					call_user_func( array($targetOperation , "action" . ucFirst( $actionName ) ) );
					$newNextProcess = $this->getNextProcess();
					$list = split( '/' , $newNextProcess );
					if (count($list)==1) {
						$this->setNextProcess( $targetOperation->name . "/" . $newNextProcess );
					}
				}
			}
			$safe_count += 1;
		} while (! $this->currentView && $safe_count < 100);
		if ($safe_count >= 100) {
			throw new GameException("無限ループしています");
//			$this->onCritical( new CEvent( $this , array( 'message' => '無限ループしています') ) );
			return;
		}
		$this->generateCheckCode();
		$this->onSaveData();
	}
	
	public function falseResult($text) {
		$this->system->add("actionResult",false);
		$this->system->add("resultText",$text);
	}
	
	//
	// ユーティリティメソッド
	//
	
	public function showView() {
		$view = new CMap( array(
				'message' => 'ポーカーゲーム',
				'template' => '_showCards',
				'text' => '',
				'charman' => $this->main["charman"]
		));
		$this->setCurrentView(  $view );
	}
}

?>