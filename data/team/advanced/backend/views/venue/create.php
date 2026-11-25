<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台新建场馆视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = '新建场馆';
$this->params['breadcrumbs'][] = ['label' => '场馆管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= yii\helpers\Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
