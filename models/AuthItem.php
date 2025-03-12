<?php

namespace ekalokman\AdminOci8\models;

use Yii;
use yii\db;
use yii\rbac\Item;
use yii\helpers\Json;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $ruleName
 * @property string $data
 *
 * @property Item $item
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AuthItem extends \yii\base\Model
{
    public $NAME;
    public $TYPE;
    public $DESCRIPTION;
    public $RULENAME;
    public $DATA;

    /**
     * @var Item
     */
    private $_item;

    /**
     * Initialize object
     * @param Item  $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
            $this->NAME = $item->name;
            $this->TYPE = $item->type;
            $this->DESCRIPTION = $item->description;
            $this->RULENAME = $item->ruleName;
            $this->DATA = $item->data === null ? null : Json::encode($item->DATA);
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RULENAME'], 'in',
                'range' => array_keys(Yii::$app->authManager->getRules()),
                'message' => 'Rule not exists'],
            [['NAME', 'TYPE'], 'required'],
            [['TYPE'], 'integer'],
            [['DESCRIPTION', 'DATA', 'RULENAME'], 'default'],
            [['NAME'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'NAME' => Yii::t('rbac-admin', 'Name'),
            'TYPE' => Yii::t('rbac-admin', 'Type'),
            'DESCRIPTION' => Yii::t('rbac-admin', 'Description'),
            'RULENAME' => Yii::t('rbac-admin', 'Rule Name'),
            'DATA' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    /**
     * Check if is new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find role
     * @param string $id
     * @return null|\self
     */
    public static function find($id)
    {
        $item = Yii::$app->authManager->getRole($id);
        if ($item !== null) {
            return new self($item);
        }

        return null;
    }

    /**
     * Save role to [[\yii\rbac\authManager]]
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Yii::$app->authManager;
            if ($this->_item === null) {
                if ($this->TYPE == Item::TYPE_ROLE) {
                    $this->_item = $manager->createRole($this->NAME);
                } else {
                    $this->_item = $manager->createPermission($this->NAME);
                }
                $isNew = true;
            } else {
                $isNew = false;
                $oldName = $this->_item->name;
            }
            $this->_item->name = $this->NAME;
            $this->_item->description = $this->DESCRIPTION;
            $this->_item->ruleName = $this->RULENAME;
            $this->_item->data = $this->DATA === null || $this->DATA === '' ? null : Json::decode($this->DATA);
            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($oldName, $this->_item);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get item
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get type name
     * @param  mixed $type
     * @return string|array
     */
    public static function getTypeName($type = null)
    {
        $result = [
            Item::TYPE_PERMISSION => 'Permission',
            Item::TYPE_ROLE => 'Role'
        ];
        if ($type === null) {
            return $result;
        }

        return $result[$type];
    }
}
