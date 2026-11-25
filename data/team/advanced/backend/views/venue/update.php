<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台修改场馆视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = '修改场馆：' . $model->name_zh;
$this->params['breadcrumbs'][] = ['label' => '场馆管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_zh, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<h1><?= yii\helpers\Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
