<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class UsersExport implements WithHeadings, WithMapping, FromQuery
{
  use Exportable;
  protected $listUserIds;
  public function __construct($listUserIds)
  {
    $this->listUserIds = $listUserIds;
  }
  public function headings(): array
  {
    return [
      'id',
      'Tên',
      'Mã số nhân viên',
      'Giới tính',
      'Email công việc',
      'Email cá nhân',
      'Số điện thoại',
      'Quốc tịch',
      'Ngày sinh',
      'Địa chỉ hiện tại',
      'Địa chỉ thường chú',
      'Mã số thuế'
    ];
  }

  public function query()
  {
    return User::query()->whereIn('id', $this->listUserIds);
  }

  public function map($row): array
  {
    $fields = [
      $row->id,
      $row->name,
      $row->employee_code,
      $row->gender,
      $row->email,
      $row->personal_email,
      $row->phone_number,
      $row->nationality,
      $row->birthday,
      $row->current_address,
      $row->permanent_address,
      $row->tax_code,
    ];
    return $fields;
  }
}
