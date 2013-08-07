<p>準備は良いですか？</p>
<p>これからポーカーゲームを始めます</p>

<?php   if (isset($engine->currentView["text"])): ?>
<p><?php echo $engine->currentView["text"]; ?></p>
<?php   endif; ?>

<?php if (isset($engine->currentView["buttons"])): ?>
<?php foreach ( $engine->currentView["buttons"] as $button ): ?>
<p>
<a href="<?php echo $this->createUrl($this->id . "/play",array( "answer" => $button[1] , "counter" => $engine->checkCode ) )?>">
<?php echo $button[0]; ?>
</a>
</p>
<?php endforeach; ?>
<?php endif; ?>
<?php if (! isset($engine->currentView["buttons"])) : ?>

<a href="<?php echo $this->createUrl($this->id."/play",array( "answer" => "OK" , "counter" => $engine->checkCode ) )?>">
OK
</a>
<?php endif; ?>
