<?php

declare(strict_types=1);

namespace App\Widget;

use Closure;
use Yiisoft\Data\Paginator\OffsetPaginator as Paginator;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Widget;

final class OffsetPagination extends Widget
{
    private array $options = [];

    private ?Closure $urlGenerator = null;
    private ?Paginator $paginator = null;
    private int $pagesCount;
    private int $currentPage;
    private array $pages;
    private bool $prepared;

    public function paginator(?Paginator $paginator): self
    {
        $this->paginator = $paginator;
        $this->prepared = false;
        return $this;
    }

    public function urlGenerator(Closure $generator): self
    {
        $this->urlGenerator = $generator;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->paginator === null ? false : $this->paginator->isRequired();
    }

    /**
     * The HTML attributes for the widget container tag. The following special options are recognized.
     *
     * {@see \Yiisoft\Html\Html::renderTagAttributes()} for details on how attributes are being rendered.
     */
    public function options(array $value): self
    {
        $this->options = $value;

        return $this;
    }

    protected function run(): string
    {
        if ($this->paginator === null) {
            return '';
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = "{$this->getId()}-post-card";
        }

        $this->initOptions();
        $this->prepareButtons();

        return implode("\n", [
            Html::openTag('nav', $this->options),
            Html::openTag('ul', ['class' => 'pagination']),
            $this->renderButtons(),
            Html::closeTag('ul'),
            Html::closeTag('nav'),
        ]);
    }

    protected function prepareButtons(): void
    {
        if ($this->prepared) {
            return;
        }
        $this->pagesCount = $this->paginator->getTotalPages();
        $this->currentPage = $this->paginator->getCurrentPage();
        if ($this->pagesCount > 9) {
            if ($this->currentPage <= 4) {
                $this->pages = [...range(1, 5), null, ...range($this->pagesCount - 2, $this->pagesCount)];
            } elseif ($this->pagesCount - $this->currentPage <= 4) {
                $this->pages = [1, 2, null, ...range($this->pagesCount - 5, $this->pagesCount)];
            } else {
                $this->pages = [
                    1,
                    2,
                    null,
                    $this->currentPage - 1,
                    $this->currentPage,
                    $this->currentPage + 1,
                    null,
                    $this->pagesCount - 1,
                    $this->pagesCount,
                ];
            }
        } else {
            $this->pages = range(1, $this->pagesCount);
        }
        $this->prepared = true;
    }

    protected function renderButtons(): string
    {
        $result = '';

        // `Previous` page
        $prevUrl = $this->paginator->isOnFirstPage() ? null : $this->getPageLink($this->currentPage - 1);
        $result .= Html::openTag('li', ['class' => $prevUrl === null ? 'page-item disabled' : 'page-item']);
        $result .= Html::a('Previous', $prevUrl, ['class' => 'page-link']);
        $result .= Html::closeTag('li');

        // Numeric buttons
        foreach ($this->pages as $page) {
            $isDisabled = $this->currentPage === $page || $page === null;
            $result .= Html::openTag('li', ['class' => $isDisabled ? 'page-item disabled' : 'page-item']);
            if ($page === null) {
                $result .= Html::span('…', ['class' => 'page-link']);
            } else {
                $result .= Html::a((string)$page, $this->getPageLink($page), ['class' => 'page-link']);
            }
            $result .= Html::closeTag('li');
        }

        // `Next` page
        $nextUrl = $this->paginator->isOnLastPage() ? null : $this->getPageLink($this->currentPage + 1);
        $result .= Html::openTag('li', ['class' => $nextUrl === null ? 'page-item disabled' : 'page-item']);
        $result .= Html::a('Next', $nextUrl, ['class' => 'page-link']);
        $result .= Html::closeTag('li');

        return $result;
    }

    protected function getPageLink(int $page): ?string
    {
        return $this->urlGenerator === null ? null : (string)($this->urlGenerator)($page);
    }

    protected function initOptions(): void
    {
        Html::addCssClass($this->options, [
            'aria-label' => 'Page navigation',
        ]);
    }
}
