<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogArticles extends Model
{
    use HasFactory;
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
        return $this->hasOne(SitesBlogArticleCategories::class);
    }
}
