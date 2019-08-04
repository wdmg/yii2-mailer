<?php

namespace wdmg\mailer\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%mails}}".
 *
 * @property int $id
 * @property string $email_from
 * @property string $email_to
 * @property string $email_copy
 * @property string $email_subject
 * @property string $email_source
 * @property integer $is_sended
 * @property integer $is_viewed
 * @property string $tracking_key
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Mails extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mails}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['email_from', 'email_to', 'is_sended'], 'required'],
            [['email_from', 'email_to', 'email_copy'], 'email', 'allowName' => true],
            [['email_subject', 'email_source'], 'string', 'max' => 255],
            [['is_sended', 'is_viewed'], 'boolean'],
            [['tracking_key'], 'string', 'max' => 32],
            [['source', 'created_at', 'updated_at'], 'safe'],
        ];

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $rules[] = [['created_by', 'updated_by'], 'required'];
        }

        return $rules;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/mailer', 'ID'),
            'email_from' => Yii::t('app/modules/mailer', 'From'),
            'email_to' => Yii::t('app/modules/mailer', 'To'),
            'email_copy' => Yii::t('app/modules/mailer', 'Copy'),
            'email_subject' => Yii::t('app/modules/mailer', 'Subject'),
            'email_source' => Yii::t('app/modules/mailer', 'Source'),

            'is_sended' => Yii::t('app/modules/mailer', 'Is sended?'),
            'is_viewed' => Yii::t('app/modules/mailer', 'Is viewed?'),

            'tracking_key' => Yii::t('app/modules/mailer', 'Tracking key'),

            'created_at' => Yii::t('app/modules/mailer', 'Created at'),
            'created_by' => Yii::t('app/modules/mailer', 'Created by'),
            'updated_at' => Yii::t('app/modules/mailer', 'Updated at'),
            'updated_by' => Yii::t('app/modules/mailer', 'Updated by'),

        ];
    }


    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {

        $statuses = [
            1 => Yii::t('app/modules/mailer', 'Sended'),
            2 => Yii::t('app/modules/mailer', 'Not sended'),
            3 => Yii::t('app/modules/mailer', 'Viewed'),
            4 => Yii::t('app/modules/mailer', 'Not viewed'),
            5 => Yii::t('app/modules/mailer', 'Sended and viewed'),
            6 => Yii::t('app/modules/mailer', 'Sended and not viewed'),
        ];

        if($allStatuses)
            $statuses = \yii\helpers\ArrayHelper::merge([
                '*' => Yii::t('app/modules/mailer', 'All statuses'),
            ], $statuses);

        return $statuses;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
            return $this->hasOne(\wdmg\users\models\Users::className(), ['id' => 'created_by']);
        else
            return null;
    }
}
