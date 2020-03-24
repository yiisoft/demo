<?php

namespace App\Api\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Rest\AbstractController;
use Yiisoft\Yii\Rest\RestControllerTrait;

class UserController
{
    public function list()
    {
        $class = new \stdClass();
        $class->var = 123;
        $class->string = 'test';
        $class->array = [[[123]]];

        return $class;
    }

    public function get(ServerRequestInterface $request)
    {
        return ['user_id' => $request->getAttribute('id')];
    }
}
