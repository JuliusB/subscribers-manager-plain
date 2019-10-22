<?php

namespace MailerTiny\Core;

use MailerTiny\Exceptions\InvalidInputException;

class Validator
{
    /**
     * @param mixed $input
     * @param array  $rules
     *
     * @throws InvalidInputException
     */
    public function validate($input, array $rules)
    {
        foreach ($rules as $rule) {
            $this->validateByRule($input, $rule);
        }
    }

    /**
     * @param array $fields
     * @param array $rulesByFields
     *
     * @throws InvalidInputException
     */
    public function validateRequiredFields(array $fields, array $rulesByFields)
    {
        $errorMessages = [];
        foreach ($rulesByFields as $field => $rules) {
            if (in_array('required', $rules)
                && !in_array($field, array_keys($fields))
            ) {
                $errorMessages[] = $field . ' is required';
            }
        }
        if (!empty($errorMessages)) {
            $exception = new InvalidInputException();
            $exception->addErrorMessages($errorMessages);
            throw $exception;
        }
    }

    /**
     * @param mixed $input
     * @param string $rule
     *
     * @throws InvalidInputException
     */
    private function validateByRule($input, string $rule)
    {
        if (preg_match('/^max:([0-9]+)/', $rule, $matches)) {
            $this->length($input, $matches[1]);

            return;
        }

        if (preg_match('/^in:(.+)/', $rule, $matches)) {
            $this->in($input, $matches[1]);

            return;
        }

        switch ($rule) {
            case 'email':
                $this->email($input);
                break;
            case 'alpha':
                $this->alpha($input);
                break;
            case 'array':
                $this->array($input);
                break;
        }
    }

    /**
     * @param $email
     *
     * @throws InvalidInputException
     */
    private function email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidInputException('Incorrect email format');
        }

        $domain = strrchr($email, "@");
        if (!$domain) {
            throw new InvalidInputException('Incorrect email format');
        }
        $host = substr($domain, 1);
        $active = checkdnsrr($host);
        if (!$active) {
            throw new InvalidInputException('Email host domain not active');
        }
    }

    /**
     * @param string $input
     *
     * @throws InvalidInputException
     */
    private function alpha(string $input)
    {
        if (!preg_match("/^[A-Za-z]+$/", $input)) {
            throw new InvalidInputException('Only letters allowed');
        }
    }

    /**
     * @param string $input
     * @param        $int
     *
     * @throws InvalidInputException
     */
    private function length(string $input, $int)
    {
        if (strlen($input) > $int) {
            throw new InvalidInputException('String too long');
        }
    }

    /**
     * @param string $input
     * @param string $implodedValues
     *
     * @throws InvalidInputException
     */
    private function in(string $input, string $implodedValues)
    {
        if (!in_array($input, explode(',', $implodedValues))) {
            throw new InvalidInputException('Value not available');
        }
    }

    /**
     * @param $input
     *
     * @throws InvalidInputException
     */
    private function array($input)
    {
        if (!is_array($input)) {
            throw new InvalidInputException('Value must be type array');
        }
    }
}