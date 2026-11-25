<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台球员表单视图（B6），新建与修改共用
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Player */

$this->title = $model->isNewRecord ? '新建球员' : ('修改球员：' . $model->name_zh);
$this->params['breadcrumbs'][] = ['label' => '球员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->isNewRecord ? '新建' : '修改';
?>
<div class="player-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name_zh')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'birth_date')->textInput(['placeholder' => 'Y-m-d，未知留空'])
        ->hint('格式2026-01-31；留空表示出生日期未知，前台不显示年龄') ?>
    <?= $form->field($model, 'photo_path')->textInput(['maxlength' => true])
        ->hint('照片相对路径或URL，留空显示占位图；上传功能待A/B/C协调后接入') ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
