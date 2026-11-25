<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台修改球员统计视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
/* @var $this yii\web\View */
/* @var $model common\models\PlayerMatchStat */
/* @var $matchOptions array */
/* @var $rosterOptions array */

$this->title = '修改球员统计 #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '球员统计管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '统计 #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<h1><?= yii\helpers\Html::encode($this->title) ?></h1>
<?= $this->render('_form', [
    'model' => $model,
    'matchOptions' => $matchOptions,
    'rosterOptions' => $rosterOptions,
]) ?>
