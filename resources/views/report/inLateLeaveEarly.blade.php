<table>
  <thead>
    <tr><th colspan="30">Vi phạm lỗi đi muộn về sớm</th></tr>
    <tr>
      <th rowspan="2"><b>STT</b></th>
      <th rowspan="2"><b>Tên</b></th>
      <th rowspan="2"><b>Phòng ban</b></th>
      <th rowspan="2"><b>Vị trí</b></th>
      @foreach($daysOfMonth as $date)
      <th><b>{{date('d-m', strtotime($date))}}</b></th>
      @endforeach
      <th rowspan="2"><b>Số lần đi muộn</b></th>
      <th rowspan="2"><b>Số lần về sớm</b></th>
      <th rowspan="2"><b>Tổng số lần</b></th>
      <th rowspan="2"><b>Tổng số phút</b></th>
    </tr>
    <tr>
      @foreach($daysOfMonth as $date)
      @if(date('N', strtotime($date)) != 7)
      <th><b>Thứ {{date('N', strtotime($date)) + 1}}</b></th>
      @else
      <th><b>CN</b></th>
      @endif
      @endforeach
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
      <td>
        @if($user[$date]['IL'] && $user[$date]['LE'])
        {{'Đi muộn và về sớm ' . $user[$date]['time'] . 'p'}}
        @elseif($user[$date]['IL'])
        {{'Đi muộn ' . $user[$date]['time'] . 'p'}}
        @elseif($user[$date]['LE'])
        {{'Về sớm ' . $user[$date]['time'] . 'p'}}
        @endif
      </td>
      @endforeach
      <td>{{ $user->total_il }}</td>
      <td>{{ $user->total_le }}</td>
      <td>{{ $user->total_faults }}</td>
      <td>{{ $user->total_time }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot></tfoot>
</table>