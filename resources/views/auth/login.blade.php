@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login') }}" id="login">
  @csrf
  <section class="login-block" style="height: 672px;">
    <div class="container">
      <div class="row">
        <div class="col-md-4 login-sec">
          <h2 class="text-center">Đăng nhập</h2>
          <form class="login-form">
            <div class="form-group">
              <label for="exampleInputEmail1" class="text-uppercase">Tài khoản</label>
              <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif

            </div>
            <div class="form-group">
              <label for="exampleInputPassword1" class="text-uppercase">Mật khẩu</label>
              <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

              @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
              @endif
            </div>


            <div class="form-check">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <small>Lưu đăng nhập</small>
              </label>
              <button type="submit" class="btn btn-login float-right">Đăng nhập</button>
            </div>
            @if (Route::has('password.request'))
            <a class="btn btn-link" href="{{ route('password.request') }}">
              {{ __('Quên mật khẩu') }}
            </a>
            @endif

          </form>
        </div>
        <div class="col-md-8 banner-sec">
          <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
              <!-- <li data-target="#carouselExampleIndicators" data-slide-to="2"></li> -->
            </ol>
            <div class="carousel-inner" role="listbox">
              <div class="carousel-item active">
                <img class="d-block img-fluid" src="/storage/login_image/photo1.jpg" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                  <!-- <div class="banner-text">
                    <h2>Quản lý chấm công</h2>
                    <p>WSE là công cụ giúp nhà quản lý có thể dễ dàng quản lý dữ liệu chấm công của nhân viên. Ngoài ra, quản lý cũng có thể xuất file excel, csv các loại báo cáo.</p>
                  </div> -->
                </div>
              </div>
              <div class="carousel-item">
                <img class="d-block img-fluid" src="/storage/login_image/photo2.jpg" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                  <!-- <div class="banner-text">
                    <h2>Làm đơn thư</h2>
                    <p>WSE là công cụ giúp nhân viên có thể làm đơn thư gửi đến nhà quản lý. Quản lý có thể dễ dàng quản lý đơn thư đã được phân loại và dễ dàng phê duyệt.</p>
                  </div> -->
                </div>
              </div>
              <!-- <div class="carousel-item">
                <img class="d-block img-fluid" src="/storage/login_image/photo3.jpeg" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                  <div class="banner-text">
                    <h2>Lưu trữ video</h2>
                    <p>WSE lưu trữ toàn bộ video thu được từ máy chấm công nhằm phục vụ nghiệp vụ xác minh chấm công sau này.</p>
                  </div>
                </div>
              </div> -->
            </div>

          </div>
        </div>
      </div>
  </section>
</form>
@endsection