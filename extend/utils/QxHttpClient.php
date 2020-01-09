<?php
namespace utils;

class QxHttpClient
{

    /**
     *
     */
    const METHOD_GET = 'get';


    /**
     *
     */
    const METHOD_POST = 'post';


    // ================================================
    //
    // ================================================
    protected $curl = null;

    protected $options = array(
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        // CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => null,
        // CURLOPT_SSL_VERIFYHOST => $sslFlag,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_HEADER => true
    );

    protected $url = null;

    protected $method = 'get';

    protected $data = null;

    protected $errMsg = null;

    public function __construct()
    {
        // 初始化
        $this->curl = curl_init();
    }

    /**
     * 销毁curl
     *
     * @return boolean
     */
    public function destoryCurl()
    {
        if (! is_null($this->curl)) {
            curl_close($this->curl);
        }
        return true;
    }

    /**
     *
     * @return boolean|HttpResponse
     */
    public function getHttpResponse()
    {
        // 初始化错误
        $this->initErrMsg();
        $options = $this->getOptions();
        $options[CURLOPT_URL] = $this->getUrl();
        if (! isset($options[CURLOPT_SSL_VERIFYHOST])) {
            $options[CURLOPT_SSL_VERIFYHOST] = $this->isVerifyHost();
        }
        switch ($this->getMethod()) {
            case 'get':
                break;
            
            case 'post':
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $this->getData();
                break;
            
            default:
                $this->setErrMsg('请求方式目前只支持 get 与 post !');
                return false;
        }
        
        //curl_setopt_array($this->curl, $options);
		foreach($options as $k=>$v){
			curl_setopt($this->curl, $k, $v);
		}
        return new HttpResponse($this);
    }

    /**
     *
     * @return the $curl
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     *
     * @return the $options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @return the $method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @return the $data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param
     *            multitype:unknown number NULL boolean $options
     */
    public function setOptions($options)
    {
        if (! is_array($options)) {
            throw new \Exception('setopt must be array');
        }
        
        foreach ($options as $sk => $sv) {
            $this->options[$sk] = $sv;
        }
    }

    /**
     *
     * @param string $method            
     */
    public function setMethod($method)
    {
        $this->method = $method;
        switch (strtolower($method)) {
            case 'get':
                $this->method = 'get';
                break;
            
            case 'post':
                $this->method = 'post';
                break;
            
            default:
                throw new \Exception('请求方式目前只支持 get 与 post !');
                break;
        }
    }

    /**
     *
     * @param field_type $data            
     */
    public function setData($data)
    {
        if($data instanceof \JsonSerializable){
            $this->data =  $data->jsonSerialize();
        }else{
            $this->data = $data;
        }

    }

    /**
     *
     * @return the $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param field_type $url            
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    // VERIFYHOST
    protected function isVerifyHost()
    {
        $pathUrl = parse_url($this->getUrl());
        return ($pathUrl['scheme'] == 'https' || $pathUrl['scheme'] == 'ssl') ? 2 : 0;
    }

    /**
     *
     * @return the $errMsg
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     *
     * @param field_type $errMsg            
     */
    protected function setErrMsg($errMsg)
    {
        $this->errMsg = $errMsg;
    }

    /**
     *
     * @param field_type $errMsg            
     */
    protected function initErrMsg()
    {
        $this->errMsg = null;
    }
}

class HttpResponse
{

    protected $requestHeader;

    protected $header;

    protected $body;

    protected $httpCode;

    /**
     *
     * @var QxHttpClient
     */
    protected $httpClient;

    protected $error;

    protected $curlInfo;

    public function __construct($httpClient)
    {
        $this->setHttpClient($httpClient);
        $this->exec();
        $this->destoryHttpClient();
    }

    /**
     */
    protected function destoryHttpClient()
    {
        $this->getHttpClient()->destoryCurl();
    }

    protected function exec()
    {
        // 执行并获取HTML文档内容
        $curl = $this->getHttpClient()->getCurl();
        $output = curl_exec($curl);
        $this->setCurlInfo(curl_getinfo($curl));
        if (curl_errno($curl)) {
            $this->setError('连接主机' . $this->getHttpClient()
                ->getUrl() . '时发生错误: ' . curl_error($curl));
            return false;
        }
        // 判断是否打印头信息
        $options = $this->getHttpClient()->getOptions();
        // 头信息处理
        if (isset($options[CURLOPT_HEADER]) && $options[CURLOPT_HEADER]) {
            $headerLen = $this->getCurlInfo('header_size');
            $this->setHeader(substr($output, 0, $headerLen));
            $this->setBody(substr($output, $headerLen));
        } else {
            $this->setBody($output);
        }
        return true;
    }

    /**
     *
     * @return the $header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     *
     * @return the $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * @return the $httpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     *
     * @param field_type $header            
     */
    protected function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     *
     * @param field_type $body            
     */
    protected function setBody($body)
    {
        $this->body = $body;
    }

    /**
     *
     * @param field_type $httpClient            
     */
    protected function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     *
     * @param field_type $requestHeader            
     */
    protected function setRequestHeader($requestHeader)
    {
        $this->requestHeader = $requestHeader;
    }

    /**
     *
     * @param field_type $error            
     */
    protected function setError($error)
    {
        $this->error = $error;
    }

    /**
     *
     * @return the $requestHeader
     */
    public function getRequestHeader()
    {
        return $this->requestHeader;
    }

    /**
     *
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * @return the $curlInfo
     */
    public function getCurlInfo($name = null)
    {
        return empty($name) ? $this->curlInfo : $this->curlInfo[$name];
    }

    /**
     *
     * @param field_type $curlInfo            
     */
    protected function setCurlInfo($curlInfo)
    {
        $this->curlInfo = $curlInfo;
    }

    public function getHttpCode()
    {
        return $this->getCurlInfo('http_code');
    }
}