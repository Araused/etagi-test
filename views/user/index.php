<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$keyIcon = '<svg aria-hidden="true" style="display: inline-block; font-size: inherit; height: 1em; overflow: visible; vertical-align: -.125em; width: .875em;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="m13.815 14.632-4.031 4.031h-2.669v2.668h-2.668v2.668h-4.447v-4.447l9.368-9.368c-.3-.782-.474-1.687-.474-2.632 0-4.171 3.382-7.553 7.553-7.553s7.553 3.382 7.553 7.553-3.382 7.553-7.553 7.553c-.945 0-1.85-.174-2.684-.491l.052.017zm7.532-9.31c0-.001 0-.002 0-.003 0-1.474-1.195-2.668-2.668-2.668s-2.668 1.195-2.668 2.668 1.194 2.668 2.667 2.668h.001c1.472 0 2.666-1.193 2.668-2.665z"/></svg>';
?>
<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1>Список</h1>

        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success btn-flat btn-sm']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
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
                            'title'      => 'Изменить пароль',
                            'aria-label' => 'Изменить пароль',
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>