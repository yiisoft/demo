<?php

declare(strict_types=1);

namespace App\Invoice\ClientNote;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;
use \DateTimeImmutable;

final class ClientNoteForm extends FormModel
{   
    private ?int $client_id=null;
    private ?string $date='';
    private ?string $note='';

    public function getClient_id() : int
    {
      return $this->client_id;
    }

    public function getDate() : ?\DateTime
    {
       if (isset($this->date) && !empty($this->date)) {
          return new DateTime($this->date);
       }
       if (empty($this->date)){
            return null;        
       }
    }

    public function getNote() : string
    {
      return $this->note;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'date' => [
            Required::rule(),
        ],
        'note' => [
            Required::rule(),
        ],
    ];
}
}
