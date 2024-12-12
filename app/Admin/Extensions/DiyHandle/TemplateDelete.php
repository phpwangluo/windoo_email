<?php

namespace App\Admin\Extensions\DiyHandle;

use Encore\Admin\Admin;

class TemplateDelete
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.template-delete-{$this->id}').on('click', function () {
    $.ajax({
        method: 'post',
        url: '/api/templatedelete',
        data: {
            _token:LA.token,
            id: {$this->id},
        },
        success: function (result) {
            $.pjax.reload('#pjax-container');
            if(result.error == 1){
                toastr.success(result.message).refresh();
            }else{
                toastr.warning(result.message).refresh();
            }
        }
    });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a title='删除' class='grid-row-delete template-delete-{$this->id}' data-id='{$this->id}'><i class='fa fa-trash'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }

}
