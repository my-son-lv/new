<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PharIo\Manifest\Extension;
use App\Exceptions\ApiException;        // 异常类
use App\ApiModel\User;                  //用户表
use App\ApiModel\User_mess as mess;     //手机号表
use App\Tools\Sms;                      // 手机号类
class LoginControlle extends Controller
{
    //注册
    public function login(){
        //接收值
        $user_name=request()->post('user_name');  //用户名
        $user_pwd=request()->post('user_pwd');  //密码
        $auth_cell=request()->post('auth_cell');  //手机号
        $auth_code=request()->post('auth_code');  //手机验证码
        // 数据校验
        $messages = [
        'user_name.required'=>'用户名必填',
        'user_name.unique'=>'用户名不能重复',
        'user_name.max'=>'用户名长度限制',
        'user_pwd.required'=>'密码必填',
        'auth_cell.required'=>'手机号不能为空',
        'auth_cell.unique'=>'手机号已注册',
        'auth_cell.regex'=>'手机号格式错误',
        'auth_code.required'=>'验证码不能为空'
        ];
        // 验证 regex:/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/'
        $Validator = Validator::make(request()->post(),[
            'user_name' => 'required|unique:user|max:12',           
            'auth_cell'=>'required|unique:user|regex:/^1[345789][0-9]{9}$/',
            'auth_code'=>'required',
            'user_pwd'=>'required',
        ],$messages);
        // 判断是否有误有误的话就给报错提示
        if ($Validator->fails()){
            $msg = $Validator->errors()->first();
            throw (new ApiException)->SetErrorMessage($msg,'10001');
        }
        // 验证码判断
        $select = mess::where('auth_cell','=',$auth_cell)->first();
        
        //验证验证码
        if($select['auth_code'] != $auth_code){
            // 有误
            throw (new ApiException)->SetErrorMessage('验证码输入有误','40003');
        }else if((time()>$select['auth_code_time'])){
            // 失效
            throw (new ApiException)->SetErrorMessage('验证码失效','40004');
        }
        // 入库
        $user=User::create([
                'user_name'=>$user_name,
                'user_pwd'=>md5($user_pwd),
                'reg_time'=>time(),
                'auth_cell'=>$auth_cell,
            ]);
        if($user){
            return response()->json(['error'=>'200','msg'=>'ok']);
        }else{
            throw (new ApiException)->SetErrorMessage('注册失败','40004');
        }
    }

    // 发送短信验证
    public function sum(){
        // 接收手机号
        $auth_cell = request()->post('auth_cell');
        if(empty($auth_cell)){
            // 抛出异常
            throw (new ApiException)->SetErrorMessage('缺少参数','10001');
        }
        //验证码
        $auth_code = rand(1000,9999);
        // 过期时间
        $auth_code_time = time()+300;
        // 手机号重复验证码
        $first = mess::where('auth_cell','=',$auth_cell)->update(['auth_code'=>$auth_code,'auth_code_time'=>$auth_code_time]);
        if($first>0){
            Sms::sendCode($auth_cell,$auth_code);die;
        }
        // 数据入库
        $userCre = mess::create([
            'auth_code'=>$auth_code,
            'auth_cell'=>$auth_cell,
            'auth_code_time'=>$auth_code_time,
        ]);
        if($userCre == false){
             // 抛出异常   入库失败
            throw (new ApiException)->SetErrorMessage('出现异常','40002');
        }
        // 调用手机发送短信
        $Sms = Sms::sendCode($auth_cell,$auth_code);
        if($Sms){
            return response()->json(['error'=>'200','msg'=>'发送成功']);
        }else{
           
            // 抛出异常  失败
            throw (new ApiException)->SetErrorMessage('发送失败','40004');
        }
    }

    // 图片验证码
    public function imgsums(){
        echo "123";
    }

    // 图片验证码---图片展示
    public function imgsum(){
        // Set the content-type
        header('Content-Type: image/png');
        // Create the image
        $im = imagecreatetruecolor(100, 30);
        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $white);
        $grey = imagecolorallocate($im, 128, 128, 128);
        // 验证码
        $text = ''.rand(1000,9999);
        // 图片路径
        $font = storage_path().'/Inkfree.ttf';
        
        // Add the text
        $i = 0;
        while($i < strlen($text) ){
            // 横线
            imageline($im,rand(0,10),rand(0,25),rand(90,100),rand(10,25),$grey);
            imagettftext($im, 20, -15, 11+20*$i, 21, $black,$font,$text[$i]);
            $i++;
        }
        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im);
        imagedestroy($im);
        exit;
    }



}
 