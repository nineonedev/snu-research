<?php

namespace app\core;

use app\contracts\RuleInterface;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected bool $executed = false;

    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function execute(): void
    {
        if ($this->executed) return;
        $this->executed = true;

        foreach ($this->rules as $attribute => $rules) {
            $value = $this->data[$attribute] ?? null;

            foreach ($rules as $rule) {
                if (is_string($rule)) {
                    $rule = $this->resolveBuiltInRule($rule);
                }

                if ($rule instanceof RuleInterface) {
                    if (!$rule->passes($attribute, $value)) {
                        $this->errors[$attribute][] = $rule->message($attribute);
                    }
                }
            }
        }
    }

    protected function resolveBuiltInRule(string $rule): RuleInterface
    {
        $map = [
            'required' => RequiredRule::class,
            'email'    => \app\rules\EmailRule::class,
            'file'     => \app\rules\FileRule::class,
            'max'      => \app\rules\MaxRule::class,
            'equalTo',
            ''
        ];

        [$ruleName, $param] = explode(':', $rule) + [null, null];

        $class = $map[$ruleName] ?? throw new \Exception("Undefined rule: {$ruleName}");

        return $param !== null
            ? new $class($param)
            : new $class();
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function responeErrors(): void
    {
        Session::set('_errors', $this->errors);
        Session::set('_old_input', $this->data);
        Response::back();
    }

    public function reset(): void
    {
        $this->executed = false;
        $this->errors = [];
    }
}
