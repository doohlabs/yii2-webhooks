<?php

namespace doohlabs\webhooks;

use doohlabs\webhooks\components\validators\ClassConstantDefinedValidator;
use doohlabs\webhooks\models\WebhookQuery;
use yii\base\Event;
use yii\base\Exception;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'doohlabs\webhooks\controllers';

    public $defaultRoute = 'webhook/index';

    public $eventDispatcherComponentClass = 'doohlabs\webhooks\components\dispatcher\EventDispatcher';

    public $webhookClass = 'doohlabs\webhooks\models\Webhook';

    public $allowedModels = [];

    public $allowedEvents = [
        'EVENT_AFTER_DELETE',
        'EVENT_AFTER_FIND',
        'EVENT_AFTER_INSERT',
        'EVENT_AFTER_REFRESH',
        'EVENT_AFTER_UPDATE',
        'EVENT_AFTER_VALIDATE',
        'EVENT_BEFORE_DELETE',
        'EVENT_BEFORE_INSERT',
        'EVENT_BEFORE_UPDATE',
        'EVENT_BEFORE_VALIDATE',
        'EVENT_INIT',
    ];

    public $timeout = 10;

    private $webhookInterface = 'doohlabs\webhooks\interfaces\WebhookInterface';

    private $eventDispatcherInterface = 'doohlabs\webhooks\components\dispatcher\EventDispatcherInterface';

    public function init(): void
    {
        parent::init();

        $this->validateWebhookClass();

        $webhooks = $this->findWebhooks();

        if ($webhooks) {
            $this->validateWebhooks($webhooks);
            $this->validateEventDispatcherComponentClass();

            \Yii::configure(\Yii::$app, [
                'components' => [
                    'eventDispatcher' => [
                        'class' => $this->eventDispatcherComponentClass
                    ],
                ]
            ]);

            $this->attachWebhooks($webhooks);
        }
    }

    private function validateWebhookClass(): void
    {
        $class = new \ReflectionClass($this->webhookClass);
        if (!$class->implementsInterface($this->webhookInterface)) {
            throw new Exception($this->webhookClass . ' must implement ' . $this->webhookInterface);
        }

        $activeRecordClassNamespace = 'yii\db\ActiveRecord';
        if (!$class->isSubclassOf($activeRecordClassNamespace)) {
            throw new Exception($this->webhookClass . ' must extend ' . $activeRecordClassNamespace);
        }
    }

    private function validateEventDispatcherComponentClass(): void
    {
        $class = new \ReflectionClass($this->eventDispatcherComponentClass);
        if (!$class->implementsInterface($this->eventDispatcherInterface)) {
            throw new Exception($this->webhookClass . ' must implement ' . $this->webhookInterface);
        }
    }

    private function validateWebhooks($webhooks): void
    {
        $validator = new ClassConstantDefinedValidator();
        foreach ($webhooks as $webhook) {
            if (!$validator->validate($webhook->getModelEvent())) {
                throw new Exception('Event ' . $webhook->getModelEvent() . ' does not exist');
            }
        }
    }

    private function attachWebhooks(array $webhooks): void
    {
        foreach ($webhooks as $webhook) {
            Event::on($webhook->getModel(), constant($webhook->getModelEvent()), function ($event) use ($webhook) {
                $this->eventDispatcher->dispatch($event, $webhook);
            });
        }
    }

    private function findWebhooks(): array
    {
        return (new WebhookQuery($this->webhookClass))
            ->all();
    }
}
