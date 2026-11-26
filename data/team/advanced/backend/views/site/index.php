<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：后台首页，显示数据概览、待审核留言与各模块快捷入口
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $counts array */
/* @var $recentComments common\models\Comment[] */

$this->title = '后台首页';

$cards = [
    ['label' => '球队', 'value' => $counts['team'], 'url' => ['/team/index'], 'class' => 'primary'],
    ['label' => '球员', 'value' => $counts['player'], 'url' => ['/player/index'], 'class' => 'success'],
    ['label' => '教练', 'value' => $counts['coach'], 'url' => ['/coach/index'], 'class' => 'info'],
    ['label' => '比赛', 'value' => $counts['match'], 'url' => ['/football-match/index'], 'class' => 'warning'],
    ['label' => '荣誉', 'value' => $counts['honor'], 'url' => ['/honor/index'], 'class' => 'default'],
    ['label' => '待审核留言', 'value' => $counts['pendingComment'], 'url' => ['/comment/index', 'status' => 'pending'], 'class' => 'danger'],
];
?>
<div class="site-index">
    <h1>后台首页</h1>
    <p class="text-muted">2026世界杯球队信息系统数据维护入口，所有修改保存后前台刷新即可见效。</p>

    <?php if ($counts['pendingComment'] > 0): ?>
        <div class="alert alert-warning">
            有 <strong><?= (int) $counts['pendingComment'] ?></strong> 条留言待审核，
            <?= Html::a('立即处理', ['/comment/index', 'status' => 'pending'], ['class' => 'alert-link']) ?>。
        </div>
    <?php else: ?>
        <div class="alert alert-success">当前没有待审核留言。</div>
    <?php endif; ?>

    <div class="row" style="margin-bottom:20px;">
        <?php foreach ($cards as $card): ?>
            <div class="col-sm-4 col-md-2" style="margin-bottom:15px;">
                <?= Html::a(
                    '<div class="panel panel-' . $card['class'] . '" style="margin-bottom:0;">'
                    . '<div class="panel-heading">' . Html::encode($card['label']) . '</div>'
                    . '<div class="panel-body" style="font-size:28px;font-weight:bold;">'
                    . (int) $card['value'] . '</div></div>',
                    $card['url'],
                    ['style' => 'text-decoration:none;color:inherit;']
                ) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="font-size:20px;">最近待审核留言</h2>
    <?php if (empty($recentComments)): ?>
        <p class="text-muted">暂无待审核留言。</p>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr><th>编号</th><th>留言对象</th><th>昵称</th><th>提交时间</th><th>操作</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recentComments as $comment): ?>
                <tr>
                    <td><?= (int) $comment->id ?></td>
                    <td><?= Html::encode($comment->getTargetLabel()) ?></td>
                    <td><?= Html::encode($comment->nickname) ?></td>
                    <td><?= Html::encode($comment->created_at) ?></td>
                    <td><?= Html::a('查看', ['/comment/view', 'id' => $comment->id],
                            ['class' => 'btn btn-default btn-xs']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2 style="font-size:20px;">快捷入口</h2>
    <p>
        <?= Html::a('球队管理', ['/team/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('球员管理', ['/player/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('教练管理', ['/coach/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('比赛管理', ['/football-match/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('留言审核', ['/comment/index', 'status' => 'pending'], ['class' => 'btn btn-warning']) ?>
    </p>
</div>
