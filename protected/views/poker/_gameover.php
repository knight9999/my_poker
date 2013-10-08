<p>GameOver</p>
 
<?php if ($engine->system->itemAt("actionResult") == false) : ?>
<p style="color:red">
	<?php echo $engine->system["resultText"]; ?>
</p>
<?php endif; ?>

<?php   if (isset($engine->currentView["text"])): ?>
<p><?php echo $engine->currentView["text"]; ?></p>
<?php   endif; ?>

<?php if ($engine->currentView["flag_win"]): ?>
<img src="/images/s_cracker.png"><p class="large">You Win!</p>
<?php else: ?>
<img src="/images/s_cemetery.png"><p class="large">You Loose!</p>
<?php endif; ?>
<?php   if (isset($engine->currentView["buttons"])): ?>
<?php   foreach ( $engine->currentView["buttons"] as $button ): ?>
<a href="<?php echo $this->createUrl($this->id . "/play",array( "answer" => $button[1] , "counter" => $engine->checkCode ) )?>">
<?php   echo $button[0]; ?>
</a>
<?php   endforeach; ?>
<?php   endif; ?>
<br>


<br>
<br>
