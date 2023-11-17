<?php

/*
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 4/14/22, 12:03 AM
 * Last Modified: 4/14/22, 12:03 AM
 * Project Name: GenCode
 * File Name: AbstractApiController.php
 */

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Helpers\MapResponseError;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends Controller
{
    /**
     * Return error as array
     */
    protected string $errorAsArray = 'array';

    /**
     * The returned key in the error array type
     */
    protected string $errorKey = 'key';

    /**
     * The returned value in the error array type
     */
    protected string $errorValue = 'message';

    /**
     * Return error as object
     */
    protected string $errorStrategy = 'object';

    /**
     * Send success data
     */
    protected function success(mixed $data = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data = [
            'success' => true,
            'status' => $statusCode,
            'records' => $data,
        ];

        return $this->send($statusCode, $data);
    }

    /**
     * Send Success data
     *
     * @param null $data
     */
    protected function successCreate($data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $data = [
            'success' => true,
            'status' => $statusCode,
            'records' => $data,
        ];

        return $this->send(Response::HTTP_CREATED, $data);
    }

    /**
     * Send bad request data
     *
     * @param mixed $data
     */
    protected function badRequest($data, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $data = $this->mapResponseError($data);

        return $this->send($statusCode, $data);
    }

    /**
     * Send not found request data
     */
    protected function notFound(array|string $data = '', int $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        if ($data === null) {
            $data = config('api-response.defaults.notFound', trans('response.notFound'));
        }

        $data = $this->mapResponseError($data);

        return $this->send($statusCode, $data);
    }

    /**
     * Unauthorized data
     */
    protected function unauthorized(string $message = null, int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $data = isset($message) ? [
            'success' => false,
            'status' => $statusCode,
            'errors' => [
                [
                    'key' => 'error',
                    'message' => $message,
                ],
            ],
        ] : config('api-response.defaults.unauthorized', trans('response.unauthorized'));

        $data = $this->mapResponseError($data);

        return $this->send($statusCode, $data);
    }

    /**
     * Send Response
     */
    protected function send(int $statusCode, mixed $data, array $headers = [], int $jsonOptions = JSON_PRESERVE_ZERO_FRACTION): JsonResponse
    {
        return response()->json(
            $data,
            $statusCode,
            $headers,
            $jsonOptions
        );
    }

    /**
     * Sends a Successful response with a given code
     */
    protected function successResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    /**
     * Sends an error code with a given code
     */
    protected function errorResponse($message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'status' => $statusCode,
                'errors' => ['key' => 'error', 'message' => $message],
            ],
            $statusCode
        );
    }

    /**
     * Sends an errors code with a given code
     */
    protected function errorsResponse($message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'status' => $statusCode,
                'errors' => ['key' => 'error', 'message' => $message],
            ],
            $statusCode
        );
    }

    /**
     * Sends a json with a collection of data with a 200 http code as default
     */
    protected function showAll(Collection $collection, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->successResponse(
            [
                'success' => true,
                'status' => $statusCode,
                'records' => $collection,
            ],
            $statusCode
        );
    }

    /**
     * sends a json response with only one result
     */
    protected function showOne(Model $instance, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->successResponse(
            [
                'success' => true,
                'status' => $statusCode,
                'record' => $instance,
            ],
            $statusCode
        );
    }

    /**
     * @return JsonResponse
     * @deprecated
     */
    protected function validator(Request $request, array $validation_data, array $message = null, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $validator = isset($message) ?
            Validator::make($request->all(), $validation_data, $message) :
            Validator::make($request->all(), $validation_data);

        if ($validator->fails()) {

            $data = $this->mapResponseError($validator->errors()) ?? [];

            response()->json($data, $statusCode)
                ->header('Content-Type', 'application/json')
                ->header('Accept', 'application/json')
                ->send();
            exit();
        }

        return $validator->validated();
    }

    /**
     * Map error based on configurations
     *
     * @param mixed $data
     * @param int $statusCode
     * @return mixed
     */
    protected function mapResponseError(mixed $data, int $statusCode = Response::HTTP_BAD_REQUEST): mixed
    {
        return MapResponseError::mapResponseError($data, $statusCode);
    }
}
