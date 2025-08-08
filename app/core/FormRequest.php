<?php

namespace app\core;

use app\core\Request;
use app\core\Validator;

abstract class FormRequest extends Request
{
    protected Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator($this->all(), $this->rules());
        $this->prepareForValidation();
    }

    abstract public function rules(): array;

    protected function prepareForValidation(): void {}

    public function validateResolved(): void
    {
        if (!$this->verifyCsrf()) {
            Response::alert('CSRF 토큰 오류가 발생했습니다.');
        }

        $this->validator->execute();

        if ($this->validator->fails()) {
            Session::set('_errors', $this->validator->getErrors());
            Session::set('_old_input', $this->all());
            Response::back();
        }
    }

    public function validated(): array
    {
        return $this->validator->fails() ? [] : $this->all();
    }

    public function errors(): array
    {
        return $this->validator->getErrors();
    }
}
