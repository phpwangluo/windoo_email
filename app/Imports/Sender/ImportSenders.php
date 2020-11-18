<?php

namespace App\Imports\Sender;

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

        return new Sender([
            //
            'email_address' => $row[0],
            'email_pass' => $row[1],
            'email_sign' => $row[2],
            'remarks' => $row[3],
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
            '0'=>Rule::unique('senders','email_address')->where('status',1),
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
