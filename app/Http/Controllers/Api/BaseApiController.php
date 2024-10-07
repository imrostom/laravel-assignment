<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    /**
     * Return a success JSON response.
     */
    protected function success($data, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message ?? 'Operation successful.',
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response, including validation errors.
     */
    protected function error($errors, string $message = null, int $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message ?? 'An error occurred.',
            'errors' => $errors
        ], $code);
    }


    /**
     * Return a validation error JSON response.
     */
    protected function validationErrorResponse($validator)
    {
        // Get the first error message for each field
        $errors = collect($validator->errors()->messages())->map(function ($messages) {
            return $messages[0];  // Only take the first error message for each field
        })->toArray();

        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);
    }

}
