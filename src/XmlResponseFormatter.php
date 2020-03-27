<?php

namespace App;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMText;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\Strings\StringHelper;

class XmlResponseFormatter implements ResponseFormatterInterface
{
    /**
     * @var string the Content-Type header for the response
     */
    private string $contentType = 'application/xml';
    /**
     * @var string the XML version
     */
    private string $version = '1.0';
    /**
     * @var string the XML encoding. If not set, it will use the value of [[Response::charset]].
     */
    private string $encoding = 'UTF-8';
    /**
     * @var string the name of the root element. If set to false, null or is empty then no root tag should be added.
     */
    private string $rootTag = 'response';
    /**
     * @var string the name of the elements that represent the array elements with numeric keys.
     */
    private string $itemTag = 'item';
    /**
     * @var bool whether to interpret objects implementing the [[\Traversable]] interface as arrays.
     * Defaults to `true`.
     */
    private bool $useTraversableAsArray = true;
    /**
     * @var bool if object tags should be added
     */
    private bool $useObjectTags = true;

    private StreamFactoryInterface $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    public function format(DeferredResponse $deferredResponse): ResponseInterface
    {
        $content = '';
        $data = $deferredResponse->getData();
        if ($data !== null) {
            $dom = new DOMDocument($this->version, $this->encoding);
            if (!empty($this->rootTag)) {
                $root = new DOMElement($this->rootTag);
                $dom->appendChild($root);
                $this->buildXml($root, $data);
            } else {
                $this->buildXml($dom, $data);
            }
            $content = $dom->saveXML();
        }
        $response = $deferredResponse->getResponse();
        $response->getBody()->write($content);

        return $response->withHeader('Content-Type', $this->contentType . ';' . $this->encoding);
    }

    /**
     * @param DOMElement $element
     * @param mixed $data
     */
    protected function buildXml($element, $data): void
    {
        if (is_array($data) ||
            ($data instanceof \Traversable && $this->useTraversableAsArray)
        ) {
            foreach ($data as $name => $value) {
                if (is_int($name) && is_object($value)) {
                    $this->buildXml($element, $value);
                } elseif (is_array($value) || is_object($value)) {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $this->buildXml($child, $value);
                } else {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $child->appendChild(new DOMText($this->formatScalarValue($value)));
                }
            }
        } elseif (is_object($data)) {
            if ($this->useObjectTags) {
                $child = new DOMElement(StringHelper::basename(get_class($data)));
                $element->appendChild($child);
            } else {
                $child = $element;
            }
            $array = [];
            foreach ($data as $name => $value) {
                $array[$name] = $value;
            }
            $this->buildXml($child, $array);
        } else {
            $element->appendChild(new DOMText($this->formatScalarValue($data)));
        }
    }

    /**
     * Formats scalar value to use in XML text node.
     *
     * @param int|string|bool|float $value a scalar value.
     * @return string string representation of the value.
     */
    protected function formatScalarValue($value): string
    {
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        if (is_float($value)) {
            return StringHelper::floatToString($value);
        }
        return (string) $value;
    }

    /**
     * Returns element name ready to be used in DOMElement if
     * name is not empty, is not int and is valid.
     *
     * Falls back to [[itemTag]] otherwise.
     *
     * @param mixed $name
     * @return string
     */
    protected function getValidXmlElementName($name): string
    {
        if (empty($name) || is_int($name) || !$this->isValidXmlName($name)) {
            return $this->itemTag;
        }

        return $name;
    }

    /**
     * Checks if name is valid to be used in XML.
     *
     * @param mixed $name
     * @return bool
     * @see http://stackoverflow.com/questions/2519845/how-to-check-if-string-is-a-valid-xml-element-name/2519943#2519943
     */
    protected function isValidXmlName($name): bool
    {
        try {
            new DOMElement($name);
            return true;
        } catch (DOMException $e) {
            return false;
        }
    }
}
