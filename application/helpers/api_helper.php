<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('api_success')) {
    function api_success($data = [], $message = 'Success')
    {
        return [
            'status'  => TRUE,
            'message' => $message,
            'data'    => $data,
        ];
    }
}

if ( ! function_exists('api_error')) {
    function api_error($message, $code = 400, $errors = [])
    {
        return [
            'status'  => FALSE,
            'message' => $message,
            'data'    => [],
            'errors'  => $errors,
            'code'    => (int) $code,
        ];
    }
}
