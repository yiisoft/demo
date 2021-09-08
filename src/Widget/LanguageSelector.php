<?php

namespace App\Widget;

use Yiisoft\Csrf\CsrfTokenInterface;
use Yiisoft\Form\Widget\Form;use Yiisoft\Html\Html;use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\Widget;

final class LanguageSelector extends Widget
{
    private UrlGeneratorInterface $urlGenerator;
    private TranslatorInterface $translator;
    private CsrfTokenInterface $csrfToken;

    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, CsrfTokenInterface $csrfToken)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->csrfToken = $csrfToken;
    }

    protected function run(): string
    {
        $form = Form::widget()
            ->action($this->urlGenerator->generate('site/set-locale'))
            ->method('POST')
            ->options([
                'id' => 'localeForm',
                'csrf' => $this->csrfToken->getValue(),
            ]);

        $out = $form->begin();

        $select = Html::select('locale')
            ->value($this->translator->getLocale())
            ->optionsData([
                'en' => $this->translator->translate('layout.language.english'),
                'ru' => $this->translator->translate('layout.language.russian')
            ])
            ->class('form-select')
            ->attributes(['aria-label' => $this->translator->translate('layout.change_language')]);

        $out .= Html::div($select, ['class' => 'col-2 d-inline-block']);

        $out .= Html::submitButton($this->translator->translate('layout.change_language'))
            ->class('btn btn-primary');

        $out .= $form->end();
        return $out;
    }
}
