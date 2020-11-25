<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSource extends Model
{
    use HasFactory;

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function trade()
    {
        return $this->belongsTo(Trade::class,'trade_id');
    }
}
