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
    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            //修改状态值
            if($model->carousel == 'on'){
                $model->carousel = 1;
            }elseif($model->carousel == 'off'){
                $model->carousel = 0;
            }
        });
    }
}
