<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台公共布局及管理员导航
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '后台首页', 'url' => ['/site/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '管理员登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => '基础资料', 'items' => [
            ['label' => '球队', 'url' => ['/team/index']],
            ['label' => '球员', 'url' => ['/player/index']],
            ['label' => '教练', 'url' => ['/coach/index']],
            ['label' => '阵容关系', 'url' => ['/team-player/index']],
            ['label' => '执教关系', 'url' => ['/team-coach/index']],
            ['label' => '荣誉', 'url' => ['/honor/index']],
            ['label' => '球队荣誉', 'url' => ['/team-honor/index']],
        ]];
        $menuItems[] = ['label' => '比赛管理', 'items' => [
            ['label' => '场馆', 'url' => ['/venue/index']],
            ['label' => '比赛', 'url' => ['/football-match/index']],
            ['label' => '球员统计', 'url' => ['/player-match-stat/index']],
        ]];
        $menuItems[] = ['label' => '留言管理', 'url' => ['/comment/index']];
        $menuItems[] = ['label' => '修改密码', 'url' => ['/site/change-password']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出 (' . Html::encode(Yii::$app->user->identity->username) . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
