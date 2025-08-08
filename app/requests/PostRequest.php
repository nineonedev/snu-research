<?php

namespace app\requests;

use app\core\FormRequest;

class PostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'content' => ['required'],
            'image' => ['file'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // $this->merge([
        //     'title' => trim($this->input('title')),
        // ]);
    }
}
