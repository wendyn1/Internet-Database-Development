<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：留言区视图，已通过留言列表与统一提交表单，每页10条
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\widgets\ListView;
?>
<section id="comments">
    <h2>留言</h2>
    <?= ListView::widget([
        'dataProvider' => $provider,
        'itemView' => '@frontend/views/comment/_list',
        'emptyText' => '暂无公开留言。',
        'summary' => '',
    ]) ?>
    <?= $this->render('_form', ['model' => $model,
        'targetType' => $targetType, 'targetId' => $targetId]) ?>
</section>
