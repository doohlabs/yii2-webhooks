<?php

namespace doohlabs\webhooks\interfaces;

interface WebhookInterface
{
    public function getModel();

    public function getEvent();

    public function getUrl();

    public function getMethod();

    public function getModelEvent();
}
