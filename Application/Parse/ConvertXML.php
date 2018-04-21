<?php

namespace Application\Parse;

use SimpleXMLIterator;

class ConvertXML
{
    public function xmlToArray(SimpleXMLIterator $xml): array
    {
        $a = array();

        for ($xml->rewind(); $xml->valid(); $xml->next()) {
            if (!array_key_exists($xml->key(), $a)) {
                $a[$xml->key()] = array();
            }
            if ($xml->hasChildren()) {
                $a[$xml->key()][] = $this->xmlToArray($xml->current());
            } else {
                $a[$xml->key()] = (array) $xml->current()->attributes();
                $a[$xml->key()]['value'] = strval($xml->current());
            }
        }
        return $a;
    }

    public function arrayToXml(array $a)
    {
        $xml = new SimpleXMLElement(
            '<?xml version="1.0" standalone="yes"?><root></root>');
        $this->phpToXml($a, $xml);
        return $xml->asXML();
    }

    protected function phpToXml($value, &$xml)
    {
        $node = $value;
        if (is_object($node)) {
            $node = get_object_vars($node);
        }
        if (is_array($node)) {
            foreach ($node as $k => $v) {
                if (is_numeric($k)) {
                    $k = 'number' . $k;
                }
                if (is_array($v)) {
                    $newNode = $xml->addChild($k);
                    $this->phpToXml($v, $newNode);
                } elseif (is_object($v)) {
                    $newNode = $xml->addChild($k);
                    $this->phpToXml($v, $newNode);
                } else {
                    $xml->addChild($k, $v);
                }
            }
        } else {
            $xml->addChild(self::UNKNOWN_KEY, $node);
        }
    }
}
