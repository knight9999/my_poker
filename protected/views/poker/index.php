<h1>Home</h1>

<p><?php echo __trans("game.view.home.title")?></p>

<div class="spc"></div>

<p><a href="<?php echo $this->createUrl("poker/init"); ?>" ><?php echo __trans("game.view.home.play_game"); ?></a></p>
<p><a href="<?php echo $this->createUrl("poker/howto"); ?>" ><?php echo __trans("game.view.home.show_explanation"); ?></a></p>
