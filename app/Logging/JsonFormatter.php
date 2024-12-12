<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class JsonFormatter extends BaseJsonFormatter
{
    public function format(array $record): string
    {
        $newRecord = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'message' => $record['message'],
            'data' => $record['context']
        ];
        $json = $this->toJson($this->normalize($newRecord), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }
}
