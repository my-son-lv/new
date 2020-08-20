<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    //自定义异常处理
    public function SetErrorMessage($errorMsg='', $errorCode = '500'){
        $this->errorMsg = $errorMsg;
        $this->errorCode = $errorCode;
        return $this;  
    }
}
