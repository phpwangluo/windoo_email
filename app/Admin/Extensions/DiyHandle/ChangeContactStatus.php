<?php

namespace App\Admin\Extensions\DiyHandle;

use Encore\Admin\Admin;

class ChangeContactStatus
{
    protected $id;
    protected $task_status;

    public function __construct($id,$task_status)
    {
        $this->id = $id;
        $this->task_status = $task_status;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.change-status-{$this->id}').on('click', function () {
    $.ajax({
        method: 'post',
        url: '/api/contactstatus',
        data: {
            _token:LA.token,
            id: {$this->id},
            task_status: {$this->task_status}
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
        if($this->task_status == 1){
            return "<a title='启用' class='grid-row-edit change-status-{$this->id}'  data-id='{$this->id}'><i class='fa fa-check'></i></a>";
        }else{
            return "<a title='停用' class='grid-row-edit change-status-{$this->id}'  data-id='{$this->id}'><i class='fa fa-close'></i></a>";
        }
    }

    public function __toString()
    {
        return $this->render();
    }

}
