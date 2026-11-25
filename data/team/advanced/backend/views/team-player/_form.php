<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台本届阵容表单视图（B6），新建与修改共用
 * 作者：王乐妍（2413423）
 * 创建日期：2026-11-22
 * 主要修改者：王乐妍（2413423）
 */
use common\models\TeamPlayer;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeamPlayer */
/* @var $lists array 球队与球员下拉列表 */

$positionLabels = [
    TeamPlayer::POSITION_GK => '门将（GK）',
    TeamPlayer::POSITION_DF => '后卫（DF）',
    TeamPlayer::POSITION_MF => '中场（MF）',
    TeamPlayer::POSITION_FW => '前锋（FW）',
];

$this->title = $model->isNewRecord ? '新建阵容记录' : ('修改阵容记录 #' . $model->id);
$this->params['breadcrumbs'][] = ['label' => '本届阵容管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->isNewRecord ? '新建' : '修改';
?>
<div class="team-player-admin-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'team_id')->dropDownList($lists['teams'], ['prompt' => '请选择球队']) ?>
    <?= $form->field($model, 'player_id')->dropDownList($lists['players'], ['prompt' => '请选择球员']) ?>
    <?= $form->field($model, 'tournament_year')->textInput()
        ->hint('仅支持2026届；同球员同届仅能归属一队') ?>
    <?= $form->field($model, 'shirt_number')->textInput()
        ->hint('1—26；同球队同届号码唯一') ?>
    <?= $form->field($model, 'position_code')->dropDownList($positionLabels, ['prompt' => '请选择位置']) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
