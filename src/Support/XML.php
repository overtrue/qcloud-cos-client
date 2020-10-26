<?php

namespace Overtrue\CosClient\Support;

use SimpleXMLElement;

class XML
{
    /**
     * XML to array.
     *
     * @param  string  $xml  XML string
     *
     * @return array
     */
    public static function toArray(string $xml)
    {
        $backup = libxml_disable_entity_loader(true);

        $xml = simplexml_load_string(
            self::sanitize($xml),
            'SimpleXMLElement',
            LIBXML_NSCLEAN | LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS
        );

        $result = [
            $xml->getName() => self::objectToArray($xml),
        ];

        libxml_disable_entity_loader($backup);

        return $result;
    }

    /**
     * XML encode.
     *
     * @param  mixed  $data
     * @param  string  $root
     * @param  string  $item
     * @param  string  $attr
     * @param  string  $id
     *
     * @return string
     */
    public static function fromArray($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id')
    {
        if (\count(\array_keys($data)) == 1 && \is_string(\array_key_first($data))) {
            $root = \array_key_first($data);
        }

        $xml = new SimpleXMLElement(\sprintf("<%s/>", $root));

        array_walk_recursive($data, [$xml, 'addChild']);

        return $xml->asXML();
    }

    public static function objectToArray($xmlObject, array $out = [])
    {
        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = (is_object($node) || is_array($node))
                ? self::objectToArray($node)
                : $node;
        }

        return $out;
    }

    /**
     * Delete invalid characters in XML.
     *
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     *
     * @param  string  $xml
     *
     * @return string
     */
    public static function sanitize(string $xml)
    {
        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $xml);
    }
}
