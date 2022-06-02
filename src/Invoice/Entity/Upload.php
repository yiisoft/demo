<?php

declare(strict_types=1); 

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;
use DateTimeImmutable;
use App\Invoice\Entity\Client;

#[Entity(repository: \App\Invoice\Upload\UploadRepository::class)]
class Upload
{
    #[BelongsTo(target:Client::class, nullable: false)]
    private ?Client $client = null;
    
    #[Column(type:'primary')]
    private ?int $id =  null;
     
    #[Column(type:'integer(11)', nullable:false)]
    private ?int $client_id =  null;
    
    #[Column(type:'string(32)', nullable:false)]
    private string $url_key =  '';
    
    #[Column(type:'longText', nullable:false)]
    private string $file_name_original =  '';
     
    #[Column(type:'longText', nullable:false)]
    private string $file_name_new =  '';
     
    #[Column(type:'date', nullable:false)]
    private  $uploaded_date;
     
    public function __construct(
        int $id = null,
        int $client_id = null,
        string $url_key = '',
        string $file_name_original = '',
        string $file_name_new = '',
        $uploaded_date = ''
    )
    {
        $this->id=$id;
        $this->client_id=$client_id;
        $this->url_key=$url_key;
        $this->file_name_original=$file_name_original;
        $this->file_name_new=$file_name_new;
        $this->uploaded_date=$uploaded_date;
    }
    
    public function getClient() : ?Client
    {
        return $this->client;
    }
    
    public function getId(): string
    {
        return (string)$this->id;
    }
    
    public function setId(int $id) : void
    {
        $this->id =  $id;
    }
    
    public function getClient_id(): string
    {
        return (string)$this->client_id;
    }
    
    public function setClient_id(int $client_id) : void
    {
        $this->client_id =  $client_id;
    }
    
    public function getUrl_key(): string
    {
        return $this->url_key;
    }
    
    public function setUrl_key(string $url_key) : void
    {
        $this->url_key =  $url_key;
    }
    
    public function getFile_name_original(): string
    {
        return $this->file_name_original;
    }
    
    public function setFile_name_original(string $file_name_original) : void
    {
        $this->file_name_original =  $file_name_original;
    }
    
    public function getFile_name_new(): string
    {
        return $this->file_name_new;
    }
    
    public function setFile_name_new(string $file_name_new) : void
    {
        $this->file_name_new =  $file_name_new;
    }
    
    public function getUploaded_date(): DateTimeImmutable
    {
      if (isset($this->uploaded_date) && !empty($this->uploaded_date)){
        return $this->uploaded_date;
     }
    }
    
    public function setUploaded_date(DateTime $uploaded_date) : void
    {
        $this->uploaded_date =  $uploaded_date;
    }
}