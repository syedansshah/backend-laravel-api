<?php


/**
 * @description: Helper File
 */

function sendResponse($data = null, $message = null, $code)
{
    $response = [
        'success' => true,
        'message' => $data,
        'data'    => $message,
        'code'    => $code,
    ];
    return response()->json($response, 200);
}

function sendError($error, $errorMessages = [], $code = 404)
{
    $response = [
        'success' => false,
        'message' => $error,
    ];
    if (!empty($errorMessages)) {
        $response['data'] = $errorMessages;
    }
    $response['code'] = $code;

    return response()->json($response, $code);
}

function sendAuthResponse($message = null, $user = null, $token = null, $code)

{
    $response = [
        'success' => true,
        'message' => $message,
        'user' => $user,
        'authorisation' => [
            'token' => $token,
            'type' => 'bearer',
        ],
        'code'    => $code,
    ];
    return response()->json($response, 200);
}
