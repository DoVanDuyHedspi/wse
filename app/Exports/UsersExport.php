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
      'name',
      'employee_code',
      'gender',
      'email',
      'personal_email',
      'phone_number',
      'nationality',
      'birthday',
      'current_address',
      'permanent_address',
      'tax_code'
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
