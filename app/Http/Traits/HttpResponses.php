<?php

namespace App\Http\Traits;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HttpResponses {

    public function ok($message = 'ok')
    {
        return [
            'message' => $message
        ];
    }

    public function badRequest($message = null)
    {
        throw new BadRequestHttpException($message);
    }


    public function unauthorized($message = null)
    {
        throw new UnauthorizedHttpException('challenge', $message);
    }


    public function forbidden($message = null)
    {
        throw new AccessDeniedHttpException($message);
    }


    public function notFound($message = null)
    {
        throw new NotFoundHttpException($message);
    }


    public function unprocessableEntity($data)
    {
        throw new UnprocessableEntityHttpException($data);
    }


    public function serviceUnavailable($data)
    {
        throw new ServiceUnavailableHttpException(null, $data);
    }
}