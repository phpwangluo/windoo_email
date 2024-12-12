<?php

namespace App\Admin\Actions\Diy;

use App\Imports\Sender\ImportContacts;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

use Encore\Admin\Admin;


class ImportContactsAction extends Action
{
    protected $selector = '.import-action';

    public function handle(Request $request)
    {
        try{
            $import = new ImportContacts();

            $import->import($request->file('file'));

            $str = "";
            foreach ($import->failures() as $failure) {
                $str .=  ' 第'. $failure->row() . '行 失败原因：' . implode(' ', $failure->errors()) . '<br> 行数据：' . implode(' ', $failure->values()). '<br>';
            }
            if ($str !== '') {
                return $this->response()->error($str)->topFullWidth()->timeout(7000000);
            }
            //查看导入的数据并同步生成邮件任务，邮件状态为待发送
            /*$contact_detail = Contact::join('templates','templates.id','=','contacts.template_id')
                            ->where(['contacts.status'=>1,'task_status'=>1])
                            ->get()->toArray();
            //发送邮件任务列表
            $mails_forsend = MailForSend::get('receiver_email')->toArray();
            $mails_forsend_formate = array_column($mails_forsend,'receiver_email');
            $insert_forsend = [];
            foreach ($contact_detail as $k => $v){
                if(!in_array($v['email_address'],$mails_forsend_formate)){
                    $insert_forsend[$k]['receiver_email'] = $v['email_address'];
                    $insert_forsend[$k]['title'] = $v['email_title'];
                    $insert_forsend[$k]['content'] = $v['email_content'].$v['template_sign'];
                    $insert_forsend[$k]['send_start_hour'] = $v['send_start_hour'];
                    $insert_forsend[$k]['send_end_hour'] = $v['send_end_hour'];
                    $insert_forsend[$k]['send_max_num'] = env('SEND_MAIL_LIMIT_NUM');;
                    $insert_forsend[$k]['created_at'] = date('Y-m-d H:i:s',time());
                }
            }
            //写入到mail_for_sends
            MailForSend::insert($insert_forsend);*/
            return $this->response()->success('数据导入成功')->refresh();
        }catch (\Exception $e){
            return $this->response()->error($e -> getMessage());
        }

        //return $this->response()->success('Success message...')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-action">联系人导入</a>
HTML;
    }

    /**
     * 表单
     */
    public function form()
    {
        $this
            ->file('file', '请选择文件')
            ->options(['showPreview' => false,
                'allowedFileExtensions'=>['xlsx','xls'],
                'showUpload'=>true
            ])->required()->help('导入的文件必须是excel，具体格式请先下载导入模版，按模版文件中标题行的提示正确填写。其中，国家、行业必须按国家管理、行业管理中查询的值填写；模板名称是选填字段，如果要设置请填写模版管理中查询到的模版名称；开始和结束时间，是联系人所属国家的当地时间');
    }
    /**
     * 上传等待
     * @return string
     */
    public function handleActionPromise()
    {
        $resolve = <<<SCRIPT
var actionResolverss = function (data) {
            $('.modal-footer').show()
            $('.tips').remove()
            var response = data[0];
            var target   = data[1];

            if (typeof response !== 'object') {
                return $.admin.swal({type: 'error', title: 'Oops!'});
            }

            var then = function (then) {
                if (then.action == 'refresh') {
                    $.admin.reload();
                }

                if (then.action == 'download') {
                    window.open(then.value, '_blank');
                }

                if (then.action == 'redirect') {
                    $.admin.redirect(then.value);
                }
            };

            if (typeof response.html === 'string') {
                target.html(response.html);
            }

            if (typeof response.swal === 'object') {
                $.admin.swal(response.swal);
            }

            if (typeof response.toastr === 'object') {
                $.admin.toastr[response.toastr.type](response.toastr.content, '', response.toastr.options);
            }

            if (response.then) {
              then(response.then);
            }
        };

        var actionCatcherss = function (request) {
            $('.modal-footer').show()
            $('.tips').remove()

            if (request && typeof request.responseJSON === 'object') {
                $.admin.toastr.error(request.responseJSON.message, '', {positionClass:"toast-bottom-center", timeOut: 10000}).css("width","500px")
            }
        };
SCRIPT;

        Admin::script($resolve);

        return <<<SCRIPT
         $('.modal-footer').hide()
         let html = `<div class='tips' style='color: red;font-size: 18px;'>导入时间取决于数据量，请耐心等待结果不要关闭窗口！<img src="data:image/gif;base64,R0lGODlhEAAQAPQAAP///1VVVfr6+np6eqysrFhYWG5ubuPj48TExGNjY6Ojo5iYmOzs7Lq6utjY2ISEhI6OjgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAAFUCAgjmRpnqUwFGwhKoRgqq2YFMaRGjWA8AbZiIBbjQQ8AmmFUJEQhQGJhaKOrCksgEla+KIkYvC6SJKQOISoNSYdeIk1ayA8ExTyeR3F749CACH5BAkKAAAALAAAAAAQABAAAAVoICCKR9KMaCoaxeCoqEAkRX3AwMHWxQIIjJSAZWgUEgzBwCBAEQpMwIDwY1FHgwJCtOW2UDWYIDyqNVVkUbYr6CK+o2eUMKgWrqKhj0FrEM8jQQALPFA3MAc8CQSAMA5ZBjgqDQmHIyEAIfkECQoAAAAsAAAAABAAEAAABWAgII4j85Ao2hRIKgrEUBQJLaSHMe8zgQo6Q8sxS7RIhILhBkgumCTZsXkACBC+0cwF2GoLLoFXREDcDlkAojBICRaFLDCOQtQKjmsQSubtDFU/NXcDBHwkaw1cKQ8MiyEAIfkECQoAAAAsAAAAABAAEAAABVIgII5kaZ6AIJQCMRTFQKiDQx4GrBfGa4uCnAEhQuRgPwCBtwK+kCNFgjh6QlFYgGO7baJ2CxIioSDpwqNggWCGDVVGphly3BkOpXDrKfNm/4AhACH5BAkKAAAALAAAAAAQABAAAAVgICCOZGmeqEAMRTEQwskYbV0Yx7kYSIzQhtgoBxCKBDQCIOcoLBimRiFhSABYU5gIgW01pLUBYkRItAYAqrlhYiwKjiWAcDMWY8QjsCf4DewiBzQ2N1AmKlgvgCiMjSQhACH5BAkKAAAALAAAAAAQABAAAAVfICCOZGmeqEgUxUAIpkA0AMKyxkEiSZEIsJqhYAg+boUFSTAkiBiNHks3sg1ILAfBiS10gyqCg0UaFBCkwy3RYKiIYMAC+RAxiQgYsJdAjw5DN2gILzEEZgVcKYuMJiEAOwAAAAAAAAAAAA=="><\/div>`
         $('.modal-header').append(html)
process.then(actionResolverss).catch(actionCatcherss);
SCRIPT;
    }
}
