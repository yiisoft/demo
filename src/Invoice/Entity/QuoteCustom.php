<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use App\Invoice\Entity\CustomField;
use App\Invoice\Entity\Quote;

#[Entity(repository: \App\Invoice\QuoteCustom\QuoteCustomRepository::class)]
class QuoteCustom
{ 
    #[BelongsTo(target:CustomField::class, nullable: false)]
    private ?CustomField $custom_field = null;

    #[BelongsTo(target:Quote::class, nullable: false)]
    private ?Quote $quote = null;
    
    #[Column(type: 'primary')]
    private ?int $id =  null;
    
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $quote_id =  null;
     
    #[Column(type: 'integer(11)', nullable: false)]
    private ?int $custom_field_id =  null;
    
    #[Column(type: 'text', nullable: true)]
    private string $value =  '';
     
    public function __construct(
        int $id = null,
        int $quote_id = null,
        int $custom_field_id = null,
        string $value = ''
    )
    {
        $this->id=$id;
        $this->quote_id=$quote_id;
        $this->custom_field_id=$custom_field_id;
        $this->value=$value;
    }
    
    public function getCustomField() : ?CustomField
    {
      return $this->custom_field;
    }
    
    public function getQuote() : ?Quote
    {
      return $this->quote;
    }
    
    public function getId(): string
    {
     return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
      $this->id =  $id;
    }
    
    public function getQuote_id(): string
    {
     return (string)$this->quote_id;
    }
    
    public function setQuote_id(int $quote_id) : void
    {
      $this->quote_id =  $quote_id;
    }
    
    public function getCustom_field_id(): string
    {
     return (string)$this->custom_field_id;
    }
    
    public function setCustom_field_id(int $custom_field_id) : void
    {
      $this->custom_field_id =  $custom_field_id;
    }
    
    public function getValue(): string
    {
       return $this->value;
    }
    
    public function setValue(string $value) : void
    {
      $this->value =  $value;
    }
}