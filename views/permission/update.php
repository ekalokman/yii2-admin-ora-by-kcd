<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ekalokman\AdminOci8\models\AuthItem */

$this->title = Yii::t('rbac-admin', 'Update Permission') . ': ' . $model->NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->NAME, 'url' => ['view', 'id' => $model->NAME]];
$this->params['breadcrumbs'][] = Yii::t('rbac-admin', 'Update');
?>
<div class="auth-item-update">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>
</div>
