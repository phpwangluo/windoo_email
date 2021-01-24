<?php

namespace App\Admin\Controllers\Sites;

use App\Helpers\Tools;
use App\Models\SitesBlogArticles;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class BlogArticlesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '文章管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SitesBlogArticles());

        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'));
        $grid->column('title', __('Title'));
        $grid->column('content', __('Content'));
        $grid->column('author_id', __('Author id'));
        $grid->column('publish_time', __('Publish time'));
        $grid->column('abstract', __('Abstract'));
        $grid->column('view_count', __('View count'));
        $grid->column('share_count', __('Share count'));
        $grid->column('photo', __('Photo'));
        $grid->column('category_id', __('Category id'));
        $grid->column('carousel', __('Carousel'));
        $grid->column('permit_comment', __('Permit comment'));
        $grid->column('uri', __('Uri'));
        $grid->column('page_title', __('Page title'));
        $grid->column('page_description', __('Page description'));
        $grid->column('page_keywords', __('Page keywords'));
        $grid->column('site_id', __('Site id'));
        $grid->column('check_status', __('Check status'));
        $grid->column('check_time', __('Check time'));
        $grid->column('checker_id', __('Checker id'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(SitesBlogArticles::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('title', __('Title'));
        $show->field('content', __('Content'));
        $show->field('author_id', __('Author id'));
        $show->field('publish_time', __('Publish time'));
        $show->field('abstract', __('Abstract'));
        $show->field('view_count', __('View count'));
        $show->field('share_count', __('Share count'));
        $show->field('photo', __('Photo'));
        $show->field('category_id', __('Category id'));
        $show->field('carousel', __('Carousel'));
        $show->field('permit_comment', __('Permit comment'));
        $show->field('uri', __('Uri'));
        $show->field('page_title', __('Page title'));
        $show->field('page_description', __('Page description'));
        $show->field('page_keywords', __('Page keywords'));
        $show->field('site_id', __('Site id'));
        $show->field('check_status', __('Check status'));
        $show->field('check_time', __('Check time'));
        $show->field('checker_id', __('Checker id'));
        $show->field('status', __('Status'));
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
        $form = new Form(new SitesBlogArticles());
        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            //$tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();

        });
        $form->setTitle('文章录入');
        $form->number('site_id', __('站点ID'))->required()->default(function (){
            $site_id = request('site_id');
            return $site_id;
        })->readonly();
        $form->hidden('image_folder')->default(function (){
            $site_id = request('site_id');
            return $site_id;
        });
        $form->select('category_id', __('选择分类'))->options('/api/blogs/categorylist')->required();
        $form->text('title', __('文章标题'))->required();
        $form->date('publish_time', __('发布时间'))->format('YYYY-MM-DD')->required();
        //$form->text('abstract', __('Abstract'));
        //$form->number('view_count', __('View count'));
        //$form->number('share_count', __('Share count'));
        Admin::js('/static/js/upload.js');
        $form->display('word_file', '上传文章Word')->with(function ($value) {
            $html = '<input class="bob-upload-input" type="file"  name="file" onchange="uploadFiles(this)">';
                return $html;
        });
        //$form->ueditor('content', __('文章内容'));
        $form->UEditor('editor_content', __('文章内容'))->required();
        $form->hidden('content');
        //$form->multipleImage('photo', __('文章头图'))->required();
        Admin::css('/static/css/upload.css');
        Admin::js('/static/js/upload.js');
        $form->display('image-list', '文章头图')->with(function ($value) {
            $html = '<div class="bob-upload" tabindex="0"><div id="add_article_image">上传文章内容然后在文章内容图片中选择头图</div>';
            return $html;
        });
        $form->hidden('photo');
        $form->switch('carousel', __('加入轮播图'))->default(0)->required();
        $form->text('uri', __('文章URI'))->required();
        $form->text('abstract', __('文章摘要'))->required();
        $form->saving(function ($model) {
            if (request('site_id')){
                $insertArticle = [];
                $insertArticle['title'] =  $model->title;
                $insertArticle['publish_time'] =  date('Y-m-d H:i:s',time());
                $insertArticle['author_id'] =  1;
                $insertArticle['category_id'] = $model->category_id;
                $insertArticle['checker_id'] =  1;
                $insertArticle['photo'] =  $model->photo;
                $insertArticle['site_id'] =  $model->site_id;
                $insertArticle['check_status'] =  1;
                $insertArticle['check_time'] =  date('Y-m-d H:i:s',time());
                $insertArticle['carousel'] =  $model->carousel;
                $insertArticle['uri'] = Tools::usubstr($model->uri, 0, 100);
                $insertArticle['abstract'] = Tools::usubstr($model->content, 0, 100);
                if (SitesBlogArticles::insertGetId($insertArticle)){
                    $success = new MessageBag([
                        'title'   => '提示',
                        'message' => '创建成功',
                    ]);
                    //return redirect(url("admin/sites-blog-articles"))->with(compact('success'));
                }
            }
            return;

        });
        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            //$footer->disableReset();

            // 去掉`提交`按钮
            //$footer->disableSubmit();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();

            // 去掉`继续编辑`checkbox
            //$footer->disableEditingCheck();

            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();

        });
        Admin::js('/static/js/article.js');
        return $form;
    }
}
