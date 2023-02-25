<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Смена пароля: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-form">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1><?= $this->title ?></h1>
    </div>

    <?php $form = ActiveForm::begin([]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success mt-3']) ?>

    <?php ActiveForm::end(); ?>
</div>