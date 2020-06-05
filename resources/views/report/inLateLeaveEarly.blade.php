<table style="width: 100%;">
  <thead>
    <tr><th colspan="30"><b>Vi phạm lỗi đi muộn về sớm</b></th></tr>
    <tr>
      <th colspan="30"></th>
    </tr>
    <tr>
      <th rowspan="2"><b>STT</b></th>
      <th rowspan="2" colspan="2"><b>Tên</b></th>
      <th rowspan="2" colspan="2"><b>Phòng ban</b></th>
      <th rowspan="2" colspan="2"><b>Vị trí</b></th>
      @foreach($daysOfMonth as $date)
      <th><b>{{date('d-m', strtotime($date))}}</b></th>
      @endforeach
      <th rowspan="2" colspan="2"><b>Số lần đi muộn</b></th>
      <th rowspan="2" colspan="2"><b>Số lần về sớm</b></th>
      <th rowspan="2" colspan="2"><b>Tổng số lần</b></th>
      <th rowspan="2" colspan="2"><b>Tổng số phút</b></th>
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
      <td colspan="2">{{ $user->name }}</td>
      <td colspan="2">{{ $user->group->name }}</td>
      <td colspan="2">{{ $user->position->name }}</td>
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
      <td colspan="2" style="text-align: center;">{{ $user->total_il }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_le }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_faults }}</td>
      <td colspan="2" style="text-align: center;">{{ $user->total_time }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot></tfoot>
</table>