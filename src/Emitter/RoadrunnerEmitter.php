<?php
namespace App\Emitter;

use Psr\Http\Message\ResponseInterface;
use Spiral\RoadRunner\PSR7Client;
use Yiisoft\Yii\Web\Emitter\EmitterInterface;

class RoadrunnerEmitter implements EmitterInterface
{
    private $roadRunnerClient;

    public function __construct(PSR7Client $roadRunnerClient)
    {
        $this->roadRunnerClient = $roadRunnerClient;
    }

    public function emit(ResponseInterface $response, bool $withoutBody = false): bool
    {
        $this->roadRunnerClient->respond($response);
        return true;
    }
}
