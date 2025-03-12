<?php

namespace ekalokman\AdminOci8\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\components\ImoHelper;
use yii\db\Query;
/**
 * AssignmentSearch represents the model behind the search form about Assignment.
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Assignment extends Model
{
    public $ID;
    public $USERNAME;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'USERNAME'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => Yii::t('rbac-admin', 'ID'),
            'USERNAME' => Yii::t('rbac-admin', 'Username'),
            'NAME' => Yii::t('rbac-admin', 'Name'),
        ];
    }

    /**
     * Create data provider for Assignment model.
     * @param  array                        $params
     * @param  \yii\db\ActiveRecord         $class
     * @param  string                       $usernameField
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $class, $usernameField)
    {
        $query = $class::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', $usernameField, $this->USERNAME]);

        return $dataProvider;
    }
    public function searchbykcd($params, $class, $usernameField)
    {   

        $userDetail=ImoHelper::getkcd();
        $email=$userDetail->email;
        $kcdio=$userDetail->acadkuly;
        $dept_code=$userDetail->dept_code;

        // $query = $class::find();

        $query = new Query;

       
            $query->select('QUEST.QST_IMON_USER.*,');
            $query->from('QUEST.QST_IMON_USER');
            // $query->where( ['UIA.V_QST_SUPERVISOR_APPOINTMENT.SUPERVISION_CODE' => 'S']);
            // $query->where(['in', 'UIA.V_QST_SUPERVISOR_APPOINTMENT.SUPERVISION_CODE', ['S','SS']  ]);
            $query->join('INNER JOIN', 'ADMIN.STAFFINFO_VW',"QUEST.QST_IMON_USER.USERNAME=ADMIN.STAFFINFO_VW.USERNAME AND ADMIN.STAFFINFO_VW.DEPT_CODE='$dept_code'",false);

            $query->orderBy([
              'QUEST.QST_IMON_USER.USERNAME' => SORT_ASC,
              // 'UIA.STUDENT_ST.STATUS' => SORT_DESC

            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'QUEST.QST_IMON_USER.'.$usernameField, strtolower($this->USERNAME)]);
        //$query->andFilterWhere(['like', $usernameField, strtolower($this->username)]);

        return $dataProvider;
    }
}
