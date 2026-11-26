<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：单条公开留言视图，昵称、正文及时间均转义，正文按纯文本展示
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;
?>
<article class="comment-item">
    <p><strong><?= Html::encode($model->nickname) ?></strong>
        <time><?= Html::encode($model->created_at) ?></time></p>
    <p><?= nl2br(Html::encode($model->content)) ?></p>
</article>
