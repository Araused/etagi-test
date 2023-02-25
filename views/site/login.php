<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <h1 class="mt-5 mb-3">Авторизация</h1>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <?= Html::submitButton('Войти', [
                'class' => 'btn btn-primary w-100 mt-3',
                'name' => 'login-button',
            ]) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
