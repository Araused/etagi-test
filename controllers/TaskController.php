<?php

namespace app\controllers;

use app\models\User;
use Yii;
use app\models\Task;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Task models.
     * @param integer|null $user
     * @param string|null $period
     * @return mixed
     */
    public function actionIndex($user = null, $period = null)
    {
        $dataProvider = $this->buildDataProvider($user, $period);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filterUser' => $user,
            'filterPeriod' => $period,
        ]);
    }

    /**
     * @param $user
     * @param $period
     * @return ActiveDataProvider
     */
    private function buildDataProvider($user, $period)
    {
        $query = Task::find()
            ->orderBy(['updated_at' => SORT_DESC]);

        if ($user !== null && $user !== '') {
            $query->andWhere([
                'performer_user_id' => (int) $user,
            ]);
        }

        if ($period !== null) {
            $oneDay = 60 * 60 * 24;
            $today = strtotime(date('Y-m-d'));

            switch ($period) {
                case 'today':
                    $query->andWhere(['end_at' => $today]);
                    break;
                case 'week':
                    $query
                        ->andWhere(['>=', 'end_at', $today])
                        ->andWhere(['<', 'end_at', $today + $oneDay * 7]);
                    break;
                case 'future':
                    $query->andWhere(['>=', 'end_at', $today + $oneDay * 7]);
                    break;
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Task([]);
        $model->author_user_id = Yii::$app->user->identity->id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if ($user->role !== User::ROLE_ADMIN && $user->id !== $model->author_user_id) {
            $model->scenario = $user->id !== $model->performer_user_id
                ? Task::SCENARIO_READ_ONLY
                : Task::SCENARIO_ONLY_STATUS;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if ($user->role === User::ROLE_ADMIN || $user->id === $model->author_user_id) {
            $model->delete();
        } else {
            throw new ForbiddenHttpException('У вас нет прав для удаления данной задачи.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Exclude layout rendering when ajax requests
     */
    public function render($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial($view, $params);
        }
        return parent::render($view, $params);
    }
}
