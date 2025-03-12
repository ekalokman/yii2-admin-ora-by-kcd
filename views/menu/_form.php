<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ekalokman\AdminOci8\models\Menu;

/* @var $this yii\web\View */
/* @var $model ekalokman\AdminOci8\models\Menu */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'NAME')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'PARENT_NAME')->widget('yii\jui\AutoComplete',[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=>  Menu::find()->select(['NAME'])->column()
        ]
    ]) ?>

    <?= $form->field($model, 'route')->widget('yii\jui\AutoComplete',[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=> Menu::getSavedRoutes()
        ]
    ]) ?>

    <?= $form->field($model, 'ORDER')->input('number') ?>

    <?= $form->field($model, 'DATA')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
