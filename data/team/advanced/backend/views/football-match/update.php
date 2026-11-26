<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台修改比赛视图（C5）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
/* @var $this yii\web\View */
/* @var $model common\models\FootballMatch */
/* @var $venues array */
/* @var $hasStatistics bool */

$this->title = '修改比赛：' . $model->home_team_name . ' vs ' . $model->away_team_name;
$this->params['breadcrumbs'][] = ['label' => '比赛管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<h1><?= yii\helpers\Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model, 'venues' => $venues, 'hasStatistics' => $hasStatistics]) ?>
