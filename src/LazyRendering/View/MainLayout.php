<?php

namespace App\LazyRendering\View;

use App\Asset\AppAsset;
use Generator;
use Psr\Http\Message\RequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;

class MainLayout
{
    private AssetManager $assetManager;
    private ?string $title = null;
    private array $metaTags = [
        ['charset' => 'UTF-8'],
        [
            'name'    => 'viewport',
            'content' => 'width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0',
        ],
        ['http-equiv' => 'X-UA-Compatible', 'content' => 'ie=edge'],
    ];

    public function __construct(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function render(iterable $content, RequestInterface $request): iterable
    {
        ob_start();
        foreach ($this->renderPage($content) as $content) {
            yield  ob_get_clean() . $content;
            ob_start();
        }
        ob_end_clean();
    }

    protected function renderPage(iterable $content): Generator
    {

        $this->assetManager->register([AppAsset::class]);

        echo '<html><head>' . ($this->title !== null ? '<title>' . Html::encode($this->title) . '</title>' : '');
        // Meta tags
        foreach ($this->metaTags as $value) {
            echo Html::tag('meta', '', $value);
        }
        // CSS files
        foreach ($this->assetManager->getCssFiles() as $value) {
            echo Html::cssFile($value['url'], $value['attributes']);
        }
        yield '</head><body><main role="main" class="container py-4">';
        // Content
        yield from $content;
        yield '</main>';
        // JS files
        foreach ($this->assetManager->getJsFiles() as $value) {
            yield Html::script($value['url'], $value['attributes']);
        }
        yield '</body></html>';
    }
}
