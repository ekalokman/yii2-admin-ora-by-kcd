<?php

namespace ekalokman\AdminOci8\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use ekalokman\AdminOci8\models\BizRule as MBizRule;
use ekalokman\AdminOci8\components\RouteRule;

/**
 * Description of BizRule
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class BizRule extends Model
{
    /**
     * @var string name of the rule
     */
    public $NAME;

    public function rules()
    {
        return [
            [['NAME'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'NAME' => Yii::t('rbac-admin', 'Name'),
        ];
    }

    /**
     * Search BizRule
     * @param array $params
     * @return \yii\data\ActiveDataProvider|\yii\data\ArrayDataProvider
     */
    public function search($params)
    {
        /* @var \yii\rbac\Manager $authManager */
        $authManager = Yii::$app->authManager;
        $models = [];
        $included = !($this->load($params) && $this->validate() && trim($this->NAME) !== '');
        foreach ($authManager->getRules() as $name => $item) {
            if ($name != RouteRule::RULE_NAME && ($included || stripos($item->NAME, $this->NAME) !== false)) {
                $models[$name] = new MBizRule($item);
            }
        }

        return new ArrayDataProvider([
            'allModels' => $models,
        ]);
    }
}
