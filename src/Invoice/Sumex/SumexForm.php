<?php

declare(strict_types=1);

namespace App\Invoice\Sumex;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;
use \DateTime;

final class SumexForm extends FormModel
{    
    private ?int $invoice=null;
    private ?int $reason=null;
    private ?string $diagnosis='';
    private ?string $observations='';
    private ?string $treatmentstart='';
    private ?string $treatmentend='';
    private ?string $casedate='';
    private ?string $casenumber='';

    public function getInvoice() : int
    {
      return $this->invoice;
    }

    public function getReason() : int
    {
      return $this->reason;
    }

    public function getDiagnosis() : string
    {
      return $this->diagnosis;
    }

    public function getObservations() : string
    {
      return $this->observations;
    }

    public function getTreatmentstart() : ?\DateTime
    {
       if (isset($this->treatmentstart) && !empty($this->treatmentstart)) {
          return new DateTime($this->treatmentstart);
       }
    }

    public function getTreatmentend() : ?\DateTime
    {
       if (isset($this->treatmentend) && !empty($this->treatmentend)) {
          return new DateTime($this->treatmentend);
       }
    }

    public function getCasedate() : ?\DateTime
    {
       if (isset($this->casedate) && !empty($this->casedate)) {
          return new DateTime($this->casedate);
       }
    }

    public function getCasenumber() : string
    {
      return $this->casenumber;
    }

    public function getFormName(): string
    {
      return '';
    }

    public function getRules(): array    {
      return [
        'invoice' => [
            Required::rule(),
        ],
        'reason' => [
            Required::rule(),
        ],
        'diagnosis' => [
            Required::rule(),
        ],
        'observations' => [
            Required::rule(),
        ],
        'treatmentstart' => [
            Required::rule(),
        ],
        'treatmentend' => [
            Required::rule(),
        ],
        'casedate' => [
            Required::rule(),
        ],
        'casenumber' => [
            Required::rule(),
        ],
    ];
}
}
