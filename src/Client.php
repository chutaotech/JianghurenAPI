<?php

namespace ChutaoTech\Jianghuren;

use ChutaoTech\Jianghuren\Exceptions\HttpException;
use ChutaoTech\Jianghuren\Support\Config;
use ChutaoTech\Jianghuren\Traits\HasHttpRequest;

class Client
{
    use HasHttpRequest;

    const DEV_BASE_URI = 'http://ydapi.test.yduan.net/web/Post';

    const PROD_BASE_URI = 'http://ydapi.jianghuren.com/web/Post';

    protected $config;

    protected $baseUri;

    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return $this->config->get('is_dev') ? self::DEV_BASE_URI : self::PROD_BASE_URI;
    }

    /**
     * @return int
     */
    protected function getTimeout()
    {
        return 0;
    }

    /**
     * generate sign
     *
     * @param array $params
     * @return string
     */
    protected function generateSign($params)
    {
        $apiPwd = $this->config->get('api_pwd');

        return strtoupper(md5($apiPwd . strtoupper(md5(base64_encode(json_encode($params)))) . $apiPwd));
    }

    /**
     * @param $action
     * @param null $params
     * @param string $version
     * @return array
     */
    protected function createPayload($action, $params = null, $version = '1.0')
    {
        $data = [
            'Action' => $action,
            'API_KEY' => $this->config->get('api_key'),
            'Version' => $version,
        ];
        if (!empty($params)) {
            $data['Fields'] = $params;
        }

        return [
            'json' => json_encode($data),
            'sign' => $this->generateSign($data),
        ];
    }

    /**
     * 获取所有产品
     * @param array $params
     * @return array|mixed
     * @throws HttpException
     */
    public function getAllProducts($params = [])
    {
        $data = [
            'ProductName' => !empty($params['ProductName']) ? trim($params['ProductName']) : '',
            'ProductId' => !empty($params['ProductId']) ? trim($params['ProductId']) : '',
            'Scid' => !empty($params['Scid']) ? $params['Scid'] : '',
            'Scusname' => !empty($params['Scusname']) ? $params['Scusname'] : '',
        ];

        try {
            $result = $this->post('', $this->createPayload('PostProductListJson', $data, '1.0'));
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
        $result = !empty($result) ? json_decode($result, true) : [];

        return $result;
    }

    /**
     * 获取产品价格日历
     * @param $params
     * @return array|mixed
     * @throws HttpException
     */
    public function getProductPriceList($params)
    {
        $data = [
            'ProductId' => !empty($params['ProductId']) ? trim($params['ProductId']) : '',
            'InDate' => !empty($params['InDate']) ? $params['InDate'] : '',
        ];

        try {
            $result = $this->post('', $this->createPayload('POSTGETCUSPRODUCTPRICEJSON', $data, '1.0'));
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
        $result = !empty($result) ? json_decode($result, true) : [];

        return $result;
    }










}