<?php

namespace FintechSystems\Payfast;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function generate()
    {
        $newRecord = self::create([
            'billable_id' => Auth::user()->getKey(),
            'billable_type' => Auth::user()->getMorphClass(),
        ]);

        return $newRecord->id . '-' . Carbon::now()->format('YmdHis');
    }
    
}
