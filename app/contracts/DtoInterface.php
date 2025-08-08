<?php

namespace app\contracts;

interface DtoInterface
{
    public function handle(array &$data): array;
}
