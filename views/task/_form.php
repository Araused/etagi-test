<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\Task;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Task */
/* @var $form yii\widgets\ActiveForm */

$id = $model->isNewRecord ? null : $model->id;
$user = Yii::$app->user->identity;
$performers = $user->role === User::ROLE_ADMIN
    ? User::getUsersList()
    : Yii::$app->user->identity->userPerformers;

$readOnly = $model->scenario === Task::SCENARIO_READ_ONLY;
$disabled = $model->scenario === Task::SCENARIO_ONLY_STATUS || $readOnly;
?>

<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalToggleLabel"><?= $this->title ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
    </div>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'title')->textInput([
            'maxlength' => true,
            'disabled' => $disabled,
        ]) ?>

        <?= $form->field($model, 'description')->textarea([
            'disabled' => $disabled,
        ]) ?>

        <?= $form->field($model, 'endAt')->textInput([
            'disabled' => $disabled,
        ]) ?>

        <?= $form->field($model, 'status')->dropDownList(Task::getStatuses(), [
            'disabled' => $readOnly,
        ]) ?>

        <?= $form->field($model, 'priority')->dropDownList(Task::getPriorities(), [
            'disabled' => $disabled,
        ]) ?>

        <?= $form->field($model, 'performer_user_id')->dropDownList($performers, [
            'prompt' => '- Выберите исполнителя -',
            'disabled' => $disabled,
        ]) ?>

    </div>

    <div class="modal-footer">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-success',
            'disabled' => $readOnly,
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>