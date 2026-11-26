<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：当前管理员修改密码视图
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = '修改密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-change-password">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>新密码为8—64个字符，须与旧密码不同。修改成功后需要重新登录。</p>
    <div class="row"><div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>
        <?= $form->field($model, 'oldPassword')->passwordInput(['autocomplete' => 'current-password']) ?>
        <?= $form->field($model, 'newPassword')->passwordInput(['autocomplete' => 'new-password']) ?>
        <?= $form->field($model, 'confirmPassword')->passwordInput(['autocomplete' => 'new-password']) ?>
        <?= Html::submitButton('保存新密码', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div></div>
</div>
