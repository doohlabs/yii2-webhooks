<?php

namespace doohlabs\webhooks;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->setModule('webhooks', [
            'id' => 'webhooks',
            'class' => 'doohlabs\webhooks\Module'
        ]);
    }
}
