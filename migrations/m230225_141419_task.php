<?php

use yii\db\Migration;
use app\models\Task;

class m230225_141419_task extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'end_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'priority' => $this->smallInteger()->notNull()->defaultValue(Task::PRIORITY_LOW),
            'status' => $this->smallInteger()->notNull()->defaultValue(Task::STATUS_TO_FULFILLMENT),
            'author_user_id' => $this->integer()->notNull(),
            'performer_user_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_task_author_user',
            '{{%task}}',
            'author_user_id',
            '{{%user}}',
            'id'
        );

        $this->addForeignKey(
            'fk_task_performer_user',
            '{{%task}}',
            'performer_user_id',
            '{{%user}}',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%task}}');
    }
}
