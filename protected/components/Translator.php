<?php 

class Translator extends CComponent{ 
	public $data;
	public $category;
	
	function init() {
		$this->category = 'app';
	}

	function trans($message,$params=array()) {
		if (Yii::app() instanceof CWebApplication) {
			$lang = isset( Yii::app()->settings->data['lang'] ) ? Yii::app()->settings->data['lang']  : Yii::app()->language;
			if ($lang) {
				return Yii::t($this->category,$message ,$params,null,$lang);
			}
			throw "Unknown langauge " . $lang . " called";
		} else {
			return Yii::t($this->category,$message,$params);
		}
	}
	
}



?>