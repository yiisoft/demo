<?php

declare(strict_types=1);

namespace App\Stream\Data;

class XMLConverter implements Converter
{
    public function convert($data, array $params = []): string
    {
        return $this->xml_encode($data);
    }

    private function xml_encode($data, \DOMNode $domElement = null, \DOMDocument $DOMDocument = null): string
    {
        if ($DOMDocument === null) {
            $DOMDocument = new \DOMDocument();
            $DOMDocument->formatOutput = true;
            $this->xml_encode(['data' => $data], $DOMDocument, $DOMDocument);
            return $DOMDocument->saveXML();
        }
        // To cope with embedded objects
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    } else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                } else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                $this->xml_encode($mixedElement, $node, $DOMDocument);
            }
        } else {
            $data = is_bool($data) ? ($data ? 'true' : 'false') : $data;
            $domElement->appendChild($DOMDocument->createTextNode($data));
        }
        return '';
    }
}
