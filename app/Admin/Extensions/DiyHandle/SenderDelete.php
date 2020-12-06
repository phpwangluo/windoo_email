<?php

namespace App\Admin\Extensions\DiyHandle;

use Encore\Admin\Admin;

class SenderDelete
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.sender-delete-{$this->id}').on('click', function () {
    $.ajax({
        method: 'post',
        url: '/api/senderdelete',
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

        return "<a class='grid-row-delete sender-delete-{$this->id}' data-id='{$this->id}'><i class='fa fa-trash'></i></a>";
    }

    public function __toString()
    {
        return $this->render();
    }

}
