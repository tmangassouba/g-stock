<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    public function creating(Customer $customer)
    {
        $customer->name = sprintf("%s %s", $customer->fisrt_name, $customer->last_name);
    }

    public function created(Customer $customer)
    {
        $customer->code = str_pad($customer->id, 4, '0', STR_PAD_LEFT);
        $customer->save();
    }
}
