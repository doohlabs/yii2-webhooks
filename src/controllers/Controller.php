<?php


namespace doohlabs\webhooks\controllers;

class Controller extends \yii\web\Controller
{

    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }
}
