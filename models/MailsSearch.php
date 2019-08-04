<?php

namespace wdmg\mailer\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\mailer\models\Mails;

/**
 * MailsSearch represents the model behind the search form of `wdmg\mailer\models\Mails`.
 */
class MailsSearch extends Mails
{

    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['email_from', 'email_to', 'email_copy', 'email_subject', 'email_source', 'is_sended', 'is_viewed', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Mails::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
        ]);

        $query->andFilterWhere(['like', 'email_from', $this->email_from])
            ->andFilterWhere(['like', 'email_to', $this->email_to])
            ->andFilterWhere(['like', 'email_copy', $this->email_copy])
            ->andFilterWhere(['like', 'email_subject', $this->email_subject])
            ->andFilterWhere(['like', 'email_source', $this->email_source]);

        if ($this->status !== "*") {
            if ($this->status == 1) {
                $query->andFilterWhere(['like', 'is_sended', 1]);
            } elseif ($this->status == 2) {
                $query->andFilterWhere(['like', 'is_sended', 0]);
            } elseif ($this->status == 3) {
                $query->andFilterWhere(['like', 'is_viewed', 1]);
            } elseif ($this->status == 4) {
                $query->andFilterWhere(['like', 'is_viewed', 0]);
            } elseif ($this->status == 5) {
                $query->andFilterWhere(['like', 'is_sended', 1]);
                $query->andFilterWhere(['like', 'is_viewed', 1]);
            } elseif ($this->status == 6) {
                $query->andFilterWhere(['like', 'is_sended', 1]);
                $query->andFilterWhere(['like', 'is_viewed', 0]);
            }
        }

        return $dataProvider;
    }

}
