<?php


namespace App\Admin\Extensions\Tools;

use Encore\Admin\Form\Field;

class Simditor extends Field
{
    protected $view = 'admin.tools.simditor';

    protected static $css = [
        '/packages/simditor-2.3.6/styles/simditor.css',
    ];

    protected static $js = [
        '/packages/simditor-2.3.6/scripts/module.js',
        '/packages/simditor-2.3.6/scripts/hotkeys.js',
        '/packages/simditor-2.3.6/scripts/uploader.js',
        '/packages/simditor-2.3.6/scripts/simditor.js',

    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $token = csrf_token();
        $url = route('uploadImg');

        $this->script = <<<EOT
$(document).ready(function(){

      var editor = new Simditor({
          textarea: $('#editor'),
          toolbar: ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', 'table', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment'],
          upload: {
                url: '$url',
                params: { _token: '$token'},
                fileKey: 'upload_file',
                connectionCount: 3,
                leaveConfirm: '文件上传中，关闭此页面将取消上传。'
            },
            pasteImage: true,
      });
 });
EOT;
        return parent::render();

    }
}
