<?php

namespace Overtrue\CosClient\Support;

class XML
{
    public static function toArray(string $xml): array
    {
        $xml = simplexml_load_string(
            self::sanitize($xml),
            'SimpleXMLElement',
            LIBXML_NSCLEAN | LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS
        );

        return [
            $xml->getName() => self::objectToArray($xml),
        ];
    }

    public static function fromArray(array $data): bool|string
    {
        if (empty($data)) {
            return '';
        }

        $xml = new \DomDocument('1.0', 'utf-8');
        $xml->appendChild(self::convertToXml(\key($data), \reset($data), $xml));
        $xml->formatOutput = true;

        return $xml->saveXML();
    }

    /**
     * @throws \DOMException
     */
    protected static function convertToXml($root, string|array$data = [], ?\DOMDocument $xml = null): \DOMElement
    {
        $node = $xml->createElement($root);

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === '@attributes') {
                    foreach ($value as $k => $v) {
                        $node->setAttribute($k, $v);
                    }
                    continue;
                }

                if (is_array($value) && is_numeric(key($value))) {
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convertToXml($key, $v, $xml));
                    }
                } else {
                    $node->appendChild(self::convertToXml($key, $value, $xml));
                }
                unset($data[$key]);
            }
        } else {
            $node->appendChild($xml->createTextNode($data));
        }

        return $node;
    }

    protected static function objectToArray($xmlObject, array $out = []): array
    {
        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = (is_object($node) || is_array($node))
                ? self::objectToArray($node)
                : $node;
        }

        return $out;
    }

    public static function removeSpace(string $xmlContents): string
    {
        return trim(\preg_replace('/>\s*</', '><', $xmlContents));
    }

    /**
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     */
    protected static function sanitize(string $xml): string
    {
        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $xml);
    }
}
