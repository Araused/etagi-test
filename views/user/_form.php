<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$id = $model->isNewRecord ? null : $model->id;
?>

<div class="user-form">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1><?= $this->title ?></h1>
    </div>

    <?php $form = ActiveForm::begin([]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?php if (!$model->password_hash): ?>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
            <?php endif; ?>

            <?= $form->field($model, 'status')->dropDownList(User::getStatuses()) ?>

            <?= $form->field($model, 'role')->dropDownList(User::getRoles()) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'bio_surname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'bio_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'bio_patronymic')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'head_user_id')->dropDownList(User::getUsersList($id), ['prompt' => '- Нет -']) ?>
        </div>
    </div>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success mt-3']) ?>

    <?php ActiveForm::end(); ?>
</div>
