<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'report_date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:financial,occupancy,maintenance,other'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'building_id' => ['required', 'exists:buildings,id'],
            'data' => ['required', 'array'],
            'data.*.key' => ['required', 'string'],
            'data.*.value' => ['required'],
            'data.*.unit' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'report_date.required' => 'Ngày báo cáo là bắt buộc',
            'report_date.date' => 'Ngày báo cáo không hợp lệ',
            'type.required' => 'Loại báo cáo là bắt buộc',
            'type.in' => 'Loại báo cáo không hợp lệ',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'building_id.required' => 'Tòa nhà là bắt buộc',
            'building_id.exists' => 'Tòa nhà không tồn tại',
            'data.required' => 'Dữ liệu báo cáo là bắt buộc',
            'data.*.key.required' => 'Tên trường dữ liệu là bắt buộc',
            'data.*.value.required' => 'Giá trị dữ liệu là bắt buộc',
        ];
    }
}
