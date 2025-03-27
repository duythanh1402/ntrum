<?php
/**
 * Base Validator class
 * 
 * @package VietQR
 */

namespace VietQR\Base;

use VietQR\Respect\Validation\Validator as RespectValidator;
use VietQR\Respect\Validation\Exceptions\NestedValidationException;

class Validator {
    protected $rules = [];
    protected $errors = [];

    /**
     * Add a validation rule.
     *
     * @param string $field The field name.
     * @param RespectValidator $rule The validation rule.
     * @return void
     */
    public function add_rule($field, RespectValidator $rule) {
        $this->rules[$field] = $rule;
    }

    /**
     * Validate the given data against the defined rules.
     *
     * @param array $data The data to validate.
     * @return bool True if validation passes, false otherwise.
     */
    public function validate(array $data) {
        $this->errors = [];

        foreach ($this->rules as $field => $rule) {
            try {
                $rule->assert(isset($data[$field]) ? $data[$field] : null);
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors from Validator.
     *
     * @return array The validation errors.
     */
    public function get_errors() {
        $messages = [];
        
        foreach ($this->errors as $field => $rules) {
            foreach ($rules as $rule => $message) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Get an array of validation errors for $this->errors.
     * 
     * @return array The validation error messages.
     * 
     * Example response:
     * ```
     * Array
     * (
     *     [field_name] => Array
     *     (
     *         [setName] => Array
     *         (
     *             [url] => "" must be a URL  // Example error message
     *             [notEmpty] => The value must not be empty // Example error message
     *         )
     *     )
     * )
     * ```
     */
    public function get_full_errors() {
        return $this->errors;
    }
}