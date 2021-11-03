<?php

namespace doohlabs\webhooks\components\dispatcher;

use doohlabs\webhooks\components\logger\Logger;
use doohlabs\webhooks\models\Webhook;
use yii\base\Component;
use yii\base\Event;
use yii\httpclient\Client;

class EventDispatcher extends Component implements EventDispatcherInterface
{
    private $userAgent = 'yii2-webhooks';

    public function dispatch(Event $event, Webhook $webhook)
    {
        $client = new Client();
        $data = [
            'model' => $webhook->getModel(),
            'modelEvent' => $webhook->getEvent(),
            'modelAttributes' => array_intersect_key($event->sender->attributes, array_flip($event->sender->webhookFields())),
        ];
        try {
            $request = $client->createRequest()
                ->setMethod($webhook->method)
                ->setHeaders([
                    'User-Agent' => $this->userAgent,
                ])
                ->setUrl($webhook->url)
                ->setData($data);
            $response = $request->send();
            Logger::log($webhook, $request, $response);
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
        }
    }
}
