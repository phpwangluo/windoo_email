<?php


namespace App\Handles;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
class UploadHandles {
    //定义一个允许的后缀名属性
    public $allow_image_ext = ['jpg', 'jpeg', 'png', 'gif'];
    //定义一个允许的后缀名属性
    public $allow_file_ext = ['docx'];
    public function save($file, $folder, $file_prefix, $max_width,$type='images') {
        //进行后缀名的验证,如果没有那么就默认为png
        $extension = $file->extension();
        if(!$extension || $extension == ''){
            return false;
        }
        if ($type == 'images'){

            if(! in_array($extension, $this->allow_image_ext)) {
                return false;
            }

            //定义存储路径，文件夹切割能让查找效率更高
            $folder_name = $folder.date("Y", time());
            $upload_path = storage_path('app/public/upload') . '/images/' . $folder_name;
            //定义文件名
            $file_name = $file_prefix . "_" . time() . "_" . Str::random(10).".". $extension;

            //将图片移动到目标储存位置
            $file->move($upload_path, $file_name);

            //如果限制了图片宽度，就进行裁剪
            if ($max_width && $extension != 'gif') {
                // 此类封装的函数， 用于裁剪图片
                $this->reduceSize($upload_path . "/" . $file_name,$max_width);
            }

            //返回图片已经存储的路径
            return ['path' => '/storage/upload/images/' . "$folder_name/$file_name"];
        }elseif ($type == 'files'){

            if(! in_array($extension, $this->allow_file_ext)) {
                return false;
            }

            //定义存储路径，文件夹切割能让查找效率更高
            $folder_name = $folder.date("Ym/d", time());
            $upload_path = storage_path('app/public/upload') . '/files/' . $folder_name;
            //定义文件名
            $file_name = $file_prefix . "_" . time() . "_" . Str::random(10).".". $extension;

            //将图片移动到目标储存位置
            $file->move($upload_path, $file_name);

            //返回图片已经存储的路径
            return ['path' => '/storage/upload/files/' . "$folder_name/$file_name"];
        }

    }

    public function reduceSize($file_name, $max_width) {
        //先实例化，参数是图片物理路径
        $image = Image::make($file_name);

        //将图片的大小进行调整
        $image->resize($max_width, null, function($constraint) {

            //设定宽度 $max_width, 高度等比例双方缩放
            $constraint->aspectRatio();

            //防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        //对图片进行保存
        $image->save();
    }
}
