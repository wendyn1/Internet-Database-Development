<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台荣誉定义修改页（B6），复用_form
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Honor */

$this->title = '修改荣誉：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '荣誉管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="honor-admin-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
