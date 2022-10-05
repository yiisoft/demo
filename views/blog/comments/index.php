<?php

declare(strict_types=1);

use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Tag\A;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Div;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Tag\H5;
use Yiisoft\Html\Tag\I;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Widget\KeysetPagination;

/**
 * @var string $csrf
 * @var CurrentRoute $currentRoute
 * @var PaginatorInterface $paginator
 * @var TranslatorInterface $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView $this
 */

$this->setTitle($translator->translate('menu.comments-feed'));
$title = $this->getTitle();

$toolbarApplyChange = Button::tag()
    ->addClass('btn btn-success me-1')
    ->content(I::tag()->addClass('bi bi-check-all'))
    ->id('btn-apply-changes')
    ->type('submit')
    ->render();

$toolbarReset = A::tag()
    ->addAttributes(['type' => 'reset'])
    ->addClass('btn btn-danger me-1')
    ->content(I::tag()->addClass('bi bi-bootstrap-reboot'))
    ->href($urlGenerator->generate($currentRoute->getName()))
    ->id('btn-reset')
    ->render();

$toolbarSelect = Select::tag()
    ->addClass('form-select ms-3')
    ->id('pageSize')
    ->name('pageSize')
    ->optionsData(
        [
            '1' => '1',
            '5' => '5',
            '10' => '10',
            '15' => '15',
            '20' => '20',
            '25' => '25',
        ],
    )
    ->value($paginator->getPageSize())
    ->render();

// Define header gridview
$toolbar = Div::tag()
    ->addClass('container')
    ->content(
        Div::tag()
            ->addClass('row mb-3')
            ->content(
                H5::tag()
                    ->addClass('bg-primary text-white p-2')
                    ->content(
                        I::tag()->addClass('bi bi-chat-left-text')->content(' ' . $title),
                    ) . PHP_EOL .
                    Form::tag()->post($urlGenerator->generate('blog/comment/index'))->csrf($csrf)->open() . PHP_EOL .
                    Div::tag()->addClass('float-start m-2')->content($toolbarSelect)->encode(false)->render() . PHP_EOL .
                    Div::tag()->addClass('float-end m-2')->content($toolbarApplyChange . $toolbarReset)->encode(false)->render() . PHP_EOL .
                    Form::tag()->close()
            )->encode(false)->render() . PHP_EOL
    )->encode(false)->render() . PHP_EOL;
?>

<?= ListView::widget()
        ->itemView('//blog/comments/_comments')
        ->paginator($paginator)
        ->pagination(
            KeysetPagination::widget()
                ->menuClass('pagination justify-content-center')
                ->paginator($paginator)
                ->urlArguments([])
                ->urlGenerator($urlGenerator)
                ->urlName($currentRoute->getName())
                ->render(),
        )
        ->toolbar($toolbar)
        ->webView($this);

