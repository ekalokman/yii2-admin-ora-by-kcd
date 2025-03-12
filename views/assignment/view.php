<?php

use yii\helpers\Html;
use common\components\ImoHelper;
$role_cps_admin=ImoHelper::getRole("CPS Administrator");
$role_kcd_admin=ImoHelper::getRole("Kulliyyah Administrator");

/* @var $this yii\web\View */
/* @var $model yii\web\IdentityInterface */


if($role_cps_admin){
    $avaliable=$avaliable;
}else{
    $remove=array('CPS Administrator');
    $new_array=array_diff($avaliable['Roles'],$remove);
    $avaliable['Roles']=$new_array;
    $avaliable['Permissions']=array( );
}

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assignment-index">
    <?= Html::a(Yii::t('rbac-admin', 'Users'), ['index'], ['class'=>'btn btn-success']) ?>
    <h1><?= Yii::t('rbac-admin', 'User') ?>: <?= $model->{$usernameField} ?></h1>

    <div class="col-lg-5">
        <?= Yii::t('rbac-admin', 'Avaliable') ?>:
        <?php
        echo Html::textInput('search_av', '', ['class' => 'role-search', 'data-target' => 'avaliable']) . '<br>';
        echo Html::listBox('roles', '', $avaliable, [
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
        echo Html::textInput('search_asgn', '', ['class' => 'role-search', 'data-target' => 'assigned']) . '<br>';
        echo Html::listBox('roles', '', $assigned, [
            'id' => 'assigned',
            'multiple' => true,
            'size' => 20,
            'style' => 'width:100%']);
        ?>
    </div>
</div>
<?php
$this->render('_script',['id'=>$model->{$idField}]);
