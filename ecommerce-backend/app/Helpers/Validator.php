<?php
namespace App\Helpers;

class Validator {
    public static function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty($data[$field])) {
                $errors[] = "$field is required";
            }
            if ($rule === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format for $field";
            }
            if ($rule === 'numeric' && !empty($data[$field]) && !is_numeric($data[$field])) {
                $errors[] = "$field must be a number";
            }
            if ($rule === 'integer' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_INT)) {
                $errors[] = "$field must be an integer";
            }
            if (strpos($rule, 'min:') === 0 && !empty($data[$field])) {
                $min = (int) explode(':', $rule)[1];
                if (strlen($data[$field]) < $min) {
                    $errors[] = "$field must be at least $min characters";
                }
            }
        }
        return $errors;
    }
}