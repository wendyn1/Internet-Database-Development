<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台执教关系新建页（B6），复用_form
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeamCoach */
/* @var $lists array */

$this->title = '新建执教关系';
$this->params['breadcrumbs'][] = ['label' => '执教关系管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-coach-admin-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model, 'lists' => $lists]) ?>
</div>
