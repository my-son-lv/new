<?php

namespace App\Tools;

use Illuminate\Database\Eloquent\Model;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms extends Model{
    // static:静态方法
    // 手机短信发送
    public static function sendCode($mobile,$code){
        AlibabaCloud::accessKeyClient(env('Aliyun_Key'),env('Aliyun_Secret'))
                    ->regionId('cn-hangzhou')
                    ->asDefaultClient();
                    $TemplateParam = ['code'=>$code];
        try {
        $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                                'query' => [
                                'RegionId' => "cn-hangzhou",
                                'PhoneNumbers' => "$mobile",
                                'SignName' => "php东app",
                                'TemplateCode' => "SMS_189835628",
                                'TemplateParam' => json_encode($TemplateParam),
                                ],
                            ])
                ->request();
                return response()->json(['error'=>'200','msg'=>'send successfully']);
                print_r($result->toArray());
        
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

}




