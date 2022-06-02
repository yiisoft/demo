<?php
declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;  
#[Entity(repository: \App\Invoice\InvRecurring\InvRecurringRepository::class)]
 
class InvRecurring
{   
    #[Column(type:'primary')]
    private ?int $id =  null;

    #[BelongsTo(target:\App\Invoice\Entity\Inv::class, nullable: false, fkAction: 'NO ACTION')]
    private ?Inv $inv = null;
    
    #[Column(type:'integer(11)', nullable: false)]
    private ?int $inv_id =  null;

    #[Column(type:'date', nullable: true)]
    private $start;

    #[Column(type:'date', nullable: true)]
    private $end;

    #[Column(type:'string(191)', nullable: false)]
    private string $frequency =  '';

    #[Column(type:'date', nullable: true)]
    private $next;

    public function __construct(
        int $id = null,
        int $inv_id = null,
        string $frequency = '',
        $start = '',
        $end = '',
        $next = ''
    )
    {
        $this->id=$id;
        $this->inv_id=$inv_id;
        $this->frequency=$frequency;
        $this->start=$start;
        $this->end=$end;
        $this->next=$next;
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function setId(int $id) : void
    {
        $this->id =  $id;
    }
    
    public function getInv(): Inv
    {
        return $this->inv;
    }

    public function getInv_id(): string
    {
        return (string)$this->inv_id;
    }

    public function setInv_id(int $inv_id) : void
    {
        $this->inv_id =  $inv_id;
    }
    
     //cycle 
    public function getStart() : ?DateTimeImmutable  
    {
        if (isset($this->start) && !empty($this->start)){
            return $this->start;            
        }
        if (empty($this->start)){
            return $this->start = null;
        }
    }    
    
    public function setStart(?string $start): void
    {
        $this->start = new DateTime($start);
    }
    
     //cycle 
    public function getEnd() : ?DateTimeImmutable  
    {
        if (isset($this->end) && !empty($this->end)){
            return $this->end;            
        }
        if (empty($this->end)){
            return $this->end = null;
        }
    }    
    
    public function setEnd(?string $end): void
    {
        $this->end = new DateTime($end);
    }
    
     //cycle 
    public function getNext() : ?DateTimeImmutable  
    {
        if (isset($this->next) && !empty($this->next)){
            return $this->next;            
        }
        if (empty($this->next)){
            return $this->next = null;
        }
    }    
    
    public function setNext(?string $next): void 
    {
        $this->next = new DateTime($next);
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency) : void
    {
        $this->frequency =  $frequency;
    }
}