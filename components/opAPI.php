<?php

namespace app\components;

use app\models\Log;
use Yii;
use yii\base\Component;
use yii\helpers\Json;

class opAPI extends Component
{

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $auth_key;

    /**
     * @var string
     */
    public $ds_auth_key;

    /**
     * @var string
     */
    public $ds_upload_url;

    /**
     * @var string
     */
    public $dev_version;

    /**
     * @var bool
     */
    public $ssl_verify;

    /**
     * @var string
     */
    public $cookie_file;

    /**
     * @var Curl
     */
    private $instance = null;


    public function init()
    {
        //Здесь мы производим инициализацию компонента, необходимые действия
        parent::init();
    }

    /**
     * @param bool|false $reset
     * @return Curl
     */
    public function getInstance($reset = false)
    {
        if (!$this->instance) {
            $this->instance = new Curl();
        }

        if ($reset) {
            $this->instance->reset();
        }

        $this->instance->
        setOption(CURLOPT_SSL_VERIFYPEER, $this->ssl_verify)->
        setOption(CURLOPT_COOKIEFILE, $this->cookie_file)->
        setOption(CURLOPT_COOKIEJAR, $this->cookie_file);

        return $this->instance;
    }

    public function ping()
    {
        if ($this->getInstance()->GET($this->url . $this->version . '/tenders')) {
            return true;
        }
        return false;
    }


    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $offset
     * @return array
     * @throws \Exception
     */
    public function tenders($data = null, $id = null, $token = null, $offset = null)
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => null,
                'reqAddId' => null,
                'reqPatch' => $token ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }

    /**
     *
     * Новая публичная точка доступа.
     *
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $offset
     * @return array
     * @throws \Exception
     */
    public function publicTendersPoint($data = null, $id = null, $token = null, $offset = null)
    {
        // переопределяем точки доступа
        $this->url = Yii::$app->params['publicTenderPoint'];
        $this->version = Yii::$app->params['publicTenderVersion'];

        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => null,
                'reqAddId' => null,
                'reqPatch' => $token ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }


    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $offset
     * @return array
     * @throws \Exception
     */
    public function contracts($data = null, $id = null, $token = null, $offset = null)
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'contracts',//tender_token
                'reqId' => $id,
                'reqAdd' => null,
                'reqAddId' => null,
                'reqPatch' => $token ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }

    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $offset
     * @return array
     * @throws \Exception
     */
    public function contractsPOST($data = null, $id = null, $token = null, $offset = null)
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'contracts',//tender_token
                'reqId' => $id,
                'reqAdd' => null,
                'reqAddId' => null,
                'reqPatch' => false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }


    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $offset
     * @return array
     * @throws \Exception
     */
    public function plans($data = null, $id = null, $token = null, $offset = null)
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'plans',
                'reqId' => $id,
                'reqAdd' => null,
                'reqAddId' => null,
                'reqPatch' => $token ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }


    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $cancellation_id
     * @param string $offset
     * @return array
     */
    public function cancellations($data = null, $id = null, $token = null, $cancellation_id = null, $offset = null) //?offset
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => 'cancellations',
                'reqAddId' => $cancellation_id,
                'reqPatch' => ($token && $cancellation_id) ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }

    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $award_id
     * @param string $offset
     * @return array
     */
    public function awards($data = null, $id = null, $token = null, $award_id = null, $offset = null) //?offset
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => 'awards',
                'reqAddId' => $award_id,
                'reqPatch' => ($token && $award_id) ? true : false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,

            ]
        );
    }

    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $lot_id
     * @param string $offset
     * @return array
     */
    public function lots($data = null, $id = null, $token = null, $lot_id = null, $offset = null) //?offset
    {
        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => 'lots',
                'reqAddId' => $lot_id,
                'reqPatch' => ($token && $lot_id) ? true : false,
                'reqDelete' => (!$data && $lot_id) ? true : false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }

    /**
     * @param string $data
     * @param string $id
     * @param string $token
     * @param string $bidId
     * @param string $offset
     * @return array
     */
    public function bids($data = null, $id = null, $token = null, $bidId= null, $offset = null) //?offset
    {

        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => 'bids',
                'reqAddId' => $bidId,
                'reqPatch' => ($token && $bidId) ? true : false,
                'reqDelete' => (!$data && $bidId) ? true : false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }

    /**
     * @param null $data
     * @param null $id
     * @param null $token
     * @param null $bidId
     * @param null $offset
     * @return array
     * @throws apiException
     */

    public function getBids($data = null, $id = null, $token = null, $bidId= null, $offset = null) //?offset
    {


        return $this->request(
            [
                'data' => $data,
                'reqPoint' => 'tenders',
                'reqId' => $id,
                'reqAdd' => 'bids',
                'reqAddId' => $bidId,
                'reqPatch' => false,
                'reqDelete' => false,
                'token' => $token,
                'offset' => $offset,
            ]
        );
    }


    /**
     * @param array $file
     * @param string $data
     * @param string $tender_id
     * @param string $token
     * @param string $cancellation_id
     * @param string $document_id
     * @return mixed
     * @throws apiException
     */
    public function tendersCancellationsDocuments($file = ['name' => null, 'title' => null, 'mime' => 'text/plain'], $data = null, $tender_id, $token, $cancellation_id = null, $document_id = null)
    {
        return $this->tendersDocuments(
            $file,
            $data,
            $tender_id . '/cancellations/' . $cancellation_id,
            $token,
            $document_id
        );
    }


    /**
     * @param $file array
     * @param string $data
     * @param $tender_id
     * @param $token
     * @param $document_id
     * @return mixed
     * @throws apiException
     * @throws \Exception
     */
    public function tendersDocuments($file = ['name' => null, 'title' => null, 'mime' => 'text/plain'], $data = null, $tender_id, $token, $document_id = null, $type = 'tenders')
    {
        $curl = $this->getInstance(true);
        $method = 'GET';
        $out = [];

        //костыль для конвертов
//        if(!strpos($tender_id,'documents')){
            $convert = ApiHelper::getBidDocumentConvert($tender_id);
//        }


//        $url = $this->url . $this->version . '/' . $type . '/' . $tender_id . '/documents' . ($document_id ? '/' . $document_id : '') . ($token ? '?acc_token=' . $token : '');
        $url = $this->url . $this->version . '/' . $type . '/' . $convert . ($document_id ? '/' . $document_id : '') . ($token ? '?acc_token=' . $token : '');

        if ($file && $file['name']) {

            if (!file_exists($file['name'])) {
                throw new \Exception(' - File NOT exist ['. $file['name'] .'] '. $_SERVER['SERVER_NAME'] .' - '. $_SERVER['SERVER_ADDR'] .' - ', 0); }

            $method = 'POST';

            if ($document_id && $document_id != NULL) {
                $method = 'PUT';
            }

            if ($file['title']) {
                $file['title'] .= '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            } else {
                $file['title'] = basename($file['name']);
            }

            $file = curl_file_create($file['name'], $file['mime'], $file['title']);

            $curl->
            setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
                'Authorization: Basic ' . $this->auth_key])->
            setOption(CURLOPT_POST, true)->
            setOption(CURLOPT_POSTFIELDS, ['file' => $file]);


        } else if ($data && $document_id && $document_id != NULL) {
            $method = 'PATCH';

            $curl->
            setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $this->auth_key])->
            setOption(CURLOPT_POST, true)->
            setOption(CURLOPT_POSTFIELDS, $data)->
            setOption(CURLOPT_RETURNTRANSFER, true);
        }

        $log = new Log();
        $log->logging($method, $url, isset($data) ? $data : '{"file" : "upload file. we did not send it, this is for log"}', 'document');
        $log->isSave();

        try {
            $response = $this->_tryRequest($curl, $method, $url);
        } catch (\Exception $e) {
            $log->responce = $e->getMessage();
            $log->isSave();
            throw new apiException('Internal Error: ' . $e->getCode() . ' 1| ' . $e->getMessage() . ' 2| ' . print_r($data, 1), $e->getCode(), $e);
        }

        $log->responce = $response;
        $log->isSave();

        $response = explode("\r\n\r\n", $response);

        $n = count($response);
        $out['headers'] = explode("\r\n", $response[$n - 2]);
        $out['body'] = json_decode($response[$n - 1], 1);
        $out['raw'] = $response[$n - 1];
        return $out;
    }


    /**
     * @param $params
     * @return array
     * @throws apiException
     */
    public function request($params)
    {
        $curl = $this->getInstance(true);
        $method = 'GET';

        $curl->setOption(CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->auth_key]);

        $out = [];
        $url =
            $this->url .
            $this->version . '/' .
            $params['reqPoint'] .                                                // Точка входа  tenders|plans
            ($params['reqId'] ? '/' . $params['reqId'] : '') .     // ID tender/plan
            ($params['reqAdd'] ? '/' . $params['reqAdd'] : '') .     // Доп.параметр, cancellations|...
            ($params['reqAddId'] ? '/' . $params['reqAddId'] : '') .     // ID Доп.параметра, cancellations|...
            ($params['token'] ? '?acc_token=' . $params['token'] :            // Access Token
                ($params['offset'] ? '?offset=' . $params['offset'] : ''));    // DATA - для списка тендеров

        if (isset($params['data']) && $params['data']) {
            $method = 'POST';

            $curl->
            setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $this->auth_key])->
            setOption(CURLOPT_POST, true)->
            setOption(CURLOPT_POSTFIELDS, $params['data'])->
            setOption(CURLOPT_RETURNTRANSFER, true);
        }

        if ($params['reqPatch'] && $params['token']) {
            $method = 'PATCH';
        }

        if ($params['reqDelete'] && $params['token']) {
            $method = 'DELETE';
            $curl->
            setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $this->auth_key])->
            setOption(CURLOPT_RETURNTRANSFER, true);
        }

        $log = new Log();
        $log->logging($method, $url, $params['data']);
        $log->isSave();

        try {
            $response = $this->_tryRequest($curl, $method, $url);
        } catch (\Exception $e) {
            $log->responce = $e->getMessage();
            $log->isSave();
            throw $e;
        }
        $log->responce = $response;
        $log->isSave();

        return ApiHelper::parseResponce($response);
    }


    /**
     * @param $curl Curl
     * @param $method string
     * @param $url string
     *
     * @throws \Exception if request failed
     *
     * @return string
     */
    public function GET($curl, $method, $url)
    {
        return $this->_tryRequest($curl, $method, $url);
    }

    /**
     * @param $curl Curl
     * @param $method string
     * @param $url string
     *
     * @return string
     */
    public function POST($curl, $method, $url)
    {
        return $this->_tryRequest($curl, $method, $url);
    }

    /**
     * @param $curl Curl
     * @param $method string
     * @param $url string
     *
     * @return string
     */
    public function PATCH($curl, $method, $url)
    {
        return $this->_tryRequest($curl, $method, $url);
    }

    /**
     * @param $curl Curl
     * @param $method string
     * @param $url string
     *
     * @throws \Exception if request failed
     *
     * @return string
     */
    private function _tryRequest($curl, $method, $url)
    {
        try {
            $response = $curl->{$method}($url);

            // --- HTTP Error
        } catch (apiHttpException $e) {

            if ($e->statusCode !== 412) {
                throw $e;
            } // не 412 ошибка

            // [412] | если 412 пытаемся перейти на другой сервер в кластере | Запрос ID сервера
            $this->ping();

            try {
                $response = $curl->{$method}($url);
            } catch (apiHttpException $e) {
                // если ошибка повторилась, выходим...
                throw new apiException('cant change server, [412|412] ' . $e->getMessage(), $e->statusCode, $e);
            }

            // --- Data error
        } catch (apiDataException $e) {
            throw $e;

            // --- Network error
        } catch (apiException $e) {
            // Попытка № 2 ...
            try {
                $response = $curl->{$method}($url);
            } catch (apiException $e) {
                throw $e;
            }
        }

        return $response;
    }


    /**
     * @param string $url
     * @param array $langs
     * @param string $format
     * @return mixed
     */
    public function getStandards($url, $langs = ['uk', 'ru', 'en'], $format = 'json')
    {
        $response = [];
        foreach ($langs AS $lang) {
            $response[$lang] = json_decode(file_get_contents($url . $lang . '.' . $format), true);
        }
        return $response;
    }

    public function getStandardsFromFile($url, $langs = ['uk', 'ru', 'en'])
    {
        $response = [];
        foreach ($langs AS $lang) {
            $response[$lang] = json_decode(file_get_contents($url), true);
        }
        return $response;
    }

    /**Register document id CDB via Document Service
     *
     *
     * @param string $hash
     * @return mixed
     * @throws apiException
     * @throws \Exception
     */
    public function RegisterTendersDocumentsDS($hash)
    {
        $curl = $this->getInstance(true);
        $method = 'POST';
        $out = [];
        $url = $this->ds_upload_url . 'register';
        $data =  Json::encode(['data' => ['hash' => 'md5:' . $hash]]);
        $curl->
        setOption(CURLOPT_HTTPHEADER, [
            'Content-Type:  application/json; charset=utf-8',
            'Authorization: Basic ' . $this->ds_auth_key])->
        setOption(CURLOPT_POST, true)->
        setOption(CURLOPT_POSTFIELDS, $data)->
        setOption(CURLOPT_RETURNTRANSFER, true);
        $log = new Log();
        $log->logging($method, $url, $data, 'document');
        $log->isSave();
        try {
            $response = $this->_tryRequest($curl, $method, $url);
        } catch (\Exception $e) {
            $log->responce = $e->getMessage();
            $log->isSave();
            throw new apiException('Internal Error: ' . $e->getCode() . ' 1| ' . $e->getMessage() . ' 2| ' . print_r($data, 1), $e->getCode(), $e);
        }
        $log->responce = $response;
        $log->isSave();
        $response = explode("\r\n\r\n", $response);
        $n = count($response);
        $out['headers'] = explode("\r\n", $response[$n - 2]);
        $out['body'] = json_decode($response[$n - 1], 1);
        $out['raw'] = $response[$n - 1];
        return $out;
    }

    /**Add document information via Document Service
     *
     *
     * @param $file array
     * @param $data string
     * @param $tender_id
     * @param $token
     * @param $document_id
     * @return mixed
     * @throws apiException
     * @throws \Exception
     */
    public function AddTendersDocumentsInApiDS($file = ['name' => null, 'title' => null, 'mime' => 'text/plain'], $data, $tender_id, $token, $document_id = null, $type = 'tender')
    {
        $curl = $this->getInstance(true);
        $method = 'GET';
        $out = [];
        //костыль для конвертов
//        if(!strpos($tender_id,'documents')){
        $convert = ApiHelper::getBidDocumentConvert($tender_id);
//        }
        //формируем ссылку для запроса
        $url = $this->url . $this->version . '/' . $type . '/' . $convert . ($document_id ? '/' . $document_id : '') . ($token ? '?acc_token=' . $token : '');
        if ($file && $file['name']) {
            //если документ заливаем в первый раз
            $method = 'POST';
            if ($document_id && $document_id != NULL) {
                $method = 'PATCH';
            }
            if (!file_exists($file['name'])) {
                throw new \Exception(' - File NOT exist [' . $file['name'] . '] ' . $_SERVER['SERVER_NAME'] . ' - ' . $_SERVER['SERVER_ADDR'] . ' - ', 0);
            }
            $hash = md5_file($file['name']);
            //регестрируем файл и получае юрл для его загрузки и просмотра
            $registerRaw = Json::decode(self::RegisterTendersDocumentsDS($hash)['raw'], true);
            $regData['url'] = $registerRaw['data']['url'];
            $regData['hash'] = $registerRaw['data']['hash'];
            $regData['upload_url'] = $registerRaw['upload_url'];
            //проверяем информацию по регистрации файла
            if (!is_null($regData['hash']) && !is_null($regData['url'])) {
                if ($file['title']) {
                    $file['title'] .= '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                } else {
                    $file['title'] = basename($file['name']);
                }
                $data['data']['url'] = $regData['url'];
                $data['data']['title'] = $file['title'];
                $data['data']['hash'] = $regData['hash'];
                $data['data']['format'] = $file['mime'];
            }
        }
        $data = Json::encode($data);
        $curl->
        setOption(CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->auth_key])->
        setOption(CURLOPT_POST, true)->
        setOption(CURLOPT_POSTFIELDS, $data)->
        setOption(CURLOPT_RETURNTRANSFER, true);
        $log = new Log();
        $log->logging($method, $url, $data, 'document');
        $log->isSave();
        try {
            $response = $this->_tryRequest($curl, $method, $url);
        } catch (\Exception $e) {
            $log->responce = $e->getMessage();
            $log->isSave();
            throw new apiException('Internal Error: ' . $e->getCode() . ' 1| ' . $e->getMessage() . ' 2| ' . print_r($data, 1), $e->getCode(), $e);
        }
        $log->responce = $response;
        $log->isSave();
        $response = explode("\r\n\r\n", $response);
        $n = count($response);
        $out['headers'] = explode("\r\n", $response[$n - 2]);
        $out['body'] = json_decode($response[$n - 1], 1);
        $out['raw'] = $response[$n - 1];
        $out['upload_url'] = $regData['upload_url'];
        return $out;
    }

    /**Upload tender document via Document Service
     *
     *
     * @param $file array
     * @param string $url
     * @param $document_id
     * @return mixed
     * @throws apiException
     * @throws \Exception
     */
    public function UploadTendersDocumentsDS($file = ['name' => null, 'title' => null, 'mime' => 'text/plain'], $url, $document_id = null)
    {
        $curl = $this->getInstance(true);
        $out = [];
        $method = 'POST';
        if ($file && $file['name']) {
            if (!file_exists($file['name'])) {
                throw new \Exception(' - File NOT exist [' . $file['name'] . '] ' . $_SERVER['SERVER_NAME'] . ' - ' . $_SERVER['SERVER_ADDR'] . ' - ', 0);
            }
            if ($file['title']) {
                $file['title'] .= '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            } else {
                $file['title'] = basename($file['name']);
            }
            $file = curl_file_create($file['name'], $file['mime'], $file['title']);
            $curl->
            setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
                'Authorization: Basic ' . $this->ds_auth_key])->
            setOption(CURLOPT_POST, true)->
            setOption(CURLOPT_POSTFIELDS, ['file' => $file]);
        }
        $dbData = Json::encode(['file' => 'upload file. we did not send it, this is for log.']);
        $log = new Log();
        $log->logging($method, $url, $dbData, 'document');
        $log->isSave();
        try {
            $response = $this->_tryRequest($curl, $method, $url);
        } catch (\Exception $e) {
            $log->responce = $e->getMessage();
            $log->isSave();
            throw new apiException('Internal Error: ' . $e->getCode() . ' 1| ' . $e->getMessage() . ' 2| ' . $url, $e->getCode(), $e);
        }
        $log->responce = $response;
        $log->isSave();
        $response = explode("\r\n\r\n", $response);
        $n = count($response);
        $out['headers'] = explode("\r\n", $response[$n - 2]);
        $out['body'] = json_decode($response[$n - 1], 1);
        $out['raw'] = $response[$n - 1];
        return $out;
    }

}