<?php

declare(strict_types=1);

namespace App\Invoice\InvRecurring;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class InvRecurringForm extends FormModel
{    
    
    private ?int $inv_id=null;
    private ?string $start='';
    private ?string $end='';
    private ?string $frequency='';
    private ?string $next='';

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getStart() : ?string
    {
        return $this->start;
    }

    public function getEnd() : ?string
    {
        return $this->end;
    }

    public function getFrequency() : string
    {
      return $this->frequency;
    }

    public function getNext() : ?string
    {
        return $this->next;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
        return [
        'start' => [new Required()],
        'frequency' => [new Required()],
        'next' => [new Required()],
    ];
}
}
