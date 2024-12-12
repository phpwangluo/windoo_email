<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogArticleCategories extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'article_categories';
    public function categories()
    {
        return $this->belongsTo(SitesBlogCategories::class,'category_id');
    }

    public function articles()
    {
        return $this->belongsTo(SitesBlogArticles::class,'article_id');
    }
}
