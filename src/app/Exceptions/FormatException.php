<?php

namespace App\Exceptions;

use Symfony\Component\Debug\Exception\FlattenException;

class FormatException extends FlattenException
{
    public function getTrace()
    {
        return $this->trace;
    }

    public function setTrace($trace, $file, $line)
    {
        $this->trace = [];
        $this->trace[] = $file . ":" . $line;
        foreach ($trace as $entry) {
            $func = isset($entry['function']) ? $entry['function'] : '';
            if (!isset($entry['line'])) {
                $entry['line'] = null;
            }
            if (isset($entry['file'])) {
                $this->trace[] = $entry['file'] . ' '. $func .':' . $entry['line'];
            }
        }
    }
}
