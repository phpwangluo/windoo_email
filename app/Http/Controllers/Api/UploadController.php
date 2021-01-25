<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Handles\UploadHandles;
use Illuminate\Http\Request;
use App\Admin\Extensions\Tools\Uploader;

class UploadController extends Controller
{
    public function uploadImages(Request $request, UploadHandles $upload)
    {
        //调用上传图片类里的save方法，获取存储的图片路径
        $image = $upload->save($request->file()['file'], $request->get('image_folder').'/', '', 500);
        //将图片路径赋值 $data 里
        return [
            'error'=>1,'message'=>'上传成功','url'=>$image['path']
        ];
    }
    public function uploadFiles(Request $request, UploadHandles $upload)
    {
        //调用上传图片类里的save方法，获取存储的图片路径
        $file = $upload->save($request->file()['file'], $request->get('image_folder').'/', '', '','files');
        $source = public_path() . $file['path'];
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, "HTML");
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            $writer = new \PhpOffice\PhpWord\Writer\HTML\Element\Container($htmlWriter, $section);
            $content .= $writer->write();
        }
        if($content != ''){
            $pattern = '/(<img.+?src="?)([^"]+)("[^>]+>)/i';
            preg_match_all($pattern,$content,$matchs);
            $image_lists  = $matchs[2];
        }
        //将图片路径赋值 $data 里
        return [
            'error'=>1,'message'=>'上传成功','content'=>$content,'image_lists'=>$image_lists
        ];
    }
    function editorUpload(){

        //上传配置
        $config = array(
            "savePath" => storage_path('app/public/upload') . '/images/' ,             //存储文件夹
            "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
        );
        //上传文件目录
        $Path = storage_path('app/public/upload') . '/images/';

        //背景保存在临时目录中
        $config[ "savePath" ] = $Path;
        $up = new Uploader( "upfile" , $config );
        $type = $_REQUEST['type'];
        $callback=$_GET['callback'];

        $info = $up->getFileInfo();
        /**
         * 返回数据
         */
        if($callback) {
            echo '<script>'.$callback.'('.json_encode($info).')</script>';
        } else {
            echo json_encode($info);
        }
    }
}
