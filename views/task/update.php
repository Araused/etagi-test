<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Редактирование задачи: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
