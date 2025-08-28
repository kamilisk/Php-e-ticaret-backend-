<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // Validation hataları
        if ($e instanceof ValidationException) {
            return ApiResponse::error('Validation hatası', $e->errors(), 422);
        }

        // Model bulunamadı (findOrFail, firstOrFail)
        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::error('Kayıt bulunamadı', null, 404);
        }

        // URL bulunamadı
        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::error('Sayfa bulunamadı', null, 404);
        }

        // Yetkisiz erişim
        if ($e instanceof AuthenticationException) {
            return ApiResponse::error('Yetkilendirme gerekli', null, 401);
        }

        // Diğer tüm hatalar
        return ApiResponse::error($e->getMessage(), null, 500);
    }
}

