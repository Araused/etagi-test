<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $role
 * @property string $bio_name
 * @property string $bio_surname
 * @property string $bio_patronymic
 * @property integer $head_user_id
 *
 * @property User $headUser
 * @property User[] $subordinateUsers
 *
 * @property string $fullName
 * @property string $headName
 *
 * @property array $userPerformers
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_HOLD = 1;
    const STATUS_ACTIVE = 10;

    const AUTH_KEY_LENGTH = 32;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    const SCENARIO_CREATE_USER = 'create_user';

    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['status'], 'default', 'value' => self::STATUS_HOLD],
            [['status'], 'in', 'range' => array_keys(self::getStatuses())],

            [['role'], 'default', 'value' => self::ROLE_USER],
            [['role'], 'in', 'range' => array_keys(self::getRoles())],

            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'required'],
            [['username'], 'string', 'min' => 2, 'max' => 255],
            [
                ['username'],
                'unique',
                'targetClass' => User::className(),
                'message' => 'Пользователь с таким именем уже существует.',
            ],

            [['bio_name', 'bio_surname', 'bio_patronymic'], 'filter', 'filter' => 'trim'],
            [['bio_name', 'bio_surname'], 'required'],
            [['bio_name', 'bio_surname', 'bio_patronymic'], 'string', 'min' => 2, 'max' => 255],

            [
                ['head_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['head_user_id' => 'id'],
            ],

            [['password'], 'string', 'min' => 6, 'max' => 255],
            [['password'], 'required', 'on' => self::SCENARIO_CREATE_USER],

            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [
                ['email'],
                'unique',
                'targetClass' => User::className(),
                'message' => 'Пользователь с таким e-mail уже существует.',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'email' => 'E-mail',
            'created_at' => 'Добавлен',
            'updated_at' => 'Обновлен',
            'status' => 'Статус',
            'role' => 'Роль',
            'password' => 'Пароль',
            'bio_name' => 'Имя',
            'bio_surname' => 'Фамилия',
            'bio_patronymic' => 'Отчество',
            'head_user_id' => 'Руководитель',
            'fullName' => 'Имя',
            'headName' => 'Руководитель',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'status' => [
                self::STATUS_ACTIVE,
                self::STATUS_HOLD,
            ],
        ]);
    }

    /**
     * @param string $usernameOrEmail
     * @return User|array|null
     */
    public static function findByUsernameOrEmail($usernameOrEmail)
    {
        $query = static::find();

        if (strpos($usernameOrEmail, '@')) {
            $query->andWhere(["email" => $usernameOrEmail]);
        } else {
            $query->andWhere(["username" => $usernameOrEmail]);
        }

        return $query->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param int|null $i
     * @return mixed
     */
    public static function getRoles($i = null)
    {
        $array = [
            self::ROLE_USER => 'Пользователь',
            self::ROLE_ADMIN => 'Администратор',
        ];

        return $i === null
            ? $array
            : (isset($array[$i]) ? $array[$i] : false);
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
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_HOLD => 'Неактивен',
            self::STATUS_DELETED => 'Удален',
        ];

        return $i === null
            ? $array
            : (isset($array[$i]) ? $array[$i] : false);
    }

    /**
     * @return array
     */
    public static function getUsersList($id = null)
    {
        $query = parent::find()
            ->select(['id', 'username', 'bio_name', 'bio_surname']);

        if ($id !== null) {
            $query->andWhere(['<>', 'id', $id]);
        }

        return ArrayHelper::map($query->all(), 'id', function ($model) {
            return "{$model->bio_surname} {$model->bio_name} [{$model->username}]";
        });
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHeadUser()
    {
        return $this->hasOne(User::className(), ['id' => 'head_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubordinateUsers()
    {
        return $this->hasMany(User::className(), ['head_user_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUserPerformers()
    {
        return ArrayHelper::map($this->subordinateUsers, 'id', 'fullName');
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return implode(' ', [
            $this->bio_surname,
            $this->bio_name,
        ]);
    }
}