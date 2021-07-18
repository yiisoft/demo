<?php

declare(strict_types=1);

namespace App\Invoice\GeneratorRelation;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class GeneratorRelationForm extends FormModel
{
    private ?string $lowercasename = '';
    
    private ?string $camelcasename = '';
    
    private ?int $id = null;
    
    private ?int $gentor_id = null;
    
    public function getLowercase_name(): string
    {
        return $this->lowercasename;
    }
    
    public function getCamelcase_name(): string
    {
        return $this->camelcasename;
    }
    
    public function getGentor_id(): int
    {
        return $this->gentor_id;
    }
    
    public function getFormName(): string
    {
        return '';
    }
    
    public function getRules(): array
    {
        return [
            'lowercasename' => [
                Required::rule(),
            ],
            'camelcasename' => [
                Required::rule(),
            ],
            'gentor_id' => [
                Required::rule(),
            ],
        ];
    }
}
