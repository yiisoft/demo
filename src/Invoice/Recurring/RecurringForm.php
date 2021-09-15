<?php

declare(strict_types=1);

namespace App\Invoice\Recurring;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class RecurringForm extends FormModel
{    
    
    private ?string $start_date='';
    private ?string $end_date='';
    private ?string $frequency='';
    private ?string $next_date='';
    private ?int $inv_id=null;

    public function getStart_date() : ?\DateTime
    {
       if (isset($this->start_date) && !empty($this->start_date)) {
          return new DateTime($this->start_date);
       }
    }

    public function getEnd_date() : ?\DateTime
    {
       if (isset($this->end_date) && !empty($this->end_date)) {
          return new DateTime($this->end_date);
       }
    }

    public function getFrequency() : string
    {
      return $this->frequency;
    }

    public function getNext_date() : ?\DateTime
    {
       if (isset($this->next_date) && !empty($this->next_date)) {
          return new DateTime($this->next_date);
       }
    }

    public function getInv_id() : int
    {
      return $this->inv_id;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'start_date' => [
            Required::rule(),
        ],
        'end_date' => [
            Required::rule(),
        ],
        'frequency' => [
            Required::rule(),
        ],
        'next_date' => [
            Required::rule(),
        ],
    ];
}
}
