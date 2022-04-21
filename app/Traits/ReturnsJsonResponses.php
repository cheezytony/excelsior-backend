<?php

namespace App\Traits;

use App\Services\PaginationService;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ReturnsJsonResponses
{

    /**
     * @param Exception $e
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function fatalErrorResponse(
        Exception $e,
        ?string $message = 'Oops! Something went wrong on the server',
        ?int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        $line = $e->getTrace();
        $error = [
            "message" => $e->getMessage(),
            "trace" => $line[0],
            "mini_trace" => $line[1]
        ];

        return response()->json([
            "success" => false,
            "message" => $message,
            "error" => !app()->environment(['production']) ? $error : null
        ], $statusCode);
    }

    /**
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function successResponse(
        mixed $data = null,
        string $message = null,
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        $responseData = [
            'success' => true,
            'message' => $message ?: __('response.success')
        ];
        if (null !== $data) {
            $responseData['data'] = $data;
        }
        return response()->json($responseData, $statusCode);
    }

    /**
     * @param string|null $message
     * @param ?mixed $data
     * @param array|null $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    public function errorResponse(
        ?string $message,
        mixed $data = null,
        ?array $errors = [],
        ?int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        $response = ['success' => false, 'message' => $message];
        is_array($errors) && count($errors) ? $response['errors'] = $errors : null;
        !is_null($data) ? $response['data'] = $data : null;
        return response()->json($response, $statusCode);
    }

    /**
     * @param array $errors
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function formErrors(
        array $errors,
        ?string $message = 'Invalid form data',
        ?int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        return $this->errorResponse($message, null, $errors, $code);
    }

    public function paginatedResponse(
        EloquentBuilder|QueryBuilder|Relation $query,
        string $resource,
        ?array $config = []
    ): Response {
        $data = (new PaginationService($query, $resource, $config))->process();

        if ($data instanceof JsonResponse) {
            return $data;
        }

        return $this->successResponse($data);
    }
}
