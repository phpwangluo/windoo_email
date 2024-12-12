<?php

namespace App\Admin\Controllers\Sites;

use App\Models\SitesBlogArticleCategories;
use App\Models\SitesBlogArticles;
use App\Models\SitesBlogCategories;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class BlogArticlesCategriesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '边栏设置';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogArticles());
        $obj = $grid->model();
        if(request('site_id')) {
            $obj->where('site_id','=',request('site_id'));
        }
        $obj->groupBy(['id']);
        $obj->orderBy('site_id','desc');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('title', '文章标题');

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->column('title', __('文章标题'))->limit(30,'...');
        $grid->column('publish_time', __('发布时间'));
        $grid->column('site_id', __('站点ID'));
        $grid->column('choose_lable', '选择栏目')->display(function () {
            $now_selected = SitesBlogArticleCategories::query()
                ->where('article_id','=',$this->id)
                ->get();
            $in_selected = [];
            foreach ($now_selected as $sitem){
                $in_selected[] = $sitem->category_id;
            }
            $cate_aside  = SitesBlogCategories::query()->where('type','=',2)
                ->where('site_id','=',$this->site_id)->get();
            if($cate_aside){
                $html = '<div onchange="add_article_to_category(this)"  id="ischange"  article_id = '.$this->id.'  site_id='.$this->site_id.'>';
                foreach ($cate_aside as $item){
                    if(in_array($item->id,$in_selected)){
                        $html .= '<div class="checkbox icheck">
                <label>
                    <input  type="checkbox" checked name=\'category_id[]\' class="minimal ie-input" value="'.$item->id.'" data-label="'.$item->name.'"/>&nbsp;'.$item->name.'&nbsp;&nbsp;
                </label>
            </div>';
                    }else{
                        $html .= '
            <div class="checkbox icheck">
                <label>
                    <input  type="checkbox" name=\'category_id[]\' class="minimal ie-input" value="'.$item->id.'" data-label="'.$item->name.'"/>&nbsp;'.$item->name.'&nbsp;&nbsp;
                </label>
            </div>';
                    }
                }
            }
            $html .= '</div>';
            return $html;
        });
        $grid->disableActions();
        Admin::js('/static/js/diycommon.js');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SitesBlogArticleCategories::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('article_id', __('Article id'));
        $show->field('category_id', __('Category id'));
        $show->field('site_id', __('Site id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SitesBlogArticleCategories());

        $form->number('article_id', __('Article id'));
        $form->number('category_id', __('Category id'));
        $form->number('site_id', __('Site id'));

        return $form;
    }
}
