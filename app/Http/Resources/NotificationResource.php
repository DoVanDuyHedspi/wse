<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
  public function toArray($request)
  {
    if ($this->type == 'App\Notifications\ResultOfRequest') {
      $type = 'form-request';
      if($this->data['status'] == 'accept') {
        $mess = 'Yêu cầu xin phép của bạn đã được chấp nhận';
      } else if($this->data['status']== 'refuse') {
        $mess = 'Yêu cầu xin phép của bạn đã bị từ chối';
      } else if($this->data['status'] == 'cancel') {
        $mess = 'Yêu cầu xin phép của bạn đã bị hủy bỏ';
      }
    } else if ($this->type == 'App\Notifications\ResultOfComplain') {
      $type = 'form-complain';
      if($this->data['result'] == 'success') {
        $mess = 'Kết quả khiếu lại của bạn là thành công';
      } else if($this->data['result'] == 'fail') {
        $mess = 'Kết quả xác minh khiếu lại: thất bại';
      } else if($this->data['status'] == 'accept') {
        $mess = 'Khiếu lại của bạn đã được chấp nhận';
      } else if($this->data['status'] == 'refuse') {
        $mess = 'Khiếu lại của bạn đã bị từ chối';
      }
    }
    $date_time = date('d-m-Y H:i:s', strtotime($this->created_at));
    return [
      'id' => $this->id,
      'message' => $mess,
      'date_time' => $date_time,
      'type' => $type,
      'read_at' => $this->read_at
    ];
  }
}
