<?php

declare(strict_types=1);

namespace App\Blog;

use OpenApi\Annotations as OA;
use Yiisoft\RequestModel\RequestModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * @OA\Schema(
 *      schema="EditPostRequest",
 *
 *      @OA\Property(example="Title post", property="title", format="string"),
 *      @OA\Property(example="Text post", property="text", format="string"),
 *      @OA\Property(example=1, property="status", format="int"),
 * )
 */
final class EditPostRequest extends RequestModel implements RulesProviderInterface
{
    public function getId(): int
    {
        return (int) $this->getAttributeValue('router.id');
    }

    public function getTitle(): string
    {
        return (string) $this->getAttributeValue('body.title');
    }

    public function getText(): string
    {
        return (string) $this->getAttributeValue('body.text');
    }

    public function getStatus(): PostStatus
    {
        return PostStatus::from($this->getAttributeValue('body.status'));
    }

    public function getRules(): array
    {
        return [
            'body.title' => [
                new Required(),
                new Length(min: 5, max: 255),
            ],
            'body.text' => [
                new Required(),
                new Length(min: 5, max: 1000),
            ],
            'body.status' => [
                new Required(),
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
