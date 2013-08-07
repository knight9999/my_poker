<?php

class PokerController extends Controller
{
	public function actionIndex()
	{
		$this->render('index' );
	}

	public function actionPlay()
	{
		$engine = $this->engine();
		try {
			$engine->run();
			$this->render('play' , array( 'engine' => $engine ) );
		} catch (GameException $e) {
			$this->redirectError( $e->message );
		}
	}
	
	public function actionMenu() {
		$engine = $this->engine();
		$res = $engine->onLoadData();
		$engine->clearCheckCode();
		if ( $res ) {
			if ($engine->mode != "menu" && $engine->currentView) {
				$engine->pushStack();
				$engine->mode = "menu";
				$engine->setCurrentView(null);
				$engine->setNextProcess( "Menu/formPlay" );
				$engine->onSaveData();
				$this->redirect( array( $this->id . '/play'));
				return;
			} else if ($engine->mode == "menu") {
				$engine->setCurrentView(null);
				$engine->setNextProcess( "Menu/formPlay" );
				$engine->onSaveData();
				$this->redirect( array( $this->id . '/play'));
				return;
			}
		}
		$this->redirect( array( $this->id . '/init'));
	}
	
	public function actionInit() {
		$engine = $this->engine();
		$engine->initData();
		$engine->mode = "main";
		$engine->onSaveData();
	
		$this->redirect( array( $this->id . '/play'));
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