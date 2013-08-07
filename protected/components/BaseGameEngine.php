<?php 

class BaseGameEngine extends CComponent {
	public $data;
	public $check_code_key = "check_code";
	
	public $loaddata;
	public $savedata;

	public function input($key) {
		
	}
	
	public function initData() {
		$this->data = new CMap( 
				array( 'system' => new CMap() , 
						'main' => new CMap() ));
	}
	
	public function generateCheckCode() {
		$check_code = $this->checkCode;
		if (!isset($check_code)) {
			$check_code = 0;
		}
		$check_code += 1;
		$this->checkCode = $check_code;
	}

	public function onInputCheckCode() {
		$return = new CMap( ); # CMapなら、参照渡しして戻りを取得可能。
		$b = $this->raiseEvent('onInputCheckCode',new CEvent( $this  , $return ) );
		return $return['value'];
	}
	
	public function checkCheckCode() {
		$ext_check_code = $this->onInputCheckCode();
		$check_code = $this->checkCode;
		if ($check_code) {
			if ($ext_check_code == $check_code) {
				return true;
			}
		} else {
			return true;
		}
		return false;
	}
	
	public function clearCheckCode() {
		$this->system->remove( $this->check_code_key );
	}
	
	public function getCheckCode() {
		return $this->system->itemAt( $this->check_code_key );	
	}
	
	public function setCheckCode($value) {
		$this->system->add($this->check_code_key,$value);
	}
	
	public function getSystem() {
		return $this->data->itemAt('system');
	}
	
	public function setSystem($value) {
		$this->data->add('system',  $value);
	}
	
	public function getMain() {
		return $this->data->itemAt('main');
	}
	
	public function setMain($value) {
		return $this->data->add('main',$value);
	}
	
	public function getValueByName($name) {
		if ($this->main) {
			if (isset($this->main[$name])) {
				return $this->main[$name];
			}
		}
		return null;
	}
	
	public function setValueByName($name,$value) {
		if (isset($value)) {
			$this->main->add($name,$value);
		} else {
			$this->main->remove($name);
 		}
	}
	
	public function getMode() {
		return $this->getValueByName("mode");
	}
	
	public function setMode($mode) {
		$this->setValueByName("mode",$mode);
	}
	
	public function getCurrentView() {
		return $this->getValueByName("currentView");
	}
	
	public function setCurrentView($view) {
		$this->setValueByName("currentView",$view);
	}
	
	public function getNextProcess() {
		return $this->getValueByName("nextProcess");
	}
	
	public function setNextProcess($view) {
		$this->setValueByName("nextProcess",$view);
	}
	
	public function pushStack() {
		$stack = $this->system["stack"]; 
		if (!isset($stack)) {
			$stack = array();
		}
		$stack_data = new CMap();
		foreach ($this->main as $key => $value) {
			$stack_data[$key] = $value;
		}
		array_push( $stack , $stack_data );
		
		$this->clearData();
		$this->system->add("stack",$stack); 
	}
	
	public function popStack() {
		$stack = $this->system["stack"]; 
		if (! isset($stack)) {
			die;
		} 
		$data = array_pop( $stack );
		$this->clearData();
		foreach ($data as $key => $value) {
			$this->setValueByName( $key , $value );
		}
		$this->system->add("stack",$stack); // 要確認
	}
	
	public function clearData() { // スタック以外を削除
		foreach ($this->main as $key => $value) {
			$this->setValueByName( $key , null );
		}
	}

	public function clearDataAll() { // スタックも含めて削除
		unset( $this->data );
	}
	
	public function onLoadData() {
		return call_user_func( $this->loaddata );
	}
	
	public function onSaveData() {
		return call_user_func( $this->savedata );
	}

	public static function loadDataFromSession($manager) {
		$saved_data = Yii::app()->session["GameManager"];
		if (isset($saved_data)) {
			$manager->data = $saved_data;
			return true;
		}
		return false;
	}
	
	public static function saveDataFromSession($manager) {
		$data = null;
		if (isset($manager->data)) {
			$data = $manager->data;
		}
		Yii::app()->session["GameManager"] = $data;		
		return true;
	}
}


?>