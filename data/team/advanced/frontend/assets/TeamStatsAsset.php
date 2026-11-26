<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：对比页统计图表脚本资源包（C6）
 * 作者：邓佳怡（学号 2412612）
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace frontend\assets;

use yii\web\AssetBundle;

class TeamStatsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = ['js/team-stats.js'];
    public $depends = ['yii\web\YiiAsset'];
}
