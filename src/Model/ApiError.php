<?php

namespace App\Model;

class ApiError
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
