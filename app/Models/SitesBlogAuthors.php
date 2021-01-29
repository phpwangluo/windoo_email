<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogAuthors extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'authors';
    public function sites()
    {
        return $this->belongsTo(SitesBlogSites::class,'site_id');
    }
}
