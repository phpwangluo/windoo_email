<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email_address',
        'country_id',
        'trade_id',
        'template_id',
        'customer_tag',
        'send_start_hour',
        'send_end_hour',
        'send_count',
        'task_status',
        'status',
        'remarks'
    ];
    use HasFactory;
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function trade()
    {
        return $this->belongsTo(Trade::class,'trade_id');
    }
    public function template()
    {
        return $this->belongsTo(Template::class,'template_id');
    }
}
