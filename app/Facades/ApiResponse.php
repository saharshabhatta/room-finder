<?php

namespace App\Facades;

use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Facade;

class ApiResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ApiResponseService::class;
    }
}

