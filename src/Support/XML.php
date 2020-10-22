<?php

namespace Overtrue\CosClient;

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
    public static function parse(string $xml)
    {
        $backup = libxml_disable_entity_loader(true);

        $result =
            self::normalize(simplexml_load_string(self::sanitize($xml), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS));

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
    public static function build($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id')
    {
        if (is_array($attr)) {
            $_attr = [];

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id);
        $xml .= "</{$root}>";

        return $xml;
    }

    /**
     * Build CDATA.
     *
     * @param  string  $string
     *
     * @return string
     */
    public static function cdata(string $string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * Object to array.
     *
     *
     * @param  SimpleXMLElement  $obj
     *
     * @return array
     */
    protected static function normalize($obj)
    {
        $result = null;

        if (is_object($obj)) {
            $obj = (array) $obj;
        }

        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $res = self::normalize($value);
                if (('@attributes' === $key) && ($key)) {
                    $result = $res; // @codeCoverageIgnore
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $obj;
        }

        return $result;
    }

    /**
     * Array to XML.
     *
     * @param  array  $data
     * @param  string  $item
     * @param  string  $id
     *
     * @return string
     */
    protected static function data2Xml(array $data, $item = 'item', $id = 'id')
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }

            $xml .= "<{$key}{$attr}>";

            if ((is_array($val) || is_object($val))) {
                $xml .= self::data2Xml((array) $val, $item, $id);
            } else {
                $xml .= is_numeric($val) ? $val : self::cdata($val);
            }

            $xml .= "</{$key}>";
        }

        return $xml;
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
