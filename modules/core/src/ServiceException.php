<?php
declare(strict_types=1);

namespace edwrodrig\hapi_core;

use Exception;
use Throwable;

class ServiceException extends Exception
{

    protected $systemMessage;
    protected $additionalData;

    public function __construct(string $systemMessage, string $userMessage = "An error has occurred", Throwable $previous = NULL, $additionalData = NULL) {
        parent::__construct($userMessage, 0, $previous);
        $this->additionalData = $additionalData;
        $this->systemMessage = $systemMessage;
    }

    public function getSystemMessage() : string {
        return $this->systemMessage;
    }

    public function getUserMessage() : string {
        return $this->getMessage();
    }

    public function getAdditionalData() {
        return $this->additionalData;
    }

    public function systemLogData() : array {
        return [
            'exception' => get_class($this),
            'system_msg' => $this->systemMessage,
            'data' => $this->additionalData,
            'dump' => $this->getTrace()
        ];
    }
}