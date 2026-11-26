<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：留言共用表单，仅提交昵称和正文，目标由固定动作地址确定
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'comment-form-' . $targetType . '-' . $targetId,
    'action' => ['/comment/create', 'targetType' => $targetType, 'targetId' => $targetId],
    'method' => 'post',
]);
?>
<?= $form->errorSummary($model) ?>
<?= $form->field($model, 'nickname')->textInput(['maxlength' => 30]) ?>
<?= $form->field($model, 'content')->textarea(['rows' => 4, 'maxlength' => 1000]) ?>
<p>留言审核通过后公开展示。</p>
<?= Html::submitButton('提交留言', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
