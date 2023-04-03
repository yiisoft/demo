<?php

declare(strict_types=1);

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var string $csrf
 * @var CurrentRoute $currentRoute
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var array $data
 * @var WebView $this
 */
?>
<div>Cache panel</div>
<input placeholder="text there something" />
<pre>
    <?= print_r($data, true) ?>
</pre>
