<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ApiResponseService
{
    protected array|Collection $data=[];
    protected int $code = 200;
    protected string $message = '';

    public function success($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status_code' => $statusCode,
        ], $statusCode);
    }

    public function error($data = null, $message = 'Error', $statusCode = 400)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status_code' => $statusCode,
        ], $statusCode);
    }

    public function setResponse(array|Collection $data)
    {
        $this->data = $data;
        return $this;
    }

    public function setCode(int $code)
    {
        $this->code = $code;
        return $this;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }
}
