<?php

declare(strict_types=1);

namespace App\Blog;

use Yiisoft\Hydrator\Validator\Attribute\Validate;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EditPostRequest',
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Title post'),
        new OA\Property(property: 'text', type: 'string', example: 'Text post'),
        new OA\Property(property: 'status', type: 'int', example: '1'),
    ]
)]
final class EditPostRequest extends AbstractInput implements RulesProviderInterface
{
    #[RouteArgument('id')]
    private int $id;

    #[Body('title')]
    #[Validate(new Required())]
    private string $title = '';

    #[Body('text')]
    #[Validate(new Required())]
    private string $text = '';

    #[Body('status')]
    #[Validate(new Required())]
    private int $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStatus(): PostStatus
    {
        return PostStatus::from($this->status);
    }

    public function getRules(): array
    {
        return [
            'title' => [
                new Length(min: 5, max: 255),
            ],
            'text' => [
                new Length(min: 5, max: 1000),
            ],
            'status' => [
                static function ($value): Result {
                    $result = new Result();
                    if (!PostStatus::isValid($value)) {
                        $result->addError('Incorrect status');
                    }

                    return $result;
                },
            ],
        ];
    }
}
