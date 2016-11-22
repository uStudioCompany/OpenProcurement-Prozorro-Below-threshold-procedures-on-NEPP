<?php

namespace app\components;

/**
 * Exception represents a generic exception for all purposes.
 *
 */
class apiException extends \Exception
{

    public $responce;

    public function __construct($message = null, $code = 0, \Exception $previous = null, $responce=[])
    {
        $this->responce = $responce;
        parent::__construct($message.print_r($responce,1), $code, $previous);
    }

    public function getErrors()
    {
        $out = '';
        if ($this->responce && isset($this->responce['body']) ) {
            if (isset($this->responce['body']['errors']) && count($this->responce['body']['errors'])) {
                foreach ($this->responce['body']['errors'] AS $err) {
                    if (is_array($err['description'])) {
                        foreach ($err['description'] AS $k=>$err2) {
                            $out .= '[' . $err['location'] . '][' . $err['name'] . '][' . $k . '] = '. print_r($err2,1); //is_array($err2) ? implode(', ',$err2) : $err2;
                        }
                    } else {
                        $out .= '[' . $err['location'] . '][' . $err['name'] . '] = ' . $err['description'];
                    }
                }
            }
        }
        return $out;
    }

    public function getResponse()
    {
        return '['. print_r($this->responce,1) .']';
    }


    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'apiException';
    }
}
