<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\User;

/**
 * Task model
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $status
 * @property integer $priority
 * @property integer $end_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $author_user_id
 * @property integer $performer_user_id
 *
 * @property User $authorUser
 * @property User $performerUser
 *
 * @property string $endAt
 */
class Task extends ActiveRecord
{
    const STATUS_TO_FULFILLMENT = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 0;

    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 2;
    const PRIORITY_HIGH = 3;

    const SCENARIO_ONLY_STATUS = 'only_status';
    const SCENARIO_READ_ONLY = 'read_only';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_TO_FULFILLMENT],
            [['status'], 'in', 'range' => array_keys(self::getStatuses())],
            [['status'], 'required'],

            [['priority'], 'default', 'value' => self::PRIORITY_LOW],
            [['priority'], 'in', 'range' => array_keys(self::getPriorities())],
            [['priority'], 'required'],

            [['title'], 'filter', 'filter' => 'trim'],
            [['title'], 'string', 'min' => 2, 'max' => 255],
            [['title'], 'required'],

            [['description'], 'filter', 'filter' => 'trim'],
            [['description'], 'string'],
            [['description'], 'required'],

            [['end_at'], 'integer'],

            [['endAt'], 'date', 'format' => 'php:y.m.Y'],
            [['endAt'], 'required'],

            [
                ['performer_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['performer_user_id' => 'id'],
            ],

            [
                ['author_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['author_user_id' => 'id'],
            ],

            [['end_at', 'author_user_id', 'performer_user_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'priority' => 'Приоритет',
            'status' => 'Статус',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'end_at' => 'Дата окончания',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
            'author_user_id' => 'Создатель',
            'performer_user_id' => 'Исполнитель',
            'endAt' => 'Дата окончания',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_READ_ONLY] = [];
        $scenarios[self::SCENARIO_ONLY_STATUS] = ['status'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int|null $i
     * @return mixed
     */
    public static function getStatuses($i = null)
    {
        $array = [
            self::STATUS_TO_FULFILLMENT => 'К выполнению',
            self::STATUS_IN_PROGRESS => 'Выполняется',
            self::STATUS_COMPLETED => 'Выполнена',
            self::STATUS_CANCELED => 'Отменена',
        ];

        return $i === null
            ? $array
            : (isset($array[$i]) ? $array[$i] : false);
    }

    /**
     * @param int|null $i
     * @return mixed
     */
    public static function getPriorities($i = null)
    {
        $array = [
            self::PRIORITY_LOW => 'Низкий',
            self::PRIORITY_NORMAL => 'Средний',
            self::PRIORITY_HIGH => 'Высокий',
        ];

        return $i === null
            ? $array
            : (isset($array[$i]) ? $array[$i] : false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'author_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerformerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'performer_user_id']);
    }

    /**
     * @return string
     */
    public function getEndAt()
    {
        return $this->end_at
            ? date('d.m.Y', $this->end_at)
            : null;
    }

    /**
     * @param string $value
     */
    public function setEndAt($value)
    {
        $this->end_at = $value !== null
            ? strtotime($value)
            : null;
    }
}