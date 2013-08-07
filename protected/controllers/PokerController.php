<?php

class PokerController extends Controller
{
	public function actionIndex()
	{
		$engine = $this->engine();
		try {
			$engine->run();
			$this->render('index' , array( 'engine' => $engine ) );
		} catch (GameException $e) {
			$this->redirectError( $e->message );
		}
	}

	public function actionMenu() {
		$engine = $this->engine();
		$res = $engine->onLoadData();
		if ( $res ) {
			if ($engine->mode != "menu" && $engine->currentView) {
				$engine->pushStack();
				$engine->mode = "menu";
				$engine->setCurrentView(null);
				$engine->setNextProcess( "PokerMenu/formPlay" );
				$engine->checkCode = null;
				$this->redirect( array( $this->id . '/index'));
				return;
			} else if ($engine->mode == "menu") {
				$this->redirect( array( $this->id . '/index'));
			}
		}
		$this->redirect( array( $this->id . '/init'));
	}
	
	public function actionInit() {
		$engine = $this->engine();
		$engine->initData();
		$engine->mode = "main";
		$engine->onSaveData();
	
		$this->redirect( array( $this->id . '/index'));
	}
	
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionErr() {
		$this->render('err');
	}
	
	public function actionClearData() {
		$engine = $this->engine();
		$engine->clearDataAll();
		$engine->onSaveData( new CEvent($this) );
		$this->render('clearData');
	}
	
	public function engine() {
		$engine = new PokerEngine();
		$engine->onInputCheckCode = function($event) {
			$event->params['value'] = Yii::app()->request->getParam('counter');
		};
	
		$engine->loaddata = function() use ($engine) { return BaseGameEngine::loadDataFromSession($engine); };
		$engine->savedata = function() use ($engine) { return BaseGameEngine::saveDataFromSession($engine); };
	
		$current = $this;
		$engine->input = function($key) { return Yii::app()->request->getParam($key); };
		return $engine;
	
	}
	
	
	public function redirectError($message) {
		Yii::app()->user->setFlash('message',$message);
		$this->redirect( array( $this->id . '/err') );
	}
	
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}