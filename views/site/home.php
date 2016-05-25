<?php
/* @var $this yii\web\View */
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Nothing to see here!</h1>
	    
	    <?= !Yii::$app->user->isGuest ? '<h2>But there is a menu above</h2>' : '' ?>
	    
    </div>
</div>
