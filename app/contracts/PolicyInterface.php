<?php 

namespace app\contracts;

interface PolicyInterface {
    public function can(string $aciion, $user, $model = null): bool;
}