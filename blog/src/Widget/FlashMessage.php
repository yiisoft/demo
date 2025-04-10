<?php

declare(strict_types=1);

namespace App\Widget;

use Yiisoft\Bootstrap5\Alert;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Widget\Widget;

final class FlashMessage extends Widget
{
    public function __construct(private FlashInterface $flash)
    {
    }

    public function render(): string
    {
        $flashes = $this->flash->getAll();

        $html = [];
        foreach ($flashes as $type => $data) {
            foreach ($data as $message) {
                $html[] = Alert::widget()->addClass("alert-{$type} shadow")->body($message['body']);
            }
        }

        return implode($html);
    }
}
