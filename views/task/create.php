<?php
/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = 'Новая задача';
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>