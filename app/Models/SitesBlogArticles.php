<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogArticles extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'articles';
    public function sites()
    {
        return $this->belongsTo(SitesBlogSites::class,'site_id');
    }
    public function category()
    {
        return $this->belongsTo(SitesBlogCategories::class,'category_id');
    }


    public function articlecategories()
    {
        return $this->hasMany(SitesBlogArticleCategories::class,'article_id');
    }
}
