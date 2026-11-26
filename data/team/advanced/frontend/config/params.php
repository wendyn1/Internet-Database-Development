<?php
/** 前台参数：留言目标路由及公开判定待对应模块负责人确认后配置。 */
return [
    'adminEmail' => 'admin@example.com',
    'commentTargets' => [
        'team' => [
            'detailRoute' => '/team/view',
            'publicExists' => static function ($id) {
                return \common\models\Team::find()
                    ->where(['id' => (int) $id])
                    ->exists();
            },
        ],
        'player' => [
            'detailRoute' => '/player/view',
            'publicExists' => static function ($id) {
                return \common\models\Player::find()
                    ->where(['id' => (int) $id])
                    ->exists();
            },
        ],
        'match' => [
            'detailRoute' => '/football-match/view',
            'publicExists' => static function ($id) {
                return \common\models\FootballMatch::find()
                    ->where(['id' => (int) $id])
                    ->exists();
            },
        ],
    ],
];
