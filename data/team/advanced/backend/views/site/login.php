<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：管理员登录视图，仅提供会话登录
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = '管理员登录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>请输入管理员用户名和密码。</p>
    <div class="row"><div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'admin-login-form']); ?>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>
        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'current-password']) ?>
        <?= Html::submitButton('登录', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div></div>
</div>
