<?php

return [
  'vehicle_type' => ['Xe máy', 'Ô tô', 'Xe buýt', 'Đi bộ'],
  'identity_type' => ['CMND', 'Hộ chiếu'],
  'bank_type' => ['Thanh toán', 'Doanh nghiệp', 'Tín dụng', 'Ký gửi', 'Tiết kiệm'],
  'gender' => ['Nam', 'Nữ', 'Khác'],
  'morning_begin' => '07:45:00',
  'morning_late' => '09:45:00',
  'morning_end' => '11:45:00',
  'afternoon_begin' => '12:45:00',
  'afternoon_late' => '14:45:00',
  'afternoon_end' => '16:45:00',
  'form_request_type' => ['OT', 'ILM', 'LEM', 'ILA', 'LEA', 'LO', 'RM', 'QQD', 'QQV', 'QQF'],
  'form_request_status' => ['waiting', 'accept', 'refuse'],
  'form_check_camera_result' => ['waiting', 'fail', 'success'],
  'form_check_camera_status' => ['waiting', 'accept', 'refuse'],
  'drive_folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
];