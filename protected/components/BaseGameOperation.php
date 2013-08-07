<?php 

class BaseGameOperation extends CComponent {
	public $engine;
	
	public function __construct($m) {
		$this->engine = $m;
	}
	
	public function input($key) {
		return $this->engine->input($key);
	}
	
	public function getData() {
		return $this->engine->data;
	}
	
	public function getMain() {
		return $this->engine->data->itemAt('main');
	}
	
	public function getSystem() {
		return $this->engine->system;
	}
	
	public function getCurrentView() {
		return $this->engine->currentView;
	}
	
	public function setCurrentView($v) {
		$this->engine->currentView = $v;
	}
	
	public function getNextProcess() {
		return $this->engine->nextProcess;
	}
	
	public function setNextProcess($v) {
		$this->engine->nextProcess = $v;
	}
}

?>