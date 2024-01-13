<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\PutUpdate\Request;

use App\Application\Blog\Entity\Post\PostStatus;
use OpenApi\Annotations as OA;
use Yiisoft\Hydrator\Validator\Attribute\Validate;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Hydrator\Temp\RouteArgument;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * @OA\Schema(
 *      schema="BlogUpdateRequest",
 *      @OA\Property(example="Title post", property="title", format="string"),
 *      @OA\Property(example="Text post", property="text", format="string"),
 *      @OA\Property(example=1, property="status", format="int"),
 * )
 */
final class Request extends AbstractInput implements RulesProviderInterface
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
