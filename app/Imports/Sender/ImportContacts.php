<?php

namespace App\Imports\Sender;

use App\Models\Contact;
use App\Models\Country;
use App\Models\MailSetting;
use App\Models\Template;
use App\Models\Trade;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ImportContacts implements ToModel,WithStartRow,WithValidation,SkipsOnFailure,WithUpserts
{
    use Importable,SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //数据映射处理
        $coutry_id = Country::where(['country_name'=>trim($row[1])])->first('id');
        $trade_id = Trade::where(['trade_name'=>trim($row[2])])->first('id');
        $template_id = Template::where(['template_name'=>trim($row[3])])->first('id');
        return new Contact([
            'email_address' => $row[0],
            'country_id' => $coutry_id->id ? $coutry_id->id :1,
            'trade_id' => $trade_id->id ? $trade_id->id :1,
            'template_id' => $template_id->id ? $template_id->id :1,
            'customer_tag' => $row[4],
            'send_start_hour' => $row[5],
            'send_end_hour' => $row[6],
            'remarks' => $row[7],
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
            '3' => 'required',
            '4' => 'required',
            '5' => 'required',
            '6' => 'required',
            '0'=>Rule::unique('contacts','email_address')->where('status',1),
        ];
    }
    // 自定义验证信息
    public function customValidationMessages()
    {
        return [
            '0.required' => '邮箱地址必填',
            '1.required' => '邮箱密码必填',
            '0.unique'=>'系统中已经存在的重复的邮箱地址'
        ];
    }

    public function uniqueBy()
    {
        return 'email_address';
    }
}
