<?php

date_default_timezone_set('Asia/Yekaterinburg');

$array = [
    'yii\grid\GridView' => [
        'tableOptions' => [
            'class' => 'table table-striped table-hover',
        ],
    ],
    'conquer\modal\ModalFormAsset' => [
        'depends' => [
            'yii\bootstrap5\BootstrapPluginAsset',
        ],
    ],
];

foreach ($array as $key => $value) {
    Yii::$container->set($key, $value);
}