<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台教练新建页（B6），复用_form
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Coach */

$this->title = '新建教练';
$this->params['breadcrumbs'][] = ['label' => '教练管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coach-admin-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
