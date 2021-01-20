<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogSites extends Model
{
    use HasFactory;

    public function blog_author()
    {
        return $this->hasOne(SitesBlogAuthors::class,'site_id');
    }
    public function categories()
    {
        return $this->hasMany(SitesBlogCategories::class,'site_id');
    }
}
