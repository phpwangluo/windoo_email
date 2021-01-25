<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogArticleCategories extends Model
{
    use HasFactory;
    public function categories()
    {
        return $this->belongsTo(SitesBlogCategories::class);
    }

    public function articles()
    {
        return $this->belongsTo(SitesBlogArticles::class);
    }
}
