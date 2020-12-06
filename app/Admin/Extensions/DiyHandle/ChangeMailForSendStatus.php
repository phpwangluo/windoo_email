<?php

namespace App\Admin\Extensions\DiyHandle;

use Encore\Admin\Admin;

class ChangeMailForSendStatus
{
    protected $id;
    protected $send_status;

    public function __construct($id,$send_status)
    {
        $this->id = $id;
        $this->send_status = $send_status;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.change-status-{$this->id}').on('click', function () {
    $.ajax({
        method: 'post',
        url: '/api/mailforsendstatus',
        data: {
            _token:LA.token,
            id: {$this->id},
            send_status: {$this->send_status}
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
        if($this->send_status == 1){
            return "<a title='恢复发送' class='grid-row-edit change-status-{$this->id}'  data-id='{$this->id}'><i class='fa fa-check'></i></a>";
        }
        if($this->send_status == 3){
            return "<a title='取消发送' class='grid-row-edit change-status-{$this->id}'  data-id='{$this->id}'><i class='fa fa-close'></i></a>";
        }
    }

    public function __toString()
    {
        return $this->render();
    }

}
