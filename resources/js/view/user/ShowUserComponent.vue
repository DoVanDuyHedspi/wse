<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Hồ sơ nhân viên</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="error" v-if="error.message.length">
      <div class="alert alert-danger" role="alert">{{ error.message }}</div>
    </div>
    <div class="noti" v-if="noti.length">
        <div class="alert alert-success" role="alert">{{ noti }}</div>
      </div>
    <div class="container">
      <div class="content" v-if="user">
        <div class="content-header">
          <b>Thông tin cá nhân</b>
        </div>
        <div class="content-body">
          <el-row :gutter="20">
            <el-col :span="6">
              <div class="grid-content">
                <el-card :body-style="{ padding: '0px' }">
                  <img
                    src="https://shadow.elemecdn.com/app/element/hamburger.9cf7b091-55e9-11e9-a976-7f4d0b07eef6.png"
                    class="image"
                  />
                  <div class="px-3">
                    <div class="bottom clearfix">
                      <el-button type="text" class="button">Operating</el-button>
                    </div>
                  </div>
                </el-card>
              </div>
            </el-col>
            <el-col :span="18">
              <div class="grid-content">
                <div class="info">
                  <div class="header p-3">
                    <span>
                      <b>Thông tin chung</b>
                    </span>
                  </div>
                  <div class="p-3 body">
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-user-solid"></i>
                          <span class="title">Tên</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.name}}</div>
                      </el-col>
                    </el-row>
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-message"></i>
                          <span class="title">Email</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.email}}</div>
                      </el-col>
                    </el-row>
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-phone-outline"></i>
                          <span class="title">Số điện thoại</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.phone_number}}</div>
                      </el-col>
                    </el-row>
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-s-flag"></i>
                          <span class="title">Quốc tịch</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.nationality}}</div>
                      </el-col>
                    </el-row>
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-male"></i>
                          <span class="title">Giới tính</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.gender}}</div>
                      </el-col>
                    </el-row>
                    <el-row class="row" :gutter="20">
                      <el-col :span="6">
                        <div class="grid-content">
                          <i class="el-icon-present"></i>
                          <span class="title">Ngày sinh</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div class="grid-content">{{user.birthday}}</div>
                      </el-col>
                    </el-row>
                    <el-tabs>
                      <el-tab-pane label="Thông tin công việc">
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-postcard"></i>
                              <span class="title">Mã nhân viên</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.employee_code}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-date"></i>
                              <span class="title">Ngày bắt đầu làm việc chính thức</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.official_start_day}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-medal"></i>
                              <span class="title">Vị trí</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.position.name}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-postcard"></i>
                              <span class="title">Mã số thuế</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.tax_code}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-s-custom"></i>
                              <span class="title">Loại nhân viên</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.employee_type.name}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-office-building"></i>
                              <span class="title">Chi nhánh - Mã chỗ ngồi</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.branch.name}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-connection"></i>
                              <span class="title">Phòng ban</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.group.name}}</div>
                          </el-col>
                        </el-row>
                      </el-tab-pane>
                      <el-tab-pane label="Thông tin khác">
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-postcard"></i>
                              <span class="title">CMND/Hộ chiếu</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.identity_card_passport.code}}</div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">
                              <i class="el-icon-date"></i>
                              <span class="title">Ngày cấp</span>
                            </div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">{{user.identity_card_passport.efective_date}}</div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">
                              <i class="el-icon-location-outline"></i>
                              <span class="title">Nơi cấp</span>
                            </div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">{{user.identity_card_passport.issued_by}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-message"></i>
                              <span class="title">Email cá nhân</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.personal_email}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-add-location"></i>
                              <span class="title">Nơi sinh</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.placebirth}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-place"></i>
                              <span class="title">Địa chỉ hiện tại</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.current_address}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-map-location"></i>
                              <span class="title">Địa chỉ thường chú</span>
                            </div>
                          </el-col>
                          <el-col :span="18">
                            <div class="grid-content">{{user.permanent_address}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-bicycle"></i>
                              <span class="title">Phương tiện đi lại</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.vehicle.type}}</div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">
                              <i class="el-icon-tickets"></i>
                              <span class="title">Dòng xe</span>
                            </div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">{{user.vehicle.brand}}</div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">
                              <i class="el-icon-s-unfold"></i>
                              <span class="title">Biển số xe</span>
                            </div>
                          </el-col>
                          <el-col :span="3">
                            <div class="grid-content">{{user.vehicle.license_plates}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-s-finance"></i>
                              <span class="title">Ngân hàng</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.bank.name}}</div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-tickets"></i>
                              <span class="title">Số tài khoản</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.bank.account_number}}</div>
                          </el-col>
                        </el-row>
                        <el-row class="row" :gutter="20">
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-school"></i>
                              <span class="title">Học vấn</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.education.school}}</div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">
                              <i class="el-icon-date"></i>
                              <span class="title">Năm tốt nghiệp</span>
                            </div>
                          </el-col>
                          <el-col :span="6">
                            <div class="grid-content">{{user.education.graduation_years}}</div>
                          </el-col>
                        </el-row>
                      </el-tab-pane>
                    </el-tabs>
                  </div>
                </div>
              </div>
              <el-row class="mt-3">
                <el-button type="primary">Đổi mật khẩu</el-button>
                <router-link :to="'/users/edit/' + user.id">
                  <el-button type="primary">Chỉnh sửa người dùng</el-button>
                </router-link>
              </el-row>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: "",
      error: {
        message: ""
      },
      noti: ""
    };
  },
  created() {
    this.getUsers();
  },
  methods: {
    getUsers() {
      axios
        .get("/users/" + this.$route.params.id)
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            console.log(response.data);
            this.user = response.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    }
  }
};
</script>

<style　lang="scss" scoped>
.content {
  border: 1px solid rgba(128, 128, 128, 0.3);
  border-radius: 5px 5px 0 0;
}

.content-header {
  padding: 15px;
  border-radius: 5px 5px 0 0;
  border-bottom: 1px solid rgba(128, 128, 128, 0.3);
  background: white;
}

.content-body {
  padding: 10px 5px;
}

.info {
  border: 1px solid rgba(128, 128, 128, 0.3);
  border-radius: 5px 5px 0 0;
  background: white;
  line-height: 1.2;

  .header {
    border-radius: 5px 5px 0 0;
    background: white;
    border-bottom: 1px solid rgba(128, 128, 128, 0.3);
  }

  .body {
    .row {
      border-top: 1px solid rgba(128, 128, 128, 0.3);
      // border-top: 1px solid rgba(128, 128, 128, 0.3);
      margin: 0 10px;
      padding: 15px 0;
    }
    .title {
      font-weight: bold;
      font-size: 1.2em;
      margin-left: 10px;
    }
  }
}
</style>