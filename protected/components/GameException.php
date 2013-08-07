<?php 

class GameException extends CException {
	
	public $message;
	
	function __construct($message) {
		$this->message = $message;	
	}
}

?>