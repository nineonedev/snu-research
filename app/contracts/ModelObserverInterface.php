<?php

namespace app\contracts;

use app\core\Model;

interface ModelObserverInterface {
    public function beforeInsert(Model $model): void;
    public function afterInsert(Model $model): void;
    public function beforeUpdate(Model $model): void;
    public function afterUpdate(Model $model): void;
    public function beforeDelete(Model $model): void;
    public function afterDelete(Model $model): void;
}