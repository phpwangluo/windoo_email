<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogPageSettings extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'page_settings';
    public function sites()
    {
        return $this->belongsTo(SitesBlogSites::class,'site_id');
    }
    public function categories()
    {
        return $this->hasOne(SitesBlogCategories::class,'page_id');
    }
}
