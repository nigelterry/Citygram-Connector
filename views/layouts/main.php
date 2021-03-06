<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Citygram',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => !Yii::$app->user->isGuest ?
	                [
                    ['label' => 'Crime Reports', 'url' => ['crime-message/index']],
                    ['label' => 'Permit Applications', 'url' => ['permit-message/index']],
                    ['label' => 'Rezoning Requests', 'url' => ['zoning-message/index']],
		            ['label' => 'Crash Reports', 'url' => ['crash-message/index']],
		                ['label' => 'Road Closures', 'url' => ['event-message/index']],
                    ['label' => 'Home', 'url' => ['/site/home']],
                    ['label' => 'About', 'url' => ['/site/about']],
//                    ['label' => 'Contact', 'url' => ['/site/contact']],
		            ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                    ] :
	                [
		                ['label' => 'Login', 'url' => ['/site/login']]
	                ]
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <div class="container">
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Sapphire Web Services <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
