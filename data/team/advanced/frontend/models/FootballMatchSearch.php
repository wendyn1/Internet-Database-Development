<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：前台比赛列表搜索模型（C3）：球队/日期/阶段/状态筛选，每页10条，日期与ID倒序
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 */
namespace frontend\models;

use common\models\FootballMatch;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FootballMatchSearch extends Model
{
    public $team_id;
    public $date;
    public $stage;
    public $status;

    public function rules()
    {
        return [
            [['team_id', 'date', 'stage', 'status'], 'trim'],
            ['team_id', 'integer', 'min' => 1,
                'message' => '球队筛选必须是正整数ID。'],
            ['date', 'date', 'format' => 'php:Y-m-d',
                'message' => '日期筛选必须为 YYYY-MM-DD 格式。'],
            ['stage', 'in', 'range' => array_keys(FootballMatch::stageLabels()),
                'message' => '比赛阶段不在允许范围内。'],
            ['status', 'in', 'range' => array_keys(FootballMatch::statusLabels()),
                'message' => '比赛状态不在允许范围内。'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'team_id' => '球队',
            'date' => '北京日期',
            'stage' => '比赛阶段',
            'status' => '比赛状态',
        ];
    }

    /**
     * @param array $params GET参数
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = FootballMatch::find()->with(['venue', 'homeTeam', 'awayTeam']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10, 'pageParam' => 'page'],
            'sort' => [
                'defaultOrder' => ['match_date_beijing' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => ['match_date_beijing', 'id'],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andWhere('1=0');
            return $dataProvider;
        }

        if ($this->team_id !== null && $this->team_id !== '') {
            $query->andWhere(['or',
                ['home_team_id' => (int) $this->team_id],
                ['away_team_id' => (int) $this->team_id],
            ]);
        }
        if ($this->date !== null && $this->date !== '') {
            $query->andWhere(['match_date_beijing' => $this->date]);
        }
        if ($this->stage !== null && $this->stage !== '') {
            $query->andWhere(['stage' => $this->stage]);
        }
        if ($this->status !== null && $this->status !== '') {
            $query->andWhere(['status' => $this->status]);
        }

        return $dataProvider;
    }
}
