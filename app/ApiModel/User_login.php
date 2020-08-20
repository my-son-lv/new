<?php

namespace App\ApiModel;

use Illuminate\Database\Eloquent\Model;

class User_login extends Model
{
        //指定表名
        protected $table = 'user_login';
        //指定id
        protected $primaryKey = 'login_id';
        //关闭时间戳
        public $timestamps = false;
        //黑名单
        protected $guarded = [];
}
