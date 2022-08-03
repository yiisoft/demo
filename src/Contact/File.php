<?php

declare(strict_types=1);

namespace App\Contact;

use Attribute;
use Closure;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;

use Yiisoft\Validator\ValidationContext;


/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class File implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;

    /**
     * @var array|string|null a list of file name extensions that are allowed to be uploaded.
     * This can be either an array or a string consisting of file extension names
     * separated by space or comma (e.g. "gif, jpg").
     * Extension names are case-insensitive. Defaults to null, meaning all file name
     * extensions are allowed.
     * @see wrongExtension for the customized message for wrong file type.
     */
    public $extensions;
    /**
     * @var bool whether to check file type (extension) with mime-type. If extension produced by
     * file mime-type check differs from uploaded file extension, the file will be considered as invalid.
     */
    public $checkExtensionByMimeType = true;
    /**
     * @var array|string|null a list of file MIME types that are allowed to be uploaded.
     * This can be either an array or a string consisting of file MIME types
     * separated by space or comma (e.g. "text/plain, image/png").
     * The mask with the special character `*` can be used to match groups of mime types.
     * For example `image/*` will pass all mime types, that begin with `image/` (e.g. `image/jpeg`, `image/png`).
     * Mime type names are case-insensitive. Defaults to null, meaning all MIME types are allowed.
     * @see wrongMimeType for the customized message for wrong MIME type.
     */
    public $mimeTypes;
    /**
     * @var int|null the minimum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * @see tooSmall for the customized message for a file that is too small.
     */
    public $minSize;
    /**
     * @var int|null the maximum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * Note, the size limit is also affected by `upload_max_filesize` and `post_max_size` INI setting
     * and the 'MAX_FILE_SIZE' hidden field value. See [[getSizeLimit()]] for details.
     * @see https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize
     * @see https://www.php.net/post-max-size
     * @see getSizeLimit
     * @see tooBig for the customized message for a file that is too big.
     */
    public $maxSize;
    /**
     * @var int the maximum file count the given attribute can hold.
     * Defaults to 1, meaning single file upload. By defining a higher number,
     * multiple uploads become possible. Setting it to `0` means there is no limit on
     * the number of files that can be uploaded simultaneously.
     *
     * > Note: The maximum number of files allowed to be uploaded simultaneously is
     * also limited with PHP directive `max_file_uploads`, which defaults to 20.
     *
     * @see https://www.php.net/manual/en/ini.core.php#ini.max-file-uploads
     * @see tooMany for the customized message when too many files are uploaded.
     */
    public $maxFiles = 1;
    /**
     * @var int the minimum file count the given attribute can hold.
     * Defaults to 0. Higher value means at least that number of files should be uploaded.
     *
     * @see tooFew for the customized message when too few files are uploaded.
     * @since 2.0.14
     */
    public $minFiles = 0;
    /**
     * @var string the error message used when a file is not uploaded correctly.
     */
    public $message;
    /**
     * @var string the error message used when no file is uploaded.
     * Note that this is the text of the validation error message. To make uploading files required,
     * you have to set [[skipOnEmpty]] to `false`.
     */
    public $uploadRequired;
    public $uploadErrorPartial;
    /**
     * @var string the error message used when the uploaded file is too large.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {limit}: the maximum size allowed (see [[getSizeLimit()]])
     * - {formattedLimit}: the maximum size formatted
     *   with [[\yii\i18n\Formatter::asShortSize()|Formatter::asShortSize()]]
     */
    public $tooBig;
    /**
     * @var string the error message used when the uploaded file is too small.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {limit}: the value of [[minSize]]
     * - {formattedLimit}: the value of [[minSize]] formatted
     *   with [[\yii\i18n\Formatter::asShortSize()|Formatter::asShortSize()]
     */
    public $tooSmall;
    /**
     * @var string the error message used if the count of multiple uploads exceeds limit.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {limit}: the value of [[maxFiles]]
     */
    public $tooMany;
    /**
     * @var string the error message used if the count of multiple uploads less that minFiles.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {limit}: the value of [[minFiles]]
     *
     * @since 2.0.14
     */
    public $tooFew;
    /**
     * @var string the error message used when the uploaded file has an extension name
     * that is not listed in [[extensions]]. You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {extensions}: the list of the allowed extensions.
     */
    public $wrongExtension;
    /**
     * @var string the error message used when the file has an mime type
     * that is not allowed by [[mimeTypes]] property.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {mimeTypes}: the value of [[mimeTypes]]
     */
    public $wrongMimeType;

    public function __construct(
        private bool $skipOnEmpty = true,
        private bool $skipOnError = true,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if ($this->message === null) {
            $this->message = 'File upload failed.';
        }
        if ($this->uploadRequired === null) {
            $this->uploadRequired = 'Please upload a file. {value}';
        }
        if ($this->tooMany === null) {
            $this->tooMany = 'You can upload at most {limit, number} {limit, plural, one{file} other{files}}.';
        }
        if ($this->tooFew === null) {
            $this->tooFew = 'You should upload at least {limit, number} {limit, plural, one{file} other{files}}.';
        }
        if ($this->wrongExtension === null) {
            $this->wrongExtension = 'Only files with these extensions are allowed: {extensions}.';
        }
        if ($this->tooBig === null) {
            $this->tooBig = 'The file "{file}" is too big. Its size cannot exceed {formattedLimit}.';
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = 'The file "{file}" is too small. Its size cannot be smaller than {formattedLimit}.';
        }
        if (!is_array($this->extensions)) {
            $this->extensions = preg_split('/[\s,]+/', strtolower((string)$this->extensions), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->extensions = array_map('strtolower', $this->extensions);
        }
        if ($this->wrongMimeType === null) {
            $this->wrongMimeType = 'Only files with these MIME types are allowed: {mimeTypes}.';
        }
        if (!is_array($this->mimeTypes)) {
            $this->mimeTypes = preg_split('/[\s,]+/', strtolower((string)$this->mimeTypes), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->mimeTypes = array_map('strtolower', $this->mimeTypes);
        }
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return FileHandler::class;
    }
}
