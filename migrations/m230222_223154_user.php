<?php

use yii\db\Migration;
use app\models\User;

class m230222_223154_user extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(User::AUTH_KEY_LENGTH)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(User::STATUS_HOLD),
            'role' => $this->string()->notNull()->defaultValue(User::ROLE_USER),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'bio_name' => $this->string()->notNull(),
            'bio_surname' => $this->string()->notNull(),
            'bio_patronymic' => $this->string(),
            'head_user_id' => $this->integer()->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_head_user',
            '{{%user}}',
            'head_user_id',
            '{{%user}}',
            'id',
            'set null'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
