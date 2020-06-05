<table id="table">
  <thead>
    <tr>
      <th colspan="10"><b>Bảng chấm công tháng {{date('m-Y', strtotime($month))}} của nhân viên</b></th>
    </tr>
    <tr>
      <th colspan="30"></th>
    </tr>
    <tr>
      <th colspan="4">Tên nhân viên: {{$user->name}}</th>
      <th colspan="4">Mã số nhân viên: {{$user->employee_code}}</th>
      <th colspan="4">Phòng ban: {{$user->group->name}}</th>
      <th colspan="4">Chức vụ: {{$user->position->name}}</th>
    </tr>
    <tr>
      <th colspan="30"></th>
    </tr>
    <tr>
      <th colspan="4">Tổng ngày đi làm: {{$user->total_work}}</th>
      <th colspan="4">Số lần bị phạt: {{$user->total_fined}}</th>
      <th colspan="4">Thời gian đi muộn, về sớm: {{$user->total_fined_time}} phút</th>
      <th colspan="4">Tổng thời gian làm thêm: {{$user->total_ot}} giờ</th>
    </tr>
    <tr>
      <th colspan="30"></th>
    </tr>
    <tr>
      <th colspan="2"><b>Thứ</b></th>
      <th colspan="2"><b>Ngày</b></th>
      <th colspan="2"><b>Chấm công đến</b></th>
      <th colspan="2"><b>Chấm công về</b></th>
      <th colspan="2"><b>Đi muộn, về sớm</b></th>
      <th colspan="2"><b>Làm thêm</b></th>
      <th colspan="3"><b>Trạng thái</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach($events as $event)
    <tr>
      @if(date('N', strtotime($event['date'])) != 7)
      <td colspan="2">Thứ {{date('N', strtotime($event['date'])) + 1}}</td>
      @else
      <td colspan="2">Chủ nhật</td>
      @endif
      <td colspan="2">{{date('d-m', strtotime($event['date']))}}</td>
      <td colspan="2" style="text-align: center;">{{$event['time_in']}}</td>
      <td colspan="2" style="text-align: center;">{{$event['time_out']}}</td>
      <td colspan="2" style="text-align: center;">{{$event['fined_time']}}</td>
      <td colspan="2" style="text-align: center;">{{$event['ot_time']}}</td>
      @if($event['classes'] == 'dg')
      <td colspan="3">Đúng giờ</td>
      @elseif($event['classes'] == 'dmvs')
      <td colspan="3">Đi muộn, về sớm</td>
      @elseif($event['classes'] == 'dlb')
      <td colspan="3">Đã làm bù</td>
      @elseif($event['classes'] == 'ncl')
      <td colspan="3">Nghỉ có luơng, nghỉ lễ</td>
      @elseif($event['classes'] == 'nkl')
      <td colspan="3">Nghỉ phép không lương</td>
      @elseif($event['classes'] == 'weeken')
      <td colspan="3">Cuối tuần</td>
      @elseif($event['classes'] == 'ktc')
      <td colspan="3">Chưa được tính công</td>
      @endif
    </tr>
    @endforeach
  </tbody>
  <tfoot></tfoot>

</table>