<?php

namespace Aolbrich\PhpRouter\Http\Response;

class JsonResponse extends Response
{
    public function __construct()
    {
        $this->setHeader('Content-Type', 'application/json');
    }

    public function arrayToJson(array $array): void
    {
        $jsonString = json_encode($array);
        $this->setBody($jsonString);
    }

    public function getBodyAsArray(): array
    {
        $resultArray = json_decode($this->getBody(), true);
        return $resultArray ?? [];
    }

    public function mergeToJson(array $array): void
    {
        $originalContent = $this->getBodyAsArray();
        $newContent = array_merge($originalContent, $array);
        
        $this->arrayToJson($newContent);
    }
}
