<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var ekalokman\AdminOci8\models\AuthItem $model
 */
$this->title = $model->NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->NAME], ['class' => 'btn btn-primary']) ?>
        <?php
        echo Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->NAME], [
            'class' => 'btn btn-danger',
            'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
            'data-method' => 'post',
        ]);
        ?>
    </p>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'NAME',
            'DESCRIPTION:ntext',
            'RULENAME',
            'DATA:ntext',
        ],
    ]);
    ?>
    <div class="col-lg-5">
        <?= Yii::t('rbac-admin', 'Avaliable') ?>:
        <?php
        echo Html::textInput('search_av', '', ['class' => 'role-search', 'data-target' => 'avaliable']) . '<br>';
        echo Html::listBox('ROLES', '', $avaliable, [
            'id' => 'avaliable',
            'multiple' => true,
            'size' => 20,
            'style' => 'width:100%']);
        ?>
    </div>
    <div class="col-lg-1">
        &nbsp;<br><br>
        <?php
        echo Html::a('>>', '#', ['class' => 'btn btn-success', 'data-action' => 'assign']) . '<br>';
        echo Html::a('<<', '#', ['class' => 'btn btn-success', 'data-action' => 'delete']) . '<br>';
        ?>
    </div>
    <div class="col-lg-5">
        <?= Yii::t('rbac-admin', 'Assigned') ?>:
        <?php
        echo Html::textInput('search_asgn', '', ['class' => 'role-search','data-target' => 'assigned']) . '<br>';
        echo Html::listBox('ROLES', '', $assigned, [
            'id' => 'assigned',
            'multiple' => true,
            'size' => 20,
            'style' => 'width:100%']);
        ?>
    </div>
</div>
<?php
$this->render('_script',['name'=>$model->NAME]);
