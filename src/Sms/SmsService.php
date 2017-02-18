<?php
namespace Sms;

include_once 'Core/Config.php';
use Sms\Request\V20160927 as Sms;
use Sms\Core as Core;

/**
 * Class SmsService
 * @package Sms
 */

class SmsService
{
    /**
     * 阿里云颁发给用户的访问服务所用的密钥ID。
     * @var
     */
    protected $access_key_id;


    /**
     * 阿里云颁发给用户的访问服务所用的密钥
     * @var
     */
    protected $access_key_secret;


    /**
     * @var
     */
    protected $sms_region;


    /**短信签名
     * @var
     */
    protected $sigh_name;


    /**
     * 模板code
     * @var
     */
    protected $template_code;




    /**
     * @var
     */
    protected $profile;


    /**
     * @var
     */
    protected $client;



    public function __construct()
    {
        $this->sms_region = env('ALIYUN_MTS_REGION', 'cn-shanghai');
        $this->access_key_id = env('ALIYUN_OSS_ACCESS_ID');
        $this->access_key_secret = env('ALIYUN_OSS_ACCESS_KEY');
        $this->sigh_name = env('ALIYUN_SMS_SIGH_NAME');
        $this->template_code = env('ALIYUN_SMS_TEMPLATE_CODE');


        //初始化Client
        $this->profile = Core\DefaultProfile::getProfile($this->mts_region, $this->access_key_id, $this->access_key_secret);
        $this->client = new Core\DefaultAcsClient($this->profile);
    }


    public function send($rec_num , $params)
    {
        $request = new Sms\SingleSendSmsRequest();
        $request->setSignName($this->sigh_name);/*签名名称*/
        $request->setTemplateCode($this->template_code);/*模板code*/
        $request->setRecNum($rec_num);/*目标手机号*/
        foreach ($params as $key => $param){
            $request->setParamString("{\"$key\":\"$param\"}");/*模板变量，数字一定要转换为字符串*/
        }
        try {
            $response = $this->client->getAcsResponse($request);

            return $response;
        }
        catch (Core\ClientException  $e) {
            \Log::info($rec_num . '发送短信失败');
            \Log::error($e->getErrorCode());
            \Log::error($e->getErrorMessage());
        }
        catch (Core\ServerException  $e) {
            \Log::info($rec_num . '发送短信失败');
            \Log::error($e->getErrorCode());
            \Log::error($e->getErrorMessage());
        }
    }
}