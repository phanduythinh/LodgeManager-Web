<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email,' . $this->customer?->id],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'address' => ['required', 'string', 'max:255', 'min:5'],
            'id_card' => ['required', 'string', 'regex:/^[0-9]{9,12}$/', 'unique:customers,id_card,' . $this->customer?->id],
            'id_card_issue_date' => ['required', 'date', 'before_or_equal:today'],
            'id_card_issue_place' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'occupation' => ['required', 'string', 'max:255'],
            'workplace' => ['required', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'emergency_contact_relationship' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive,blacklisted'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc',
            'name.min' => 'Họ tên phải có ít nhất 3 ký tự',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'phone.required' => 'Số điện thoại là bắt buộc',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.min' => 'Địa chỉ phải có ít nhất 5 ký tự',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'id_card.required' => 'CMND/CCCD là bắt buộc',
            'id_card.regex' => 'CMND/CCCD không hợp lệ',
            'id_card.unique' => 'CMND/CCCD đã tồn tại',
            'id_card_issue_date.required' => 'Ngày cấp CMND/CCCD là bắt buộc',
            'id_card_issue_date.date' => 'Ngày cấp CMND/CCCD không hợp lệ',
            'id_card_issue_date.before_or_equal' => 'Ngày cấp CMND/CCCD không được sau ngày hiện tại',
            'id_card_issue_place.required' => 'Nơi cấp CMND/CCCD là bắt buộc',
            'id_card_issue_place.max' => 'Nơi cấp CMND/CCCD không được vượt quá 255 ký tự',
            'date_of_birth.required' => 'Ngày sinh là bắt buộc',
            'date_of_birth.date' => 'Ngày sinh không hợp lệ',
            'date_of_birth.before_or_equal' => 'Khách hàng phải đủ 18 tuổi',
            'gender.required' => 'Giới tính là bắt buộc',
            'gender.in' => 'Giới tính không hợp lệ',
            'occupation.required' => 'Nghề nghiệp là bắt buộc',
            'occupation.max' => 'Nghề nghiệp không được vượt quá 255 ký tự',
            'workplace.required' => 'Nơi làm việc là bắt buộc',
            'workplace.max' => 'Nơi làm việc không được vượt quá 255 ký tự',
            'emergency_contact_name.required' => 'Tên người liên hệ khẩn cấp là bắt buộc',
            'emergency_contact_name.max' => 'Tên người liên hệ khẩn cấp không được vượt quá 255 ký tự',
            'emergency_contact_phone.required' => 'Số điện thoại liên hệ khẩn cấp là bắt buộc',
            'emergency_contact_phone.regex' => 'Số điện thoại liên hệ khẩn cấp không hợp lệ',
            'emergency_contact_relationship.required' => 'Mối quan hệ với người liên hệ khẩn cấp là bắt buộc',
            'emergency_contact_relationship.max' => 'Mối quan hệ không được vượt quá 255 ký tự',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự',
        ];
    }
}
