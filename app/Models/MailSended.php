<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSended extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'email_address',
        'email_pass',
        'email_sign',
        'send_count',
        'receive_count',
        'max_send_count',
        'email_status',
        'status',
        'remarks',
        'mail_setting_id',
    ];
}
