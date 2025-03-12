<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ekalokman\AdminOci8\models\AuthItem $model
 */
$this->title = Yii::t('rbac-admin', 'Update Role').': ' . $model->NAME;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
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
