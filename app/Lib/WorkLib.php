<?php

namespace App\Lib;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttp;

class WorkLib
{
  public $machine_ip;
  public $auth_token;
  public $device_id;
  public $headers;

  public function __construct()
  {
    $this->machine_ip = config('api.machine_ip');
    $this->auth_token = config('api.auth_token');
    $this->device_id = config('api.device_id');

    $this->headers = [
      'Content-Type' => 'application/json',
      'Authorization' => $this->auth_token
    ];
  }

  public function getTimeKeepingData()
  {
    $client = new GuzzleHttp();
    $info = [
      "operator" => "SearchControl",
      "info" => [
        "DeviceID" => $this->device_id,
        "Picture" => 0,
        "BeginTime" => Carbon::now('Asia/Ho_Chi_Minh')->subDay()->format('Y-m-d\\T H:i:s'),
        "EndTime" => Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d\\T H:i:s'),
        "RequestCount" => 100,
        "Idtype" => 0
      ]
    ];
    $body = json_encode($info, JSON_UNESCAPED_SLASHES);
    $res = $client->request(
      'POST',
      $this->machine_ip . '/action/SearchControl',
      [
        'headers' => $this->headers,
        'body' => $body
      ]
    );
    $res = json_decode($res->getBody()->getContents(), true);
    return $res;
  }

  public function searchEventOfUser($date, $begin_time, $end_time, $user_code)
  {
    $begin = $date.'T'.$begin_time;
    $end = $date.'T'.$end_time;
    $client = new GuzzleHttp();
    $info = [
      "operator" => "SearchControl",
      "info" => [
        "DeviceID" => $this->device_id,
        "CustomizeID" => $user_code,
        "Picture" => 0,
        "BeginTime" => $begin,
        "EndTime" => $end,
        "RequestCount" => 100,
        "IdType" => 0,
        "SearchType" => 1,
      ]
    ];
    $body = json_encode($info, JSON_UNESCAPED_SLASHES);
    $res = $client->request(
      'POST',
      $this->machine_ip . '/action/SearchControl',
      [
        'headers' => $this->headers,
        'body' => $body
      ]
    );
    $res = json_decode($res->getBody()->getContents(), true);
    return $res;
  }
}
