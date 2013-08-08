<?php 
	$cs = Yii::app()->clientScript;
	$cs->registerCoreScript('jquery');
	$script = <<<'EOL'
$(window).load(function(){
	var $h = $('#gamemain').height() ;
	$('#gamecontrol').height( $h ); 
	$('#gameinput').height( $h / 2 ); 
	$('#gameinput_inner').height( $h / 2 ); 
});
EOL;
	$cs->registerScript('adjustArea',$script,CClientScript::POS_HEAD);
?>
<?php if (isset($engine->currentView['select_card'])): ?>
<?php
	$script = <<<JS
function check() {
  var checks = [];
  $('form[name="card"] [name="select_card[]"]:checked').each( function() { 
	checks.push(this.value); 
  }); 
  $('form[name="command"] [name="select_card"]').val( checks.join(',') );
  var submit_btn = "交換しない";
  if (checks.length > 0) {
	submit_btn = "交換";
  }
  $('form[name="command"] :submit').val( submit_btn );
  return true;
}
	
$(function(){ check(); });
JS;
	$cs->registerScript('check_cards',$script,CClientScript::POS_HEAD);
?>
<?php endif; ?>
<?php 
 $fo = $engine->currentView['flagCardOpen'];
 $settings = Yii::app()->settings;
 $settings->load();
 if ($settings->data["openCard"]) {
	$fo = true;
 }
?>
<div id="gamearea">

<div id="gamemain">

<form name="card">

<?php foreach ($engine->main["charman"]->players as $player): ?>
<?php   if ($player->isCom() ): ?>

<p>
COMのコイン
<?php echo $player->totalCoins; ?>枚 (Bet <?php echo $player->fieldCoins; ?>)
<?php if ($engine->main["charman"]->dealer == $player) : ?>
<span class="wspc1"></span>
<span style="color:#FF9F9F">[ディーラー]</span>
<?php endif; ?>
</p>

<?php $i = 0; ?>
<?php
 $cards = $player->cards; 
 if (count($cards)==0) {
	for ($k=0;$k<5;$k++) {
		array_push( $cards , new Card( array( 'mark'=>0 ) ));
	}
 }
?>
<?php foreach ($cards as $card): ?>
<div style="float:left; width:120px;" >
<?php if (isset($engine->currentView['com_select_card'])): ?>
<?php   if (!in_array( $i , $engine->currentView['com_select_card']) ): ?>
<p style="text-align:center;" ><img src="<?php echo $fo ? $card->image() : $card->bg_image(); ?>"></p>
<?php   else: ?>
<p style="text-align:center;">&nbsp;</p>
<?php   endif; ?>
<?php else: ?>
<p style="text-align:center;" ><img src="<?php echo $fo ? $card->image() : $card->bg_image(); ?>"></p>
<?php endif; ?>
</div>
<?php $i += 1; ?>
<?php endforeach; ?>
<p style="clear:left; "></p>

<div class="spc"></div>
<?php   elseif ($player->isPerson() ): ?>

<p>
あなたのコイン
<?php echo $player->totalCoins; ?>枚 (Bet <?php echo $player->fieldCoins; ?>)
<?php if ($engine->main["charman"]->dealer == $player) : ?>
<span class="wspc1"></span>
<span style="color:#FF9F9F">[ディーラー]</span>
<?php endif; ?>
</p>

<?php $i=0; ?>
<?php
 $cards = $player->cards; 
 if (count($cards)==0) {
	for ($k=0;$k<5;$k++) {
		array_push( $cards , new Card( array( 'mark'=>0 ) ));
	}
 }
?>
<?php foreach ($cards as $card): ?>
<div style="float:left; width:120px;" >
<?php if (isset($engine->currentView['select_card'])): ?>
<p style="text-align:center;" ><label for="<?php echo "cid" . $i; ?>"><img src="<?php echo $card->image() ?>" style="cursor:pointer;"></label></p>
<p style="text-align:center;"><input id="<?php echo "cid" . $i; ?>" type="checkbox" name="select_card[]" value="<?php echo $i++; ?>" onClick="check();"></p>
<?php else: ?>
<p style="text-align:center;" ><img src="<?php echo $card->image() ?>"></p>
<?php endif; ?>
</div>
<?php endforeach; ?>
<p style="clear:left; "></p>

<?php    endif; ?>
<?php endforeach; ?>

</form>

<div class="spc"></div>

</div>
<div id="gamecontrol">

<div id="gamedata"><!--  begin data -->
<p>
<span>
Round <?php echo $engine->main["charman"]->round ?> / 4
</span>
<span class="wspc1">
残りカード数 <?php echo $engine->main['charman']->deck->count ?>枚 
</span>
</p>
<div class="spc"></div>
</div><!--  end data -->

<div id="gamemessage"><!--  begin messages  -->
<?php   if (isset($engine->currentView["text"])): ?>
<p><?php echo Yii::app()->format->ntext( $engine->currentView["text"] ); ?></p>
<?php   endif; ?>
</div><!--  end messages  -->

<div id="gameinput"><!--  begin input  -->
<div id="gameinput_inner">
<?php if (isset($engine->currentView["form"])): ?>
<?php echo CHtml::beginForm( $this->createUrl($this->id . "/play") , 'GET' , 
			isset($engine->currentView["select_card"]) ? array( 'name'=>'command', 'onSubmit' => "return check();"   ) : null ); ?> 
<input type="hidden" name="counter" value="<?php echo $engine->checkCode; ?>">
<?php   if (isset($engine->currentView['select_card'])): ?>
<input type="hidden" name="select_card">
<?php   endif; ?>
<?php   foreach ( $engine->currentView['form'] as $element ): ?>
<?php     if ($element['type'] == 'input'): ?>
<?php echo $element['before']; ?><input type="text" name="<?php echo $element['name'] ?>"<?php if (isset($element['size'])) { echo ' size="'.$element['size']. '"'; } ?>><?php echo $element['after']; ?>
<?php     endif; ?>
<?php     if ($element['type'] == 'submit'): ?>
<input type="submit" name="<?php echo $element['name']; ?>" value="送信">
<?php     endif; ?>
<?php   endforeach; ?>
<?php echo CHtml::endForm(); ?>
<?php endif; ?>
<?php if (isset($engine->currentView["buttons"])): ?>
<?php foreach ( $engine->currentView["buttons"] as $button ): ?>
<p>
<a href="<?php echo $this->createUrl($this->id . "/play",array( "answer" => $button[1] , "counter" => $engine->checkCode ) )?>">
<?php echo $button[0]; ?>
</a>
</p>
<?php endforeach; ?>
<?php endif; ?>
<?php if ( (! isset($engine->currentView["buttons"])) && (! isset($engine->currentView["form"])) ): ?>
<a href="<?php echo $this->createUrl($this->id."/play",array( "answer" => "OK" , "counter" => $engine->checkCode ) )?>">
OK
</a>
<?php endif; ?>
</div>
</div><!--  end input -->


</div>
<p style="clear:left; "></p>
</div>
