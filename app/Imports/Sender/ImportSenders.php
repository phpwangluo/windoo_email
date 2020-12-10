<?php

namespace App\Imports\Sender;

use App\Mail\ContactSender;
use App\Models\MailSetting;
use App\Models\Sender;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Validators\ValidationException;
use Webklex\IMAP\Facades\Client;
use Maatwebsite\Excel\Validators\Failure;

class ImportSenders implements ToModel,WithStartRow,WithValidation,SkipsOnFailure,WithUpserts
{
    use Importable,SkipsFailures;
    private $errors = []; // array to accumulate errors
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $email_address = trim($row[0]);
        //数据逻辑处理
        //根据运营商名称查询邮箱配置ID
        //截取邮箱的@后运营商名称
        $mail_setting_id = '';
        if($email_address){
            $email_arr = explode('@',$email_address);
            $email_arr_arr = explode('.',$email_arr[1]);
            $support_name = $email_arr_arr[0];
            $mail_setting = MailSetting::where(['support_name'=>$support_name])->first();
            $mail_setting_id = $mail_setting->id;
            /*try {
                //验证发件箱是否可以正常发送邮件
                $re = explode('@', $email_address);
                $config = array(
                    'driver' => $mail_setting->driver,
                    'host' => $mail_setting->host,
                    'port' => $mail_setting->port,
                    'from' => array('address' => $email_address, 'name' => $re[0]),
                    'encryption' => $mail_setting->encryption,
                    'username' => $email_address,
                    'password' => trim($row[1]),
                    //'sendmail' => '/usr/sbin/sendmail -bs',
                    'pretend' => false
                );
                Config()->set('mail',$config);
                $subject = '测试邮件是否可用';
                $viewData = [
                    'content' => '这是一封验证GP发件箱是否可用的邮件！',
                ];
                //验证发件箱是否可以正常拉取邮件
                $get_config = [
                    'host'          => $mail_setting->getmail_host,
                    'port'          => $mail_setting->getmail_port,
                    'encryption'    => $mail_setting->getmail_encryption,
                    'validate_cert' => true,
                    'username'      => $email_address,
                    'password'      => trim($row[1]),
                    'protocol'      => $mail_setting->getmail_protocol,
                ];
                Mail::to('1617049813@qq.com')->send(new ContactSender($subject, $viewData));
                $client = Client::make($get_config);
                $client->connect();
            }catch (ValidationException $e) {
                //echo '<script>alert("'.$email_address.'邮箱配置不正确，请配置正确的邮箱保证可以正常发送和拉取邮件")</script>';exit();
            }*/
        }
        return new Sender([
            'mail_setting_id'=> $mail_setting_id ? $mail_setting_id : '',
            'email_address' => $email_address,
            'email_pass' => trim($row[1]),
            'email_sign' => $row[2]? trim($row[2]) : '',
            'remarks' => $row[3] ? trim($row[3]) : '',
        ]);
    }

    /**
     * 从第几行开始处理数据 就是不处理标题
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    // 验证
    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required',
            '0'=> Rule::unique('senders','email_address')->where('status',1),
        ];
    }

    // 自定义验证信息
    public function customValidationMessages()
    {
        return [
            '0.required' => '邮箱地址必填',
            '1.required' => '邮箱密码必填',
            '0.unique'=>'系统中已经存在的重复的邮箱地址',
            //'0.in' => '邮箱配置不正确，请配置正确的邮箱保证可以正常发送和拉取邮件'
        ];
    }

    public function uniqueBy()
    {
        return 'email_address';
    }

}
