<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\PostCreate\Request;

use App\Application\Blog\Entity\Post\PostStatus;
use App\Application\User\Entity\User;
use OpenApi\Annotations as OA;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Hydrator\Temp\RouteArgument;
use Yiisoft\Hydrator\Validator\Attribute\Validate;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Request as RequestParameter;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

/**
 * @OA\Schema(
 *      schema="BlogPostCreate",
 *      @OA\Property(example="Title post", property="title", format="string"),
 *      @OA\Property(example="Text post", property="text", format="string"),
 *      @OA\Property(example=1, property="status", format="int"),
 * )
 */
final class Request extends AbstractInput
{
    #[RouteArgument('id')]
    private int $id;
    #[Body('title')]
    #[Validate(new Required())]
    #[Validate(new Length(min: 5, max: 255))]
    private string $title;
    #[Body('text')]
    #[Validate(new Required())]
    #[Validate(new Length(min: 5, max: 1000))]
    private string $text;
    #[Body('status')]
    #[Validate(new Required())]
    #[Validate(new Callback([self::class, 'validateStatus']))]
    private int $status;
    #[RequestParameter(Authentication::class)]
    private User $user;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public static function validateStatus($value): Result
    {
        $result = new Result();
        if (!PostStatus::isValid($value)) {
            $result->addError('Incorrect status: ' . ($value));
        }
        return $result;
    }
}
