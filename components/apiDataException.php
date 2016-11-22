<?php

namespace app\components;

/**
 * Exception represents a generic exception for all purposes.
 *
 */
class apiDataException extends apiException
{

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'apiDataException';
    }
}
