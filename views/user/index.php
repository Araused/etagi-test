<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$keyIcon = Yii::$app->params['icons']['key'] ?? null;
?>
<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1>Список пользователей</h1>

        <?= Html::a('[+] Добавить пользователя', ['create'], ['class' => 'btn btn-success btn-flat btn-sm']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '55'],
            ],
            'username',
            'fullName',
            [
                'attribute' => 'headUser.fullName',
                'header' => 'Руководитель',
            ],
            'email',
            'role',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {change-password} {delete}',
                'headerOptions' => ['width' => '75'],
                'buttons' => [
                    'change-password' => function ($url) use ($keyIcon) {
                        return Html::a($keyIcon, $url, [
                            'title' => 'Изменить пароль',
                            'aria-label' => 'Изменить пароль',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>