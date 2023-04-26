<?php

declare(strict_types=1);

namespace App\Blog;

use App\RouteAttribute\Route;
use OpenApi\Annotations as OA;
use Vjik\InputHttp\Attribute\Parameter\Body;
use Vjik\InputHttp\RequestModelInterface;
use Vjik\InputValidation\Attribute\PreValidate;
use Vjik\InputValidation\ValidatedModelInterface;
use Vjik\InputValidation\ValidatedModelTrait;
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
final class EditPostRequest implements RequestModelInterface, ValidatedModelInterface, RulesProviderInterface
{
    use ValidatedModelTrait;

    #[Route('id')]
    private int $id;

    #[Body('title')]
    #[PreValidate(new Required())]
    private string $title = '';

    #[Body('text')]
    #[PreValidate(new Required())]
    private string $text = '';

    #[Body('status')]
    #[PreValidate(new Required())]
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
