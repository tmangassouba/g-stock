<?php

namespace App\Observers;

use App\Models\Operation;

class OperationObserver
{
    public function created(Operation $operation)
    {
        $operation->reference = str_pad($operation->id, 4, '0', STR_PAD_LEFT);
        $operation->save();
    }
}
