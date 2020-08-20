<?php

namespace App\ApiModel;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //指定表名
    protected $table = 'user';
    //指定id
    protected $primaryKey = 'u_id';
    //关闭时间戳
    public $timestamps = false;
    //黑名单
    protected $guarded = [];
}
