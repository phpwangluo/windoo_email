<?php

namespace App\Imports\Sender;

use App\Models\Contact;
use App\Models\Country;
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
        $country = '';
        $trade = '';
        $template = '';
        $send_start_hour = '';
        $send_end_hour = '';
        //数据映射处理
        if(trim($row[1])){
            $country_id = Country::where(['country_name'=>trim($row[1])])->first();
            if(!empty($country_id)){
                $country = $country_id->id;
            }
        }
        if(trim($row[2])){
            $trade_id = Trade::where(['trade_name'=>trim($row[2])])->first();
            if(!empty($trade_id)){
                $trade = $trade_id->id;
            }
        }
        if(trim($row[3])){
            $template_id = Template::where(['template_name'=>trim($row[3])])->first();
            if(!empty($template_id)){
                $template = $template_id->id;
            }
        }else{
            //通过国家和行业获取模板ID
            if($country && $trade){
                $getTemplate = Template::where([
                    'country_id'=>$country,
                    'trade_id'=>$trade
                ])->first();
                if(!empty($getTemplate)){
                    $template = $getTemplate->id;
                }
            }
        }
        if(!trim($row['5']) || !trim($row['6'])){
            //此模板对应的国家设置的时间区间
            if(!empty($country_id)){
                $send_start_hour = $country_id->send_start_hour;
                $send_end_hour = $country_id->send_end_hour;
            }
        }else{
            $send_start_hour = trim($row['5']);
            $send_end_hour = trim($row['6']);
        }
        return new Contact([
            'email_address' => $row[0] ? trim($row[0]) : '',
            'country_id' => $country,
            'trade_id' => $trade,
            'template_id' => $template,
            'customer_tag' => $row[4] ? trim($row[4]) : '',
            'send_start_hour' => $send_start_hour,
            'send_end_hour' => $send_end_hour,
            'remarks' => $row[7] ? trim($row[7]) : '',
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
            '4' => 'required',
            '0'=>Rule::unique('contacts','email_address')->where('status',1),
            '1'=>Rule::exists('countries','country_name'),
            '2'=>Rule::exists('trades','trade_name')
        ];
    }
    // 自定义验证信息
    public function customValidationMessages()
    {
        return [
            '0.required' => '联系人邮箱必填',
            '1.required' => '联系人所属国家必填',
            '2.required' => '联系人所属行业必填',
            '4.required' => '项目必填',
            '0.unique'=>'系统中已经存在的重复的邮箱地址',
            '1.exists'=>'系统中不存在您所填写的国家',
            '2.exists'=>'系统中不存在您所填写的行业'
        ];
    }

    public function uniqueBy()
    {
        return 'email_address';
    }
}
