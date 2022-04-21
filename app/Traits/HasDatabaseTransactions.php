<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\JsonResponse;

trait HasDatabaseTransactions
{
    /**
     * Wraps a callback in a try and catch and a database transaction
     * that would be rolled back on error.
     *
     * @param  Callable      $callback
     * @param  Callable|null $exceptionHandler
     * @return JsonResponse
     */
    public function dbTransaction(callable $callback, callable $exceptionHandler = null): JsonResponse
    {
        try {
            DB::beginTransaction();

            $response = $callback();

            DB::commit();

            return $response;
        } catch (Exception $e) {
            DB::rollBack();

            if (is_callable($exceptionHandler)) {
                $exceptionHandler($e);
            }

            throw $e;
        }
    }
}
