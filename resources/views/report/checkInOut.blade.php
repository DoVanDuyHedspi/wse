<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
  <table>
    <thead>
      <tr>
        <th colspan="8"><b>Lịch sử chấm công vào ra</b></th>
      </tr>
      <tr>
        <th colspan="8"></th>
      </tr>
      <tr>
        <th colspan="8"><b>Ngày {{date('d-m-Y', strtotime($date))}}</b></th>
      </tr>
      <tr>
        <th colspan="8"></th>
      </tr>
      <tr>
        <th rowspan="2"><b>STT</b></th>
        <th rowspan="2" colspan="2"><b>Tên</b></th>
        <th rowspan="2" colspan="2"><b>Email</b></th>
        <th rowspan="2" colspan="2"><b>Mã số nhân viên</b></th>
        <th rowspan="2" colspan="2"><b>Chi nhánh</b></th>
        <th rowspan="2" colspan="2"><b>Phòng ban</b></th>
        <th rowspan="2" colspan="2"><b>Vị trí</b></th>
        <th colspan="4" style="text-align: center;"><b>Chấm công</b></th>
      </tr>
      <tr>
        <th colspan="2" style="text-align: center;"><b>Đến</b></th>
        <th colspan="2" style="text-align: center;"><b>Về</b></th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $index => $user)
      <tr>
        <td style="text-align: left;">{{ $index + 1}}</td>
        <td colspan="2">{{ $user->name }}</td>
        <td colspan="2">{{ $user->email }}</td>
        <td colspan="2" style="text-align: left;">{{ $user->employee_code }}</td>
        <td colspan="2">{{ $user->branch->name }}</td>
        <td colspan="2">{{ $user->group->name }}</td>
        <td colspan="2">{{ $user->position->name }}</td>
        <td colspan="2" style="text-align: center;">{{ $user['time_in'] }}</td>
        <td colspan="2" style="text-align: center;">{{ $user['time_out'] }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot></tfoot>
  </table>
</body>

</html>