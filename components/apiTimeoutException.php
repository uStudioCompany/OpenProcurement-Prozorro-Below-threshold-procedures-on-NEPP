<?php

namespace app\components;

/**
 * Exception represents a generic exception for all purposes.
 *
 */
class apiTimeoutException extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'apiTimeoutException';
    }
}
