<table>
  <thead>
    <tr>
      <th colspan="52" style="text-align: center;"><b>Bảng công tháng {{date('m-Y', strtotime($month))}}</b></th>
    </tr>
    <tr>
      <th rowspan="2"><b>STT</b></th>
      <th rowspan="2" colspan="2"><b>Tên</b></th>
      <th rowspan="2" colspan="2"><b>Phòng ban</b></th>
      <th rowspan="2" colspan="2"><b>Vị trí</b></th>
      @foreach($daysOfMonth as $date)
      <th><b>{{date('d-m', strtotime($date))}}</b></th>
      @endforeach
      <th rowspan="2" colspan="2"><b>Tổng cộng</b></th>
      <th rowspan="2" colspan="2"><b>Số lỗi đi muộn, về sớm</b></th>
      <th colspan="6" style="text-align: center"><b>Nghỉ</b></th>
      <th rowspan="2" colspan="2"><b>Công tăng ca</b></th>
      <th rowspan="2" colspan="2"><b>Công tính lương</b></th>
    </tr>
    <tr>
      @foreach($daysOfMonth as $date)
      @if(date('N', strtotime($date)) != 7)
      <th><b>Thứ {{date('N', strtotime($date)) + 1}}</b></th>
      @else
      <th><b>CN</b></th>
      @endif
      @endforeach
      <th colspan="2"><b>Nghỉ không lương</b></th>
      <th colspan="2"><b>Nghỉ có lương</b></th>
      <th colspan="2"><b>Nghỉ lễ</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach($users as $index => $user)
    <tr>
      <td>{{ $index + 1}}</td>
      <td colspan="2">{{ $user->name }}</td>
      <td colspan="2">{{ $user->group->name }}</td>
      <td colspan="2">{{ $user->position->name }}</td>
      @foreach($daysOfMonth as $date)
      <td style="text-align: center;">{{$user[$date]}}</td>
      @endforeach
      <td colspan="2" style="text-align: center;">{{ $user->total_works }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_faults }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_nkl }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_ncl }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_holiday }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_ot }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_cl }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot></tfoot>
</table>