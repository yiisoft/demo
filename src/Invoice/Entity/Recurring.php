<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;use App\Invoice\Entity\Inv;
  
 /**
 * @Entity(
 * repository="App\Invoice\Recurring\RecurringRepository",
 * )
 */
 
 class Recurring
 {
       
   
    /**
     * @BelongsTo(target="Inv", nullable=false, fkAction="NO ACTION")
     *
     * @var \Cycle\ORM\Promise\Reference|Inv
     */
     private $inv = null;
    
    
        /**
     * @Column(type="primary")
     */
     public ?int $id =  null;
     
    /**
     * @Column(type="date", nullable=false)
     */
     private  $start_date =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private  $end_date =  '';
     
    /**
     * @Column(type="string(255)", nullable=false)
     */
     private string $frequency =  '';
     
    /**
     * @Column(type="date", nullable=false)
     */
     private  $next_date =  '';
     
    /**
     * @Column(type="integer(11)", nullable=false)
     */
     private ?int $inv_id =  null;
     
     public function __construct(
          int $id = null,
          $start_date = '',
          $end_date = '',
         string $frequency = '',
          $next_date = '',
         int $inv_id = null
     )
     {
         $this->id=$id;
         $this->start_date=$start_date;
         $this->end_date=$end_date;
         $this->frequency=$frequency;
         $this->next_date=$next_date;
         $this->inv_id=$inv_id;
     }
    
    public function getInv() : ?Inv
 {
      return $this->inv;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getStart_date(): DateTimeImmutable
    {
      if (isset($this->start_date) && !empty($this->start_date)){
       return $this->start_date;
     };
    }
    
    public function setStart_date(DateTime $start_date) : void
    {
      $this->start_date =  $start_date;
    }
    
    public function getEnd_date(): DateTimeImmutable
    {
      if (isset($this->end_date) && !empty($this->end_date)){
       return $this->end_date;
     };
    }
    
    public function setEnd_date(DateTime $end_date) : void
    {
      $this->end_date =  $end_date;
    }
    
    public function getFrequency(): string
    {
       return $this->frequency;
    }
    
    public function setFrequency(string $frequency) : void
    {
      $this->frequency =  $frequency;
    }
    
    public function getNext_date(): DateTimeImmutable
    {
      if (isset($this->next_date) && !empty($this->next_date)){
       return $this->next_date;
     };
    }
    
    public function setNext_date(DateTime $next_date) : void
    {
      $this->next_date =  $next_date;
    }
    
    public function getInv_id(): string
    {
     return (string)$this->inv_id;
    }
    
    public function setInv_id(int $inv_id) : void
    {
      $this->inv_id =  $inv_id;
    }
}