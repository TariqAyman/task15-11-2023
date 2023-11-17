<?php
/*
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 4/14/22, 12:03 AM
 * Last Modified: 4/14/22, 12:03 AM
 * Project Name: GenCode
 * File Name: MapResponseError.php
 */

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

class MapResponseError
{
    /**
     * Return error as array
     */
    public static string $errorAsArray = 'array';

    /**
     * The returned key in the error array type
     */
    public static string $errorKey = 'key';

    /**
     * The returned value in the error array type
     */
    public static string $errorValue = 'message';

    /**
     * Return error as object
     */
    public static string $errorStrategy = 'object';

    /**
     * Map error based on configurations
     *
     * @return mixed
     */
    public static function mapResponseError(mixed $data, int $statusCode = Response::HTTP_BAD_REQUEST): mixed
    {
        $errorMaxArrayLength = config('api-response.response.errors.maxArrayLength', 1);
        $errorStrategy = config('api-response.response.errors.strategy', self::$errorAsArray);
        $arrayKey = config('api-response.response.errors.key', self::$errorKey);
        $arrayValue = config('api-response.response.errors.value', self::$errorValue);

        if ($data instanceof MessageBag) {
            $errors = [];

            foreach ($data->messages() as $input => $messagesList) {
                if ($errorStrategy === self::$errorStrategy) {
                    $errors[$input] = $messagesList[0];
                } elseif ($errorStrategy === self::$errorAsArray) {
                    $errors[] = [
                        $arrayKey => $input,
                        $arrayValue => $errorMaxArrayLength === 1 ? $messagesList[0] : array_slice($messagesList, 0, $errorMaxArrayLength),
                    ];
                }
            }

            $data = [
                'success' => false,
                'status' => $statusCode,
                'errors' => $errors,
            ];
        } elseif (is_string($data)) {
            if ($errorStrategy === self::$errorStrategy) {
                $data = [
                    'success' => false,
                    'status' => $statusCode,
                    'error' => $data,
                ];
            } else { /*if ($errorStrategy === $this->errorAsArray)*/
                $data = [
                    'success' => false,
                    'status' => $statusCode,
                    'errors' => [
                        [
                            $arrayKey => 'error',
                            $arrayValue => $data,
                        ],
                    ],
                ];
            }
        } elseif (is_array($data)) {
            $data = [
                'success' => false,
                'status' => $statusCode,
                'errors' => $data,
            ];
        }

        return $data;
    }
}
