<?php

namespace doohlabs\webhooks\models;

use doohlabs\webhooks\interfaces\WebhookInterface;
use doohlabs\webhooks\interfaces\WebhookModelInterface;
use doohlabs\webhooks\Module;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhook".
 *
 * @property int $id
 * @property string $model
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

    public function getModel()
    {
        return $this->model;
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

    public function getModelEvent()
    {
        return $this->model . '::' . $this->event;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        $module = Module::getInstance();

        return [
            [['event', 'description'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 2083],
            [['method'], 'string', 'max' => 6],
            [['model'], 'string'],
            [['event', 'url', 'method'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            ['event', 'in', 'range' => $module->allowedEvents],
            ['model', 'in', 'range' => $module->allowedModels],
            ['model', 'validateModel'],
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
    public function validateModel($attribute)
    {
        if (!class_exists($this->$attribute)) {
            $this->addError($attribute, Yii::t('yii', '{model} is not defined', ['model' => $this->$attribute]));
            return;
        }

        $instance = new $this->$attribute();

        if (!$instance instanceof WebhookModelInterface) {
            $this->addError($attribute, Yii::t('yii', 'Model must implement \doohlabs\webhooks\interfaces\WebhookModelInterface'));
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'event' => 'Event',
            'description' => 'Description',
            'url' => 'Url',
            'method' => 'Method',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
