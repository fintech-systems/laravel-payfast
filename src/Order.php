<?php

namespace FintechSystems\Payfast;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{    
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function generate() {
        
    }
}
