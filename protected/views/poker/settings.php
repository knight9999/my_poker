<h1>Settings</h1>

<p>ここでは、どちらがディーラを担当するか、COMのカードをオープンのまま遊ぶかなどを指定することが出来ます。</p>

<div class="spc"></div>

<?php if (isset($message)) : ?>
<p style="color:red">
	<?php echo $message; ?>
</p>
<?php endif; ?>

<div class="form">
<?php echo CHtml::beginForm( $this->createUrl($this->id . "/" . $this->action->id ) , 'GET'); ?>

<ul>
<li><label>COMのカードをオープン</label>
<?php echo CHtml::checkBox("openCard", $settings->data["openCard"] ); ?>
</li>
<li><label>ディーラー</label>
<span>
<?php echo CHtml::radioButton("dealer",($settings->data["dealer"] == 1),array( "value" => 1 , "id"=>"dealer1" ) ); ?>
<label for="dealer1">COM</label>
<span class="wspc"></span>
<?php echo CHtml::radioButton("dealer",($settings->data["dealer"] == 2),array( "value"=>2 , "id"=>"dealer2" ) ); ?>
<label for="dealer2">あなた</label>
</span>
</li>
<li><label>&nbsp;</label>
<?php echo CHtml::submitButton("OK" , array('name' => "submit")); ?>
<?php echo CHtml::submitButton("RESET", array('name' => "reset" )); ?>
</li>
</ul>

<?php echo CHtml::endForm() ;?>
</div>

<hr >

<a href="<?php echo $this->createUrl($this->id . "/" . $this->action->id , array( "cleardata" => "1") ); ?>">
あなたのゲームデータを削除
</a>
