<?php

namespace doohlabs\webhooks\components\dispatcher;

use doohlabs\webhooks\models\Webhook;
use yii\base\Event;

interface EventDispatcherInterface
{
    public function dispatch(Event $event, Webhook $webhook);
}
