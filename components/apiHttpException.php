<?php

namespace app\components;

/**
 * Exception represents a generic exception for all purposes.
 *
 */
class apiHttpException extends apiException
{

    /**
     * @var integer HTTP status code, such as 403, 404, 500, etc.
     */
    public $statusCode;

    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     * @param array $params
     */
    public function __construct($status, $message = null, $code = 0, \Exception $previous = null, $params=[])
    {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous, $params);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'apiHttpException';
    }
}
