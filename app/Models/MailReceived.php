<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailReceived extends Model
{
    use HasFactory;
    public function contact()
    {
        return $this->belongsTo(Contact::class,'sender_email','email_address');
    }
}
