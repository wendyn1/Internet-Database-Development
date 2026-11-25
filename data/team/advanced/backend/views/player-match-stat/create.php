<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台新增球员统计视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
/* @var $this yii\web\View */
/* @var $model common\models\PlayerMatchStat */
/* @var $matchOptions array */
/* @var $rosterOptions array */

$this->title = '新增球员统计';
$this->params['breadcrumbs'][] = ['label' => '球员统计管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= yii\helpers\Html::encode($this->title) ?></h1>
<?= $this->render('_form', [
    'model' => $model,
    'matchOptions' => $matchOptions,
    'rosterOptions' => $rosterOptions,
]) ?>
