<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogCategories extends Model
{
    use HasFactory;
    public function sites()
    {
        return $this->belongsTo(SitesBlogSites::class,'site_id');
    }
}
