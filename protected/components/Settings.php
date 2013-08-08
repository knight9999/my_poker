<?php 

class Settings extends CComponent {
	public $data;

	function __construct() {
		$this->reset();
	}
	
	function init() {
		
	}
	
	function reset() {
		$this->data = new CMap();
		$this->data["openCard"] = 0;
		$this->data["dealer"] = 1;
	}
	
	public function load() {
		$saved_data = Yii::app()->session["GameSettings"];
		if (isset($saved_data)) {
			$this->data = $saved_data;
			return true;
		}
		return false;
	}
	
	public function save() {
		$data = new CMap();
		if (isset($this->data)) {
			$data = $this->data;
		}
		Yii::app()->session["GameSettings"] = $data;
		return true;
	}
	
	
}

?>