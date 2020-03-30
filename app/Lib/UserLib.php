<?php

namespace App\Lib;

use GuzzleHttp\Client as GuzzleHttp;

class UserLib
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

  public function addPerson($userInfo)
  {
    $client = new GuzzleHttp();
    $av = $userInfo->getMedia('avatar');
    $path = $av[0]->getPath();
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $image = file_get_contents($path);
    $base64 = 'data:image/jpeg;base64,' . base64_encode($image);
    $gender = $userInfo->gender == "Nam" ? 0 : 1;
    $info = [
      "operator" => "AddPerson",
      "info" => [
        "DeviceID" => $this->device_id,
        "PersonType" => 0,
        "IdType" => 0,
        "CustomizeID" => $userInfo->employee_code,
        "Name" => $userInfo->name,
        "Gender" => $gender
      ],
      "picinfo" => $base64,
    ];
    $body = json_encode($info, JSON_UNESCAPED_SLASHES);
    $res = $client->request(
      'POST',
      $this->machine_ip . '/action/AddPerson',
      [
        'headers' => $this->headers,
        'body' => $body
      ]
    );
    $res = json_decode($res->getBody()->getContents(), true);
    return $res;
  }

  public function editPerson($userInfo)
  {
    $client = new GuzzleHttp();
    $av = $userInfo->getMedia('avatar');
    $path = $av[0]->getPath();
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $image = file_get_contents($path);
    $base64 = 'data:image/jpeg;base64,' . base64_encode($image);
    $gender = $userInfo->gender == "Nam" ? 0 : 1;
    $info = [
      "operator" => "EditPerson",
      "info" => [
        "DeviceID" => $this->device_id,
        "PersonType" => 0,
        "IdType" => 0,
        "CustomizeID" => $userInfo->employee_code,
        "Name" => $userInfo->name,
        "Gender" => $gender
      ],
      "picinfo" => $base64,
    ];
    $body = json_encode($info, JSON_UNESCAPED_SLASHES);
    $res = $client->request(
      'POST',
      $this->machine_ip . '/action/EditPerson',
      [
        'headers' => $this->headers,
        'body' => $body
      ]
    );
    $response = json_decode($res->getBody()->getContents(), true);
    return $response;
  }

  public function deletePerson($userInfo)
  {
    $client = new GuzzleHttp();
    $info = [
      "operator" => "DeletePerson",
      "info" => [
        "DeviceID" => $this->device_id,
        "TotalNum" => 1,
        "IdType" => 0,
        "CustomizeID" => [$userInfo->employee_code]
      ]
    ];
    // dd($userInfo->employee_code);
    $body = json_encode($info, JSON_UNESCAPED_SLASHES);
    $res = $client->request(
      'POST',
      $this->machine_ip . '/action/DeletePerson',
      [
        'headers' => $this->headers,
        'body' => $body
      ]
    );
    $response = json_decode($res->getBody()->getContents(), true);
    return $response;
  }
}
