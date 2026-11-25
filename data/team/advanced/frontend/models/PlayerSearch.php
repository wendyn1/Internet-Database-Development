<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球员列表搜索模型（B2）：名称/球队/位置筛选，均通过2026届阵容过滤
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\models;

use common\models\Player;
use common\models\TeamPlayer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PlayerSearch extends Model
{
    const ROSTER_YEAR = 2026;

    public $name;
    public $team_id;
    public $position;

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'max' => 100],
            ['team_id', 'integer', 'min' => 1],
            ['position', 'in', 'range' => [
                TeamPlayer::POSITION_GK, TeamPlayer::POSITION_DF,
                TeamPlayer::POSITION_MF, TeamPlayer::POSITION_FW,
            ]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '球员姓名',
            'team_id' => '所属球队',
            'position' => '位置',
        ];
    }

    /**
     * 球员列表仅展示已收录2026届阵容的球员；球队与位置按2026届阵容过滤。
     * @param array $params GET参数
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Player::find()
            ->distinct()
            ->innerJoin('{{%team_player}} tp',
                'tp.player_id = {{%player}}.id AND tp.tournament_year = ' . self::ROSTER_YEAR);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10, 'pageParam' => 'page'],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC], 'attributes' => ['id']],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andWhere('1=0');
            return $dataProvider;
        }

        if ($this->name !== null && $this->name !== '') {
            $query->andFilterWhere(['or',
                ['like', '{{%player}}.name_zh', $this->name],
                ['like', '{{%player}}.name_en', $this->name],
            ]);
        }
        $query->andFilterWhere(['tp.team_id' => $this->team_id ?: null]);
        $query->andFilterWhere(['tp.position_code' => $this->position ?: null]);

        return $dataProvider;
    }
}
