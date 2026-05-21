<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Validator;
use App\Services\ContactService;

class ContactController extends Controller {
    private ContactService $contactService;

    public function __construct() {
        $this->contactService = new ContactService();
    }

    public function store(Request $request, Response $response) {
        $data = $request->getBody();

        $errors = Validator::validate($data, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 400, $errors);
        }

        try {
            $messageId = $this->contactService->submitContactMessage($data);
            return $this->success($response, ['id' => $messageId], 'Contact message submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to submit contact message', 500, ['error' => $e->getMessage()]);
        }
    }
}