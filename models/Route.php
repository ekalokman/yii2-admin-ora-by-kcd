<?php
namespace ekalokman\AdminOci8\models;
/**
 * Route
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Route extends \yii\base\Model
{
    /**
     * @var string Route value. 
     */
    public $ROUTE;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            [['ROUTE'],'safe'],
        ];
    }
}
