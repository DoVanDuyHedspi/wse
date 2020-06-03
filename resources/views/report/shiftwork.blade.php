<table>
  <thead>
    <tr>
      <th rowspan="2"><b>STT</b></th>
      <th rowspan="2"><b>Tên</b></th>
      <th rowspan="2"><b>Phòng ban</b></th>
      <th rowspan="2"><b>Vị trí</b></th>
      @foreach($daysOfMonth as $date)
      <th><b>{{date('d-m', strtotime($date))}}</b></th>
      @endforeach
      <th rowspan="2"><b>Tổng cộng</b></th>
      <th rowspan="2"><b>Số lỗi đi muộn, về sớm</b></th>
      <th colspan="3" align="center"><b>Nghỉ</b></th>
      <th rowspan="2"><b>Công tăng ca</b></th>
      <th rowspan="2"><b>Công tính lương</b></th>
    </tr>
    <tr>
      @foreach($daysOfMonth as $date)
      @if(date('N', strtotime($date)) != 7)
      <th><b>Thứ {{date('N', strtotime($date)) + 1}}</b></th>
      @else
      <th><b>CN</b></th>
      @endif
      @endforeach
      <th><b>Nghỉ không lương</b></th>
      <th><b>Nghỉ có lương</b></th>
      <th><b>Nghỉ lễ</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach($users as $index => $user)
    <tr>
      <td>{{ $index + 1}}</td>
      <td>{{ $user->name }}</td>
      <td>{{ $user->group->name }}</td>
      <td>{{ $user->position->name }}</td>
      @foreach($daysOfMonth as $date)
      <td>{{$user[$date]}}</td>
      @endforeach
      <td>{{ $user->total_works }}</td>
      <td>{{ $user->total_faults }}</td>
      <td>{{ $user->total_nkl }}</td>
      <td>{{ $user->total_ncl }}</td>
      <td>{{ $user->total_holiday }}</td>
      <td>{{ $user->total_ot }}</td>
      <td>{{ $user->total_cl }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot></tfoot>
</table>