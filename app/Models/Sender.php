<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email_address',
        'email_pass',
        'email_sign',
        'remarks',
        'mail_setting_id',
        'status'
    ];
    public function mailsetting()
    {
        return $this->belongsTo(MailSetting::class,'mail_setting_id');
    }
}
