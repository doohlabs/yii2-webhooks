<?php

namespace doohlabs\webhooks\interfaces;

interface WebhookModelInterface
{
    /**
     * Returns a list of model attributes that should be sent to the webhook.
     *
     * @return array
     */
    public function webhookFields();
}