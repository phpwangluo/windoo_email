<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Handles\UploadHandles;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadImages(Request $request, UploadHandles $upload)
    {
        //调用上传图片类里的save方法，获取存储的图片路径
        $image = $upload->save($request->file()['file'], '', '', 500);
        //将图片路径赋值 $data 里
        return [
            'error'=>1,'message'=>'上传成功','url'=>$image['path']
        ];
    }
    public function uploadFiles(Request $request, UploadHandles $upload)
    {
        //调用上传图片类里的save方法，获取存储的图片路径
        $file = $upload->save($request->file()['file'], '', '', '','files');
        $source = public_path().'/storage/upload' . $file['path'];
        //dd($source);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, "HTML");
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            $writer = new \PhpOffice\PhpWord\Writer\HTML\Element\Container($htmlWriter, $section);
            $content .= $writer->write();
        }
        //将图片路径赋值 $data 里
        return [
            'error'=>1,'message'=>'上传成功','content'=>$content
        ];
    }
}
