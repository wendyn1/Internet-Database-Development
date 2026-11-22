<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台本届阵容修改页（B6），复用_form
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */
/* @var $lists array */

$this->title = '修改阵容记录 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '本届阵容管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-player-admin-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model, 'lists' => $lists]) ?>
</div>
