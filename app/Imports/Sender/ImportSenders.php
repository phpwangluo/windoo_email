<?php

namespace App\Imports\Sender;

use App\Models\MailSetting;
use App\Models\Sender;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ImportSenders implements ToModel,WithStartRow,WithValidation,SkipsOnFailure,WithUpserts
{
    use Importable,SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //数据逻辑处理
        //根据运营商名称查询邮箱配置ID
        $mail_setting_id = MailSetting::where(['support_name'=>trim($row[0])])->first('id');
        return new Sender([
            'mail_setting_id'=> $mail_setting_id->id ? $mail_setting_id->id :1,
            'email_address' => $row[1],
            'email_pass' => $row[2],
            'email_sign' => $row[3],
            'remarks' => $row[4] ? $row[4] : '',
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
            '2' => 'required',
            '1'=>Rule::unique('senders','email_address')->where('status',1),
        ];
    }
    // 自定义验证信息
    public function customValidationMessages()
    {
        return [
            '0.required' => '邮箱运营商必填',
            '1.required' => '邮箱地址必填',
            '2.required' => '邮箱密码必填',
            '1.unique'=>'系统中已经存在的重复的邮箱地址'
        ];
    }

    public function uniqueBy()
    {
        return 'email_address';
    }
}
