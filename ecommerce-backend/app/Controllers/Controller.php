<?php
namespace App\Controllers;

use App\Core\Response;

abstract class Controller {
    protected function success(Response $response, $data, $message = 'Success', $code = 200) {
        return $response->success($data, $message, $code);
    }

    protected function error(Response $response, $message, $code = 400, $errors = []) {
        return $response->error($message, $code, $errors);
    }
}
