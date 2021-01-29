<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogSites extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'sites';

    public function blog_author()
    {
        return $this->hasOne(SitesBlogAuthors::class,'site_id');
    }
    public function categories()
    {
        return $this->hasMany(SitesBlogCategories::class,'site_id');
    }
    public function articles()
    {
        return $this->hasMany(SitesBlogArticles::class,'site_id');
    }
    public function pagesettings()
    {
        return $this->hasMany(SitesBlogPageSettings::class,'site_id');
    }
}
