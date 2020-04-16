<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class CustomException extends Exception
{
    protected $code;
    protected $msg;
    protected $extends;
    protected $customResp;

    public function __construct($code, $msg = '', $extends = [], $customResp = false)
    {
        parent::__construct(is_array($msg) ? json_encode($msg) : $msg, $code);
        $this->code = $code;
        $this->msg = $msg;
        $this->extends = $extends;
        $this->customResp = $customResp;
    }

    public function resp()
    {
        if ($this->customResp) {
            return response($this->msg, $this->code)->withException($this);
        }
        $msg = '';
        $args = [];
        if (is_array($this->msg)) {
            $args = $this->msg;
        } elseif (is_string($this->msg)) {
            $msg = $this->msg;
        }
        $data = '';
        if (!empty($this->extends)) {
            $data = $this->extends;
        }
        return response(output($data, $this->code, $msg, $args))->withHeaders(access_header())->withException($this);
    }
}
