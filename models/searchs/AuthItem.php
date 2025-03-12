<?php

namespace ekalokman\AdminOci8\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\rbac\Item;

/**
 * AuthItemSearch represents the model behind the search form about AuthItem.
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AuthItem extends Model
{

    const TYPE_ROUTE = 101;

    public $NAME;
    public $TYPE;
    public $DESCRIPTION;
    public $RULE;
    public $DATA;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['NAME', 'DESCRIPTION',], 'safe'],
            [['TYPE'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'NAME' => Yii::t('rbac-admin', 'Name'),
            'ITEM_NAME' => Yii::t('rbac-admin', 'Name'),
            'TYPE' => Yii::t('rbac-admin', 'Type'),
            'DESCRIPTION' => Yii::t('rbac-admin', 'Description'),
            'RULENAME' => Yii::t('rbac-admin', 'Rule Name'),
            'DATA' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    /**
     * Search authitem
     * @param array $params
     * @return \yii\data\ActiveDataProvider|\yii\data\ArrayDataProvider
     */
    public function search($params)
    {
        /* @var \yii\rbac\Manager $authManager */
        $authManager = Yii::$app->authManager;
        if ($this->TYPE == Item::TYPE_ROLE) {
            $items = $authManager->getRoles();
        } else {
            $items = [];
            if ($this->TYPE == Item::TYPE_PERMISSION) {
                foreach ($authManager->getPermissions() as $name => $item) {
                    if ($name[0] !== '/') {
                        $items[$name] = $item;
                    }
                }
            } else {
                foreach ($authManager->getPermissions() as $name => $item) {
                    if ($name[0] === '/') {
                        $items[$name] = $item;
                    }
                }
            }
        }
        if ($this->load($params) && $this->validate() && (trim($this->NAME) !== '' || trim($this->DESCRIPTION) !== '')) {
            $search = strtolower(trim($this->NAME));
            $desc = strtolower(trim($this->DESCRIPTION));
            $items = array_filter($items, function ($item) use ($search, $desc) {
                return (empty($search) || strpos(strtolower($item->NAME), $search) !== false) && ( empty($desc) || strpos(strtolower($item->DESCRIPTION), $desc) !== false);
            });
        }

        return new ArrayDataProvider([
            'allModels' => $items,
        ]);
    }

}
