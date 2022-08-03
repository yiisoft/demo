<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;


/**
 * Validates that the value is a valid HTTP or HTTPS URL.
 *
 * Note that this rule only checks if the URL scheme and host part are correct.
 * It does not check the remaining parts of a URL.
 */
final class FileHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof File) {
            throw new UnexpectedRuleException(File::class, $rule);
        }

        if (!is_array($value)) {
            throw new RuntimeException('Value should be array of UploadedFileInterface');
        }

        foreach ($value as $attachFiles) {
            foreach ($attachFiles as $file) {
                if (!$file instanceof UploadedFileInterface) {
                    throw new RuntimeException('File should be UploadedFileInterface');
                } elseif ($file->getError() == UPLOAD_ERR_NO_FILE) {
                    $formattedMessage = $this->formatter->format(
                        $rule->uploadRequired,
                        ['attribute' => $context->getAttribute(), 'value' => $file]
                    );
                } else {
                    $error = $file->getError();
                    if ($error == UPLOAD_ERR_OK) {
                        if ($rule->maxSize !== null && $file->getSize() > $rule->getSizeLimit()) {
                            $formattedMessage = $this->formatter->format(
                                $rule->tooBig,
                                [
                                    'file' => $file->getClientFilename(),
                                    'limit' => $rule->getSizeLimit(),
                                    'formattedLimit' => $this->formatter->asShortSize($rule->getSizeLimit()),
                                ],
                            );
                        } elseif ($rule->minSize !== null && $file->getSize() < $rule->minSize) {
                            $formattedMessage = $this->formatter->format(
                                $rule->tooSmall,
                                [
                                    'file' => $file->getClientFilename(),
                                    'limit' => $rule->minSize,
                                    'formattedLimit' => Yii::$app->formatter->asShortSize($rule->minSize),
                                ],
                            );
                        } elseif (!empty($rule->extensions) && !$this->validateExtension($file)) {
                            $formattedMessage = $this->formatter->format(
                                $rule->wrongExtension,
                                ['file' => $file->name, 'extensions' => implode(', ', $rule->extensions)],
                            );
                        } elseif (!empty($rule->mimeTypes) && !$this->validateMimeType($file)) {
                            $formattedMessage = $this->formatter->format(
                                $rule->wrongMimeType,
                                ['file' => $file->name, 'mimeTypes' => implode(', ', $rule->mimeTypes)],
                            );
                        }
                    } elseif ($error == UPLOAD_ERR_INI_SIZE || $error == UPLOAD_ERR_FORM_SIZE) {
                        $formattedMessage = $this->formatter->format(
                            $rule->tooBig,
                            [
                                'file' => $file->name,
                                'limit' => $rule->getSizeLimit(),
                                'formattedLimit' => Yii::$app->formatter->asShortSize($rule->getSizeLimit()),
                            ],
                        );
                    } elseif ($error == UPLOAD_ERR_PARTIAL) {
                        $formattedMessage = $this->formatter->format(
                            $rule->uploadErrorPartial,
                            ['attribute' => $context->getAttribute(), 'value' => $file]
                        );
                    } elseif ($error == UPLOAD_ERR_NO_TMP_DIR) {
                        throw new RuntimeException('Missing the temporary folder to store the uploaded file');
                    } elseif ($error == UPLOAD_ERR_CANT_WRITE) {
                        throw new RuntimeException('Failed to write the uploaded file to disk');
                    } elseif ($error == UPLOAD_ERR_EXTENSION) {
                        throw new RuntimeException('File upload was stopped by some PHP extension');
                    }
                }
            }
        }

        $result = new Result();
        if (isset($formattedMessage)) {
            $result->addError($formattedMessage);
        }
        return $result;
    }
}
