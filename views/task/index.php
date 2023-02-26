<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap5\ActiveForm;
use conquer\modal\ModalForm;
use kartik\date\DatePickerAsset;
use app\models\Task;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filterPeriod string|null */
/* @var $filterUser integer|null */

$this->title = 'Задачи';
$this->params['breadcrumbs'][] = $this->title;

$icons = (new ActionColumn())->icons;
$user = Yii::$app->user->identity;

DatePickerAsset::register($this);
?>
<div class="task-index">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Список задач</h1>

        <?= Html::a('[+] Добавить задачу', ['create'], [
            'class' => 'btn btn-success btn-sm modal-form',
        ]) ?>
    </div>

    <div class="mt-3 mb-4">
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'method' => 'get',
            'action' => [''],
        ]); ?>

        <div class="row g-3">
            <div class="col-md-3">
                <?= Html::dropDownList('period', $filterPeriod, [
                    'today' => 'На сегодня',
                    'week' => 'На неделю',
                    'future' => 'На будущее',
                ], [
                    'prompt' => '- За все время -',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= Html::dropDownList('user', $filterUser, User::getUsersList(), [
                    'prompt' => '- Все пользователи -',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= Html::submitButton('Отобразить', [
                    'class' => 'btn btn-primary',
                ]) ?>

                <?= Html::a('Очистить', ['index'], [
                    'class' => 'btn btn-secondary',
                ]) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'title',
                'content' => function ($data) {
                    /* @var $data \app\models\Task */

                    $class = '';

                    if ($data->status === Task::STATUS_CANCELED) {
                        $class = 'text-decoration-line-through';
                    }

                    if (!$class && $data->status === Task::STATUS_COMPLETED) {
                        $class = 'text-green';
                    }

                    if (!$class && $data->end_at < strtotime(date('Y-m-d'))) {
                        $class = 'text-danger';
                    }

                    return Html::tag('span', $data->title, ['class' => $class]);
                },
            ],
            [
                'attribute' => 'priority',
                'content' => function ($data) {
                    /* @var $data \app\models\Task */
                    return Task::getPriorities($data->priority);
                },
            ],
            'endAt',
            [
                'attribute' => 'performerUser.fullName',
                'header' => 'Ответственный',
            ],
            [
                'attribute' => 'status',
                'content' => function ($data) {
                    /* @var $data \app\models\Task */
                    return Task::getStatuses($data->status);
                },
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'headerOptions' => ['width' => '75'],
                'buttons' => [
                    'update' => function ($url) use ($icons) {
                        return Html::a($icons['pencil'], $url, [
                            'class' => 'modal-form',
                            'title' => 'Редактировать задачу',
                            'aria-label' => 'Редактировать задачу',
                        ]);
                    },
                    'delete' => function ($url, $model) use ($icons, $user) {
                        $disabled = $user->role !== User::ROLE_ADMIN && $user->id !== $model->author_user_id;

                        return Html::a($icons['trash'], $url, [
                            'class' => $disabled ? 'disabled-action' : '',
                            'title' => 'Удалить задачу',
                            'aria-label' => 'Удалить задачу',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>

<?php
ModalForm::widget([
    'size' => ModalForm::SIZE_LARGE,
    'selector' => '.modal-form',
    'options' => [
        'success' => "$('#task-endat').kvDatepicker({
            format: 'dd.mm.yyyy'
        });",
    ],
    'clientOptions' => [
        'id' => 'task-modal',
    ],
]);
?>