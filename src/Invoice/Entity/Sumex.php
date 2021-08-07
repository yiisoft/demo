<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTime;
use DateTimeImmutable;
  
 /**
* @Entity(
 * repository="App\Invoice\Sumex\SumexRepository",
 * )
 */
 
 class Sumex
 {
       
       
     /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $invoice =  null;
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $reason =  null;
     
    /**
     * @Column(type="string(500)", nullable=false)
     */
     private string $diagnosis =  '';
     
    /**
     * @Column(type="string(500)", nullable=false)
     */
     private string $observations =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $treatmentstart =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $treatmentend =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private $casedate =  '';
     
    /**
     * @Column(type="string(35)", nullable=true)
     */
     private ?string $casenumber =  '';
     
     public function __construct(
         int $invoice = null,
         int $reason = null,
         string $diagnosis = '',
         string $observations = '',
         string $treatmentstart = '',
         string$treatmentend = '',
         string $casedate = '',
         string $casenumber = ''
     )
     {
         $this->invoice=$invoice;
         $this->reason=$reason;
         $this->diagnosis=$diagnosis;
         $this->observations=$observations;
         $this->treatmentstart=$treatmentstart;
         $this->treatmentend=$treatmentend;
         $this->casedate=$casedate;
         $this->casenumber=$casenumber;
     }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getInvoice(): int
    {
       return $this->invoice;
    }
    
    public function setInvoice(int $invoice) : void
    {
      $this->invoice =  $invoice;
    }
    
    public function getReason(): int
    {
       return $this->reason;
    }
    
    public function setReason(int $reason) : void
    {
      $this->reason =  $reason;
    }
    
    public function getDiagnosis(): string
    {
       return $this->diagnosis;
    }
    
    public function setDiagnosis(string $diagnosis) : void
    {
      $this->diagnosis =  $diagnosis;
    }
    
    public function getObservations(): string
    {
       return $this->observations;
    }
    
    public function setObservations(string $observations) : void
    {
      $this->observations =  $observations;
    }
    
    public function getTreatmentstart(): ?DateTimeImmutable
    {
      if (isset($this->treatmentstart) && !empty($this->treatmentstart)){
            return $this->treatmentstart;
      };
      if (empty($this->treatmentstart)){
            return $this->treatmentstart = null;
      }
    }
    
    public function setTreatmentstart(DateTime $treatmentstart) : void
    {
            $this->treatmentstart =  $treatmentstart->format('Y-m-d');
    }
    
    public function getTreatmentend(): DateTimeImmutable
    {
      if (isset($this->treatmentend) && !empty($this->treatmentend)){
       return $this->treatmentend;
       };
    }
    
    public function setTreatmentend(DateTime $treatmentend) : void
    {
      $this->treatmentend =  $treatmentend->format('Y-m-d')    ;
    }
    
    public function getCasedate(): DateTimeImmutable
    {
      if (isset($this->casedate) && !empty($this->casedate)){
       return $this->casedate;
      };
    }
    
    public function setCasedate(DateTime $casedate) : void
    {
      $this->casedate =  $casedate;
    }
    
    public function getCasenumber(): ?string
    {
       return $this->casenumber;
    }
    
    public function setCasenumber(string $casenumber) : void
    {
      $this->casenumber =  $casenumber;
    }
}