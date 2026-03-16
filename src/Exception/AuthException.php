<?php

namespace App\Exception;

class AuthException extends \Exception
{
}

class InvalidEmailException extends AuthException
{
    public function __construct(string $message = 'Email не найден')
    {
        parent::__construct($message);
    }
}

class InvalidPasswordException extends AuthException
{
    public function __construct(string $message = 'Неверный пароль')
    {
        parent::__construct($message);
    }
}
