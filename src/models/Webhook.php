<?php

namespace doohlabs\webhooks\models;

use doohlabs\webhooks\components\validators\ClassConstantDefinedValidator;
use doohlabs\webhooks\interfaces\WebhookInterface;
use doohlabs\webhooks\interfaces\WebhookModelInterface;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhook".
 *
 * @property int $id
 * @property string $event
 * @property string $description
 * @property string $url
 * @property string $method
 * @property int $created_at
 * @property int $updated_at
 */
class Webhook extends \yii\db\ActiveRecord implements WebhookInterface
{
    public static function tableName()
    {
        return 'webhook';
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getEventName()
    {
        return explode('::', $this->event)[1];
    }

    public function getClassName()
    {
        return explode('::', $this->event)[0];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['event', 'description'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 2083],
            [['method'], 'string', 'max' => 6],
            [['event', 'url', 'method'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            ['event', ClassConstantDefinedValidator::class, 'message' => 'Allowed format: <namespaced\Class>::<CONSTANT>'],
            ['event', 'validateModelInterface'],
            ['url', 'url'],
            ['method', 'in', 'range' => ['GET', 'POST', 'PUT', 'DELETE']],
        ];
    }

    public function httpMethodValidation()
    {
    }

    /**
     * Validates the interface of the model belonging to the event
     *
     * @param $attribute
     */
    public function validateModelInterface($attribute)
    {
        $parts = explode('::', $this->$attribute);
        if (count($parts) !== 2) {
            return;
        }

        $instance = new $parts[0]();

        if (!$instance instanceof WebhookModelInterface) {
            $this->addError($attribute, Yii::t('yii', 'Model must implement \doohlabs\webhooks\interfaces\WebhookModelInterface'));
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'description' => 'Description',
            'url' => 'Url',
            'method' => 'Method',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
