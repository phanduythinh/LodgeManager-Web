<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegalDocumentRequest extends FormRequest
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
            'document_number' => ['required', 'string', 'max:50', 'unique:legal_documents,document_number,' . $this->legal_document?->id],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after:issue_date'],
            'status' => ['required', 'string', 'in:active,expired,revoked'],
            'type' => ['required', 'string', 'in:contract,license,permit,certificate'],
            'file_path' => ['required', 'string'],
            'building_id' => ['required', 'exists:buildings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'document_number.required' => 'Số tài liệu là bắt buộc',
            'document_number.unique' => 'Số tài liệu đã tồn tại',
            'issue_date.required' => 'Ngày phát hành là bắt buộc',
            'issue_date.date' => 'Ngày phát hành không hợp lệ',
            'expiry_date.required' => 'Ngày hết hạn là bắt buộc',
            'expiry_date.date' => 'Ngày hết hạn không hợp lệ',
            'expiry_date.after' => 'Ngày hết hạn phải sau ngày phát hành',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'type.required' => 'Loại tài liệu là bắt buộc',
            'type.in' => 'Loại tài liệu không hợp lệ',
            'file_path.required' => 'File tài liệu là bắt buộc',
            'building_id.required' => 'Tòa nhà là bắt buộc',
            'building_id.exists' => 'Tòa nhà không tồn tại',
        ];
    }
}
