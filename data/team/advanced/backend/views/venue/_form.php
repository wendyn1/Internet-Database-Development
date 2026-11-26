<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台场馆表单（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */
?>
<div class="venue-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name_zh')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
