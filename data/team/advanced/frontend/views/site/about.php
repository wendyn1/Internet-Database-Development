<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：关于本站页面，介绍项目定位、收录范围、技术实现与仓库地址
 * 作者：牛雨萱（学号 2411061）
 * 创建日期：2026-11-22
 * 主要修改者：牛雨萱（学号 2411061）
 */
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = '关于本站';
$this->params['breadcrumbs'][] = $this->title;

$repositoryUrl = 'https://github.com/wendyn1/Internet-Database-Development';
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        2026世界杯球队信息系统是“互联网数据库开发”课程项目，采用中文体育资讯风格，
        围绕2026世界杯精选球队，集中展示球队、球员、教练、比赛与荣誉资料，
        并提供球队对比、胜平负图表和访客留言。
    </p>

    <h2>收录范围</h2>
    <ul>
        <li>首批收录6支重点展示球队，含本届阵容、主教练、荣誉与部分真实比赛。</li>
        <li>仅统计本站已录入且已结束的比赛；比分含加时赛，不含点球大战，点球决胜仍按平局计。</li>
        <li>资料来自公开网络信息，由小组成员手动整理，仅用于课程演示，不作为完整赛事资料引用。</li>
    </ul>

    <h2>主要功能</h2>
    <ul>
        <li>球队、球员、教练资料浏览与查询，比赛列表筛选与详情。</li>
        <li>两队对比与胜平负占比饼图，统计范围随页面说明。</li>
        <li>访客免注册留言，管理员在后台审核通过后公开展示。</li>
    </ul>

    <h2>技术实现</h2>
    <p>Yii2 Advanced 框架（PHP 8）+ MySQL / MariaDB，前端使用 Bootstrap，图表使用原生 Canvas 绘制。</p>

    <h2>课程作业仓库</h2>
    <p>
        项目地址：
        <?= Html::a(Html::encode($repositoryUrl), $repositoryUrl,
            ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?>
    </p>
    <p>团队材料位于仓库 <code>data/team/</code>，个人作业位于 <code>data/personal/</code>。</p>

    <h2>小组成员</h2>
    <p>牛雨萱（2411061）、王乐妍（2413423）、邓佳怡（2412612）</p>
</div>
