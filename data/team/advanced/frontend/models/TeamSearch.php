<?php
/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：球队列表搜索模型（B2）：名称模糊筛选，每页10条，ID升序
 * 作者：王乐妍（2413423）
 * 主要修改者：王乐妍（2413423）
 */
namespace frontend\models;

use common\models\Team;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TeamSearch extends Model
{
    public $name;

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return ['name' => '球队名称'];
    }

    /**
     * @param array $params GET参数
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Team::find();

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
                ['like', 'name_zh', $this->name],
                ['like', 'name_en', $this->name],
            ]);
        }

        return $dataProvider;
    }
}
