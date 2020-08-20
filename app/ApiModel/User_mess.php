<?php

namespace App\ApiModel;

use Illuminate\Database\Eloquent\Model;

class User_mess extends Model
{
    //指定表名
    protected $table = 'user_mess';
    //指定id
    protected $primaryKey = 'auth_id';
    //关闭时间戳
    public $timestamps = false;
    //黑名单
    protected $guarded = [];
}
