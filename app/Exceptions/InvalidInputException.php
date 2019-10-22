<?php


namespace MailerTiny\Exceptions;


use Exception;
use JsonSerializable;

class InvalidInputException extends Exception implements JsonSerializable
{

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param string $msg
     */
    public function addErrorMessage(string $msg): void
    {
        $this->errors[] = $msg;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errors;
    }

    public function addErrorMessages(array $errorMessages)
    {
        $this->errors = array_merge($this->errors, $errorMessages);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->errors;
    }
}