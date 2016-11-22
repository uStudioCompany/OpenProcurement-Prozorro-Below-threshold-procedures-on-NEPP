<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * DocumentUploadTaskSearch represents the model behind the search form about `app\models\DocumentUploadTask`.
 */
class DocumentUploadTaskSearch extends DocumentUploadTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'document_type', 'status'], 'integer'],
            [['file', 'title', 'mime', 'document_id', 'tender_id', 'tender_token', 'document_of', 'related_item', 'created_at', 'upload_at', 'transaction_id'], 'safe'],
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
        $query = DocumentUploadTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'document_type' => $this->document_type,
            'created_at' => $this->created_at,
            'upload_at' => $this->upload_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'mime', $this->mime])
            ->andFilterWhere(['like', 'document_id', $this->document_id])
            ->andFilterWhere(['like', 'tender_id', $this->tender_id])
            ->andFilterWhere(['like', 'tender_token', $this->tender_token])
            ->andFilterWhere(['like', 'document_of', $this->document_of])
            ->andFilterWhere(['like', 'related_item', $this->related_item])
            ->andFilterWhere(['like', 'transaction_id', $this->transaction_id]);

        return $dataProvider;
    }
}
