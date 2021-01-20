<?php

namespace App\Admin\Extensions\DiyHandle\Blogs;

use Encore\Admin\Admin;

class CreateSitesSuccess
{
    protected $id;

    public function __construct()
    {

    }

    protected function script()
    {
        return <<<SCRIPT
$('#myActionModal').modal().show;

$("#myActionBtn").click(function (e){
    var business_status = $("input[name='business_status']:checked").val();
    var remarks = $("#remarks").val();
    e.preventDefault();
    $.ajax({
        method: 'post',
        url: '/api/businessstatus',
        data: {
            _token:LA.token,
            business_status: business_status,
            remarks: remarks

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
        $html='<div class="modal fade" id="myActionModal" data-controls-modal="myActionModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myActionModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button id="cancelSmallBtn" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myActionTitle">确认合作意向</h4>
      </div>
      <form id="assignForm" method="post" action="/api/businessstatus">
          <div class="modal-body">
              <div class="form-group">
                    <label for="business_status" class="control-label">合作意向：</label>
                    <label class="radio-inline">
                        <input type="radio" name="business_status" id="business_status1" value="0" required>不合作
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="business_status" id="ibusiness_status2" value="1" checked required>合作中
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="business_status" id="business_status3" value="2" required>已合作
                    </label>
              </div>
              <div class="form-group">
                    <label for="remarks" class="control-label">意向备注：</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="4" required></textarea>
              </div>
          </div>
          <div class="modal-footer">
            <button id="cancelBtn" type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <input id="myActionBtn" type="submit" class="btn btn-primary" value="确认"/>
          </div>
      </form>
    </div>
  </div>
</div>';
        return "<a title='合作意向' class='grid-row-edit change-status-{$this->id}'  data-id='{$this->id}'><i class='fa fa fa-street-view'></i></a>".$html;
    }
    public function __toString()
    {
        return $this->render();
    }
}
