<?php
/**
 * Yii2 cURL wrapper
 * With RESTful support.
 *
 * @category  Web-yii2
 * @package   yii2-curl
 * @author    Nils Gajsek <info@linslin.org>
 * @copyright 2013-2015 Nils Gajsek<info@linslin.org>
 * @license   http://opensource.org/licenses/MIT MIT Public
 * @version   1.0.5
 * @link      http://www.linslin.org
 *
 */

namespace app\components;

use Yii;

//use yii\helpers\Json;

/**
 * cURL class
 */
class Curl
{

    // ################################################ class vars // ################################################


    /**
     * @var string
     * Holds response data right after sending a request.
     */
    public $response = null;


    /**
     * @var integer HTTP-Status Code
     * This value will hold HTTP-Status Code. False if request was not successful.
     */
    public $responseCode = null;


    /**
     * @var array HTTP-Status Code
     * Custom options holder
     */
    private $_options = array();


    /**
     * @var array default curl options
     * Default curl options
     */
    private $_defaultOptions = array(
        CURLOPT_USERAGENT => 'Yii2-Curl-Agent',
        CURLOPT_TIMEOUT => 58,
        CURLOPT_CONNECTTIMEOUT => 58,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    );


    // ############################################### class methods // ##############################################

    /**
     * Start performing GET-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function GET($url)
    {
        return $this->_httpRequest('GET', $url);
    }


    /**
     * Start performing HEAD-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function HEAD($url)
    {
        return $this->_httpRequest('HEAD', $url);
    }


    /**
     * Start performing POST-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function POST($url)
    {
        return $this->_httpRequest('POST', $url);
    }


    /**
     * Start performing PUT-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function PUT($url)
    {
        return $this->_httpRequest('PUT', $url);
    }

    /**
     * Start performing PATCH-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function PATCH($url)
    {
        return $this->_httpRequest('PATCH', $url);
    }


    /**
     * Start performing DELETE-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     */
    public function DELETE($url)
    {
        return $this->_httpRequest('DELETE', $url);
    }


    /**
     * Set curl option
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        //set value
        if (in_array($key, $this->_defaultOptions) && $key !== CURLOPT_WRITEFUNCTION) {
            $this->_defaultOptions[$key] = $value;
        } else {
            $this->_options[$key] = $value;
        }

        //return self
        return $this;
    }


    /**
     * Unset a single curl option
     *
     * @param string $key
     *
     * @return $this
     */
    public function unsetOption($key)
    {
        //reset a single option if its set already
        if (isset($this->_options[$key])) {
            unset($this->_options[$key]);
        }

        return $this;
    }


    /**
     * Unset all curl option, excluding default options.
     *
     * @return $this
     */
    public function unsetOptions()
    {
        //reset all options
        if (isset($this->_options)) {
            $this->_options = array();
        }

        return $this;
    }


    /**
     * Total reset of options, responses, etc.
     *
     * @return $this
     */
    public function reset()
    {
        //reset all options
        if (isset($this->_options)) {
            $this->_options = array();
        }

        //reset response & status code
        $this->response = null;
        $this->responseCode = null;

        return $this;
    }


    /**
     * Return a single option
     *
     * @param string|integer $key
     * @return mixed|boolean
     */
    public function getOption($key)
    {
        //get merged options depends on default and user options
        $mergesOptions = $this->getOptions();

        //return value or false if key is not set.
        return isset($mergesOptions[$key]) ? $mergesOptions[$key] : false;
    }


    /**
     * Return merged curl options and keep keys!
     *
     * @return array
     */
    public function getOptions()
    {
        @$out =
            @$this->_options +
            @$this->_defaultOptions;
        return $out;
    }


    /**
     * Performs HTTP request
     *
     * @param string $method
     * @param string $url
     *
     * @throws \Exception if request failed
     * @throws apiException if remote server or send data failed
     *
     * @return bool|mixed|string
     */

    private function _httpRequest($method, $url)
    {
        //set request type and writer function
        $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $this->setOption(CURLOPT_NOBODY, true);
            $this->unsetOption(CURLOPT_WRITEFUNCTION);
        }

        // DEBUG
        $this->setOption(CURLINFO_HEADER_OUT, true);


        //setup error reporting and profiling
        Yii::trace('Start sending cURL-Request: ' . $url . '\n', __METHOD__);
        Yii::beginProfile($method . ' ' . $url . '#' . md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        /**
         * proceed curl
         */
        $curl = curl_init($url);
        $opt = $this->getOptions();
        @curl_setopt_array($curl, @$opt);

        $this->response = curl_exec($curl);


        if (curl_errno($curl) == 28) {
            throw new apiTimeoutException('Request timeout (CBD not response): '. curl_error($curl), curl_errno($curl));
        }

        //check if curl was successful
        if ($this->response === false) {
            throw new apiException('curl request failed: '. curl_error($curl), curl_errno($curl));
        }

        //
        if ($this->response === null || $this->response === 'NULL') {
            throw new apiException('CBD request failed (response is NULL): '. curl_error($curl), curl_errno($curl));
        }


        //retrieve response code
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (!in_array($this->responseCode, ['200', '201'])) {

            // Errors on Cluster
            if (in_array($this->responseCode, ['412'])) {
                throw new apiHttpException($this->responseCode, 'server die, try another: ' . $this->responseCode, $this->responseCode); }

            $response = ApiHelper::parseResponce($this->response);

            // Errors on sended data
            if (in_array($this->responseCode, ['415', '422', '403'])) {
                throw new apiDataException('data request error',  $this->responseCode, null, $response); }

            // Other errors
            throw new apiException('CBD error:' . $this->responseCode . '|' . $this->response, $this->responseCode, null, $response);
        }

        //stop curl
        curl_close($curl);

        //end yii debug profile
        Yii::endProfile($method . ' ' . $url . '#' . md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        //check responseCode and return data/status
        if ($this->getOption(CURLOPT_CUSTOMREQUEST) === 'HEAD') {
            return true;
        } else {
            return $this->response;
        }
    }
}