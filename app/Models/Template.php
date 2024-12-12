<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'status'
    ];
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function trade()
    {
        return $this->belongsTo(Trade::class,'trade_id');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
    public function mailforsend()
    {
        return $this->hasOne(MailForSend::class);
    }

}
