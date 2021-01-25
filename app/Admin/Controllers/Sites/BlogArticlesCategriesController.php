<?php

namespace App\Admin\Controllers\Sites;

use App\Models\SitesBlogArticleCategories;
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
        $grid = new Grid(new SitesBlogArticleCategories());

        $grid->column('articles.title', __('文章标题'));
        $grid->column('category_id', __('发布时间'));
        $grid->column('site_id', __('站点ID'));
        $grid->column('site_id', __('选择栏目'));
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
