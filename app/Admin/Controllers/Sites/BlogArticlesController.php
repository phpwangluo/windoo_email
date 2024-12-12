<?php

namespace App\Admin\Controllers\Sites;

use App\Admin\Actions\Diy\ArticleContentDetailAction;
use App\Admin\Extensions\DiyHandle\ArticleContent;
use App\Helpers\Tools;
use App\Models\SitesBlogArticleCategories;
use App\Models\SitesBlogArticles;
use App\Models\SitesBlogAuthors;
use App\Models\SitesBlogCategories;
use App\Models\SitesBlogPhotos;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
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
        $obj = $grid->model();
        if(request('site_id')) {
            $obj->where('site_id','=',request('site_id'));
        }
        $obj->orderBy('site_id','desc');
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->equal('site_id', '站点ID');
            $filter->equal('sites.name', '站点名称');
            $filter->equal('title', '文章标题');

        });
        $grid->disableExport();//禁用导出
        $grid->disableCreateButton(); //禁用创建
        $grid->fixColumns(1,-1);
        $grid->column('site_id', __('站点ID'));
        $grid->column('content_more', '文章内容')->modal('内容详情', function ($model) {
            if($model->content != ''){
                $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
                preg_match_all($pattern,$model->content,$matchs);
                $model->content = preg_replace( $pattern,'$1'.'/storage/upload/'.$model->site_id.'/$2$3',$model->content);
            }
            return '<div style="width: 95%;white-space: normal;margin: 10px;";>'.$model->content.'</div>';
        })->width('200');
        $grid->column('sites.name', __('站点名称'))->limit(30,'...');
        $grid->column('title', __('文章标题'))->limit(30,'...');
        $grid->column('uri', __('文章地址'))->limit(30,'...');
        $grid->column('photo', __('头图'))->display(function (){
            return $this->site_id.'/'.$this->photo;
        })->image();
        $grid->column('carousel', __('加入轮播图'))->switch();

        $grid->column('insert_index_list', '加入首页')->display(function (){
            $cate = SitesBlogCategories::query()
                ->select('categories.id as cate_id')
                ->join('page_settings','page_settings.id','=','categories.page_id')
                ->where([
                    'page_settings.page_type'=>1,
                    'page_settings.site_id'=>$this->site_id
                ])->first();

            $category_id = $cate->cate_id;
            $is_exist_in_home = SitesBlogArticleCategories::query()
                ->where([
                    'article_id'=>$this->id,
                    'site_id'=>$this->site_id,
                    'category_id'=>$category_id
                ])->first();
            if($is_exist_in_home){
                return 1;
            }else{
                return 0;
            }
        })->switch();
        //$grid->column('carousel', __('首页展现'))->switch();
        $grid->column('category.name', __('分类'));
        $grid->column('publish_time', __('发布时间'));

        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            //$actions->disableEdit();

            // 去掉查看
            $actions->disableView();
            // 添加自定义查看的按钮
        });

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
        $form->text('site_id', __('站点ID'))->required()->default(function (){
            $site_id = request('site_id');
            return $site_id;
        })->readonly();
        if(!$form->isEditing()){
            $form->hidden('image_folder')->default(function (){
                $site_id = request('site_id');
                return $site_id;
            });
            $form->select('category_id', __('选择分类'))->options('/api/blogs/categorylist', [
                'site_id' => request('site_id')
                ])->required();
            $form->switch('carousel', __('加入轮播图'))->default(0)->required();
        }
        $form->text('title', __('文章标题'))->required();
        $form->date('publish_time', __('发布时间'))->format('YYYY-MM-DD')->required();
        if(!$form->isEditing()){
            Admin::js('/static/js/upload.js');
            $form->display('word_file', '上传文章Word')->with(function ($value) {
                $html = '<input class="bob-upload-input" type="file"  name="file" onchange="uploadFiles(this)">';
                return $html;
            });
        }
        $form->UEditor('content', __('文章内容'))->with(function ($model){
            if($this->content != ''){
                $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
                preg_match_all($pattern,$this->content,$matchs);
                $this->content = preg_replace( $pattern,'$1'.'/storage/upload/'.$this->site_id.'/$2$3',$this->content);
            }
            return $this->content;
        })->required();
        Admin::css('/static/css/upload.css');
        Admin::js('/static/js/upload.js');
        $form->display('image-list', '文章头图')->with(function ($model) {
            $img = '上传文章内容然后在文章内容图片中选择头图';
            if($this->content != ''){
                $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
                preg_match_all($pattern,$this->content,$matchs);
                $image_lists  = $matchs[2];
                $img = '';
                foreach ($image_lists as $images){
                    $image_arr = explode('/',$images);
                    $image_name = end($image_arr);
                    if($image_name == $this->photo){
                        $img .= '<img class="avatar" onclick="choosePhoto(this)" data="'.$image_name.'" src="'.$images.'" style="border: 2px solid red;">';
                    }else{
                        $img .= '<img class="avatar" onclick="choosePhoto(this)" data="'.$image_name.'" src="'.$images.'">';
                    }
                }
            }
            $html = '<div class="bob-upload" tabindex="0"><div id="add_article_image">'.$img.'</div>';
            return $html;
        });
        $form->hidden('photo')->default($form->model()->photo);
        $form->hidden('carousel')->default($form->model()->carousel);
        $form->hidden('id')->default($form->model()->id);
        $form->hidden('uri')->default($form->model()->uri);
        $form->hidden('abstract')->default($form->model()->abstract);

        if(!$form->isEditing()){
            $form->saving(function ($model) {
                if ($model->site_id){
                    //获取作者相关信息
                    $author = SitesBlogAuthors::query()->where('site_id','=',request('site_id'))->first();
                    $insertArticle = [];
                    $insertArticle['title'] =  trim($model->title);
                    $insertArticle['publish_time'] =  $model->publish_time;
                    $insertArticle['author_id'] =  $author->id;
                    $insertArticle['category_id'] = $model->category_id;
                    $insertArticle['checker_id'] =  $author->id;;
                    $insertArticle['photo'] =  $model->photo;
                    if($model->content != ''){
                        $model->content = str_replace('/storage/upload/'.$model->site_id,'',$model->content);
                    }
                    $insertArticle['content'] =  $model->content;
                    $insertArticle['site_id'] =  $model->site_id;
                    $insertArticle['check_status'] =  1;
                    $insertArticle['check_time'] =  $model->publish_time;
                    $insertArticle['carousel'] =  $model->carousel == 'on' ? 1 : 0;
                    $data=[
                        'title'=>$model->title,
                    ];
                    $uri = Tools::format_uri($data);
                    $insertArticle['uri'] = $uri;
                    $insertArticle['abstract'] = Tools::blog_summary($model->content, 160);
                    $article_id = SitesBlogArticles::insertGetId($insertArticle);

                    SitesBlogPhotos::query()->where('article_id', '=',$article_id)->delete();
                    $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
                    preg_match_all($pattern,$model->content,$matchs);
                    $article_image_lists  = $matchs[2];
                    $insert_article_phots = [];
                    foreach ($article_image_lists as $imgk =>  $images){
                        $insert_article_phots[$imgk] = [
                            'name'=>array_last(explode('/',$images)),
                            'path'=>$images,
                            'article_id'=>$article_id,
                        ];
                    }
                    SitesBlogPhotos::query()->insert($insert_article_phots);
                    //保存文章所在图片
                    $success = new MessageBag([
                        'title'   => '提示',
                        'message' => '创建成功',
                    ]);
                    return redirect(url("admin/sites-blog-articles/create/?site_id=".$model->site_id))->with(compact('success'));
                }
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '文章录入失败',
                ]);

                return back()->with(compact('error'));

            });
        }else{
            $form->saving(function (Form $form) {
                if($form->insert_index_list != null){
                    $article_id = $form->key;
                    $article = SitesBlogArticles::query()->where([
                        'id'=>$article_id
                    ])->first();
                    $site_id = $article->site_id;
                    $cate = SitesBlogCategories::query()
                        ->select('categories.id as cate_id')
                        ->join('page_settings','page_settings.id','=','categories.page_id')
                        ->where([
                            'page_settings.page_type'=>1,
                            'page_settings.site_id'=>$site_id
                        ])->first();
                    $category_id = $cate->cate_id;
                    if($form->insert_index_list == 0){
                        SitesBlogArticleCategories::query()
                            ->where('article_id', '=',$article_id)
                            ->where('site_id','=',$site_id)
                            ->where('category_id','=',$category_id)
                            ->delete();
                    }else{
                        $_insert= [
                            'category_id'=>$category_id,
                            'site_id'=>$site_id,
                            'article_id'=>$article_id
                        ];
                        SitesBlogArticleCategories::query()->create($_insert);
                    }
                    return response()->json([
                        'status'  => true,
                        'message' => '成功',
                    ]);
                }
                if ($form->id != null){
                    $data=[
                        'title'=>$form->title,
                    ];
                    $uri = Tools::format_uri($data);
                    $form->uri = $uri;
                    if($form->content != ''){
                        $form->content = str_replace('/storage/upload/'.$form->site_id,'',$form->content);
                    }
                    $form->abstract = Tools::blog_summary($form->content, 160);
                    SitesBlogPhotos::query()->where('article_id', '=',$form->id)->delete();
                    $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
                    preg_match_all($pattern,$form->content,$matchs);
                    $article_image_lists  = $matchs[2];
                    $insert_article_phots = [];
                    foreach ($article_image_lists as $imgk =>  $images){
                        $insert_article_phots[$imgk] = [
                            'name'=>array_last(explode('/',$images)),
                            'path'=>$images,
                            'article_id'=>$form->id,
                        ];
                    }
                    SitesBlogPhotos::query()->insert($insert_article_phots);
                }

            });
        }
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
