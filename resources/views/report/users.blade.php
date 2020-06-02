<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
  <table>
    <thead>
      <tr>
        <th><b>STT</b></th>
        <th><b>Tên</b></th>
        <th><b>Email</b></th>
        <th><b>Mã số nhân viên</b></th>
        <th><b>Chi nhánh</b></th>
        <th><b>Phòng ban</b></th>
        <th><b>Vị trí</b></th>
        <th><b>Giới tính</b></th>
        <th><b>Số điện thoại</b></th>
        <th><b>Ngày sinh</b></th>
        <th><b>Địa chỉ hiện tại</b></th>
        <th><b>Địa chỉ thường chú</b></th>
        <th><b>Số CMTND</b></th>
        <th><b>Ngày tạo tài khoản</b></th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $index => $user)
      <tr>
        <td>{{ $index + 1}}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->employee_code }}</td>
        <td>{{ $user->branch->name }}</td>
        <td>{{ $user->group->name }}</td>
        <td>{{ $user->position->name }}</td>
        <td>{{ $user->gender }}</td>
        <td>{{ $user->number_phone }}</td>
        <td>{{ $user->birthday }}</td>
        <td>{{ $user->current_address }}</td>
        <td>{{ $user->permanent_address }}</td>
        <td>{{ $user->identity_card_passport->code }}</td>
        <td>{{ date('d-m-Y', strtotime($user->created_at)) }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot></tfoot>
  </table>
</body>

</html>