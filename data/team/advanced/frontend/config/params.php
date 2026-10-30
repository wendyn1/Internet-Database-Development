<?php
/** 前台参数：留言目标路由及公开判定待对应模块负责人确认后配置。 */
return [
    'adminEmail' => 'admin@example.com',
    'commentTargets' => [
        'team' => ['detailRoute' => null, 'publicExists' => null],
        'player' => ['detailRoute' => null, 'publicExists' => null],
        'match' => ['detailRoute' => null, 'publicExists' => null],
    ],
];
