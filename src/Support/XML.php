<?php

namespace Overtrue\CosClient\Support;

use TheNorthMemory\Xml\Transformer;

class XML
{
    public static function toArray(string $xml): array
    {
        return Transformer::toArray($xml);
    }

    public static function fromArray(array $data): bool|string
    {
        if (empty($data)) {
            return '';
        }
        
        return Transformer::toXml($data);
    }
}
