<?php

declare(strict_types=1);

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;
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
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\OffsetPagination;

/**
 * @var string                $csrf
 * @var CurrentRoute          $currentRoute
 * @var OffsetPaginator       $paginator
 * @var TranslatorInterface   $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var WebView               $this
 */
$this->setTitle($translator->translate('menu.users'));

// Define header gridview
$header = Div::tag()
    ->addClass('row')
    ->content(
        H5::tag()
            ->addClass('bg-primary text-white p-3 rounded-top')
            ->content(
                I::tag()->addClass('bi bi-people-fill')->content(' ' . $translator->translate('gridview.title'))
            )
    )
    ->render();

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

$toolbar = Div::tag();
?>

<div>
    <div class="text-end">
        <?= Html::a('API v1 Info', $urlGenerator->generate('api/info/v1'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API v2 Info', $urlGenerator->generate('api/info/v2'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API Users List Data', $urlGenerator->generate('api/user/index'), ['class' => 'btn btn-link'])?>
    </div>
</div>

<?= GridView::widget()
    ->columns(
        DataColumn::create()
            ->attribute('id')
            ->value(static fn (object $data) => $data->getId()),
        DataColumn::create()
            ->attribute('login')
            ->label($translator->translate('gridview.login'))
            ->value(static fn (object $data) => $data->getLogin()),
        DataColumn::create()
            ->attribute('create_at')
            ->label($translator->translate('gridview.create.at'))
            ->value(static fn (object $data) => $data->getCreatedAt()->format('r')),
        DataColumn::create()
            ->attribute('api')
            ->label($translator->translate('gridview.api'))
            ->value(
                static function (object $data) use ($urlGenerator): string {
                    return Html::a(
                        'API User Data',
                        $urlGenerator->generate('api/user/profile', ['login' => $data->getLogin()]),
                        ['target' => '_blank'],
                    )->render();
                },
            ),
        DataColumn::create()
            ->attribute('profile')
            ->label($translator->translate('gridview.profile'))
            ->value(
                static function (object $data) use ($urlGenerator): string {
                    return Html::a(
                        Html::tag('i', '', [
                            'class' => 'bi bi-person-fill ms-1',
                            'style' => 'font-size: 1.5em;',
                        ]),
                        $urlGenerator->generate('user/profile', ['login' => $data->getLogin()]),
                        ['class' => 'btn btn-link'],
                    )->render();
                },
            ),
    )
    ->header($header)
    ->id('w1-grid')
    ->paginator($paginator)
    ->pagination(
        OffsetPagination::widget()
            ->menuClass('pagination justify-content-center')
            ->paginator($paginator)
            ->urlArguments([])
            ->render(),
    )
    ->rowAttributes(['class' => 'align-middle'])
    ->summaryAttributes(['class' => 'summary text-end mb-5'])
    ->tableAttributes(['class' => 'table table-hover'])
    ->toolbar(
        Form::tag()->post($urlGenerator->generate('user/index'))->csrf($csrf)->open() .
        Div::tag()->addClass('float-start m-3')->content($toolbarSelect)->encode(false)->render() .
        Div::tag()->addClass('float-end m-3')->content($toolbarApplyChange . $toolbarReset)->encode(false)->render() .
        Form::tag()->close()
    );
