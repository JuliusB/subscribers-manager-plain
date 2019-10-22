<?php

namespace MailerTiny\Core;

class Request
{
    /** @var array */
    private $input;

    /** @var string */
    private $method;

    /** @var bool */
    private $ajax = false;

    /**
     * Private construct for singleton design pattern
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];

        if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
                == 'xmlhttprequest')
            || (isset($_SERVER['CONTENT_TYPE'])
                && strpos($_SERVER['CONTENT_TYPE'], 'application/json')
                !== false
            )
        ) {
            $this->ajax = true;
        }
        $this->sanitizeInputs();
    }


    /**
     * Sanitize request inputs from XSS/
     */
    private function sanitizeInputs()
    {
        $inputs = [];
        if ($this->isAjax()) {
            $inputJSON = file_get_contents('php://input');
            $inputs = json_decode($inputJSON, true);
            if (!is_array($inputs)) {
                $inputs = [];
            }
        }
        $inputs = array_merge($_POST, $_GET, $inputs);

        $cleanedInput = [];
        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $cleanedInput[$key] = $value;
                continue;
            }
            $value = trim($value);
            $value = strip_tags($value);
            $value = preg_replace('!\s+!', ' ', $value);
            $cleanedInput[$key] = $value;
        }

        $this->input = $cleanedInput;
    }

    /**
     * @return array
     */
    public function inputs()
    {
        return $this->input;
    }


    /**
     * @param array $availableInput
     */
    public function filterOutInput(array $availableInput)
    {
        foreach (array_keys($this->input) as $input) {
            if (!in_array($input, $availableInput)) {
                unset($this->input[$input]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @return mixed
     */
    public function getHttpMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return (bool) $this->ajax;
    }
}