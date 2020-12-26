<?php

declare(strict_types=1);

namespace App\Widget;

use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Widget\Widget;

final class FlashMessage extends Widget
{
    private FlashInterface $flash;

    public function __construct(FlashInterface $flash)
    {
        $this->flash = $flash;
    }

    public function run(): string
    {
        $flashes = $this->flash->getAll();

        $html = [];
        foreach ($flashes as $type => $data) {
            foreach ($data as $message) {
                $html[] = Alert::widget()
                    ->options(['class' => "alert-{$type} shadow"])
                    ->body($message['body'])
                ;
            }
        }

        return implode($html);
    }
}
