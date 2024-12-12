<?php

namespace App\Http\Controllers\Api\Blogs;

use App\Http\Controllers\Controller;
use App\Models\SitesBlogArticleCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleCategoriesController extends Controller
{

    /**
     * 修改联系人状态
     * @param Request $request
     * @return array
     */
    public function addArticleCategories(Request $request)
    {
        $cate_ids = $request->post('category_ids');
        $site_id = $request->post('site_id');
        $article_id = $request->post('article_id');
        if(!$cate_ids || !$site_id || !$article_id){
            return [
                'error'=>0,'message'=>'参数不合法'
            ];
        }
        SitesBlogArticleCategories::query()
            ->where('article_id', '=',$article_id)
            ->where('site_id','=',$site_id)
            ->delete();
        $batch_insert = [];
        foreach ($cate_ids as $categoyr_id){
            $batch_insert[] = [
                'category_id'=>$categoyr_id,
                'site_id'=>$site_id,
                'article_id'=>$article_id
            ];
        }
        SitesBlogArticleCategories::query()->insert($batch_insert);
        return [
            'error'=>1,'message'=>'成功'
        ];
    }
}
