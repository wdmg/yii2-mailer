<?php

use yii\db\Migration;

/**
 * Class m190803_225334_mails
 */
class m190803_225334_mails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%mails}}', [

            'id' => $this->bigPrimaryKey(),
            'email_from' => $this->string(64)->notNull(),
            'email_to' => $this->string(64)->notNull(),
            'email_copy' => $this->string(64)->null(),
            'email_subject' => $this->string(255)->null(),
            'email_source' => $this->string(255)->null(),

            'is_sended' => $this->boolean()->null(),
            'is_viewed' => $this->boolean()->null(),
            'tracking_key' => $this->string(32)->null(),
            'web_mail_url' => $this->string(64)->null(),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),


        ], $tableOptions);

        $this->createIndex('{{%idx-mails-emails}}', '{{%mails}}', ['email_from', 'email_to', 'email_copy', 'email_subject']);
        $this->createIndex('{{%idx-mails-status}}', '{{%mails}}', ['is_sended', 'is_viewed']);
        $this->createIndex('{{%idx-mails-author}}','{{%mails}}', ['created_by', 'updated_by'],false);

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_mails_to_users',
                '{{%mails}}',
                'created_by, updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-mails-emails}}', '{{%mails}}');
        $this->dropIndex('{{%idx-mails-status}}', '{{%mails}}');
        $this->dropIndex('{{%idx-mails-author}}', '{{%mails}}');

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_mails_to_users',
                    '{{%mails}}'
                );
            }
        }

        $this->truncateTable('{{%mails}}');
        $this->dropTable('{{%mails}}');
    }

}
