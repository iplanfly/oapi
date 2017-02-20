<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Question;
use app\models\QuestionCollection;

/**
 * QuestionSearch represents the model behind the search form about `app\models\Question`.
 */
class QuestionSearch extends Question
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'content', 'image', 'qq_group'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Question::find()
            ->select(['id', 'type', 'title', 'content', 'image', 'qq_group', 'created_at']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'qq_group', $this->qq_group]);

        return $dataProvider;
    }

    /**
     * 获取问题列表
     *
     * @param array $params 参数
     *
     * @return array
     */
    public function getList($params)
    {
        $dataProvider = $this->search($params);

        $result = [];

        foreach ($dataProvider->getModels() as $model) {
            $result[] = [
                'id' => $model->id,
                'type' => $model->getType(),
                'title' => $model->title,
                'content' => $model->content,
                'image' => explode('，', $model->image),
                'qq_group' => $model->qq_group,
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        return $result;
    }

    /**
     * 当用户登录时，为返回的问题列表附加收藏状态
     *
     * @param $indexList 收藏列表
     * @return array 附加状态后的列表
     */
    public static function attachCollectionStatus($indexList)
    {
        $userId = Yii::$app->user->id;
        foreach ($indexList as &$question) {
            $question['isCollected'] = QuestionCollection::isCollected($userId, $question['id']) ? true : false;
        }
        return $indexList;
    }
}
