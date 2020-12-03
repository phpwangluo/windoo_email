<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailForSend extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'sender_email',
        'receiver_email',
        'title',
        'content',
        'send_type',
        'send_start_hour',
        'send_end_hour',
        'send_status',
        'plan_send_time'
    ];
    public function template()
    {
        return $this->belongsTo(Template::class,'template_id');
    }
}
