<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
     /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Flight Reservation API",
     *      description="Flight Reservation API",
     *      @OA\Contact(
     *          email="snehagupta614@gmail.com"
     *      )
     * ),
     * @OA\PathItem(path="/api")
     */

    public function respondWithSuccess($data = null)
    {
        return response()->json(['data' =>  $data], Response::HTTP_OK);
    }

    public function respondWithValidationError($error = null)
    {
        return response()->json(['error' => $error], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondWithError($error_message = null, $data = null)
    {
        $error = ($error_message) ? $error_message : "Error";
        return response()->json(['error' =>  $error, 'data' => $data], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondWithBadRequest($error_message = null)
    {
        $error = ($error_message) ? $error_message : "Error";
        return response()->json(['error' =>  $error], Response::HTTP_BAD_REQUEST);
    }

    public function respondWithUnAuthorized($error_message = null)
    {
        $error = ($error_message) ? $error_message : "Invalid Credentials";
        return response()->json(['error' => $error], Response::HTTP_UNAUTHORIZED);
    }

    public function respondWithNotFound($error_message = null)
    {
        $error = ($error_message) ? $error_message : "Resource Not Found";
        return response()->json(['error' => $error], Response::HTTP_NOT_FOUND);
    }

    public function respondWithAlreadyExists($error_message = null)
    {
        $error = ($error_message) ? $error_message : "Resource Already Exists";
        return response()->json(['error' => $error], Response::HTTP_CONFLICT);
    }
}
