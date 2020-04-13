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
          <b>Chỉnh sửa thông tin cá nhân</b>
        </div>
        <div class="content-body">
          <el-row :gutter="20">
            <el-col :span="6">
              <div class="grid-content">
                <el-card :body-style="{ padding: '0px' }">
                  <img :src="user.avatar" class="image" style="max-width: 250px; margin: auto; max-height: 250px;" />
                  <div class="px-3">
                    <div class="bottom clearfix text-center">
                      <div class="upload-btn-wrapper" v-if="user.can_update_adv">
                        <button class="btn">Chọn ảnh</button>
                        <input type="file" name="myfile" ref="file" @change="handleImageUpload()" />
                      </div>
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
                  <div class="mt-3">
                    <el-form :model="user" label-width="120px">
                      <el-row :gutter="20" class="mb-3">
                        <el-col :span="6" class="label">Tên</el-col>
                        <el-col :span="12">
                          <el-input
                            v-model="user.name"
                            :disabled="!user.can_update_adv"
                            class="input"
                            suffix-icon="el-icon-user-solid"
                          ></el-input>
                        </el-col>
                      </el-row>
                      <el-row :gutter="20" class="mb-3">
                        <el-col :span="6" class="label">Email</el-col>
                        <el-col :span="12">
                          <el-input
                            v-model="user.email"
                            :disabled="!user.can_update_adv"
                            class="input"
                            suffix-icon="el-icon-message"
                          ></el-input>
                        </el-col>
                      </el-row>
                      <el-row :gutter="20" class="mb-3">
                        <el-col :span="6" class="label">Giới tính</el-col>
                        <el-col :span="12" style="line-height: 2.5">
                          <el-radio-group v-model="user.gender" :disabled="!user.can_update_adv">
                            <el-radio label="Nam"></el-radio>
                            <el-radio label="Nữ"></el-radio>
                          </el-radio-group>
                        </el-col>
                      </el-row>
                      <el-row :gutter="20" class="mb-3">
                        <el-col :span="6" class="label">Quốc tịch</el-col>
                        <el-col :span="12">
                          <el-input
                            v-model="user.nationality"
                            class="input"
                            suffix-icon="el-icon-s-flag"
                          ></el-input>
                        </el-col>
                      </el-row>
                      <el-row :gutter="20" class="mb-3">
                        <el-col :span="6" class="label">Ngày sinh</el-col>
                        <el-col :span="12">
                          <el-date-picker
                            type="date"
                            placeholder="Chọn ngày sinh"
                            v-model="user.birthday"
                            style="width: 100%;"
                            format="dd-MM-yyyy"
                            value-format="dd-MM-yyyy"
                          ></el-date-picker>
                        </el-col>
                      </el-row>
                      <el-tabs type="card" class="pt-4">
                        <el-tab-pane label="Thông tin công việc">
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Mã nhân viên</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.employee_code"
                                class="input"
                                :disabled="!user.can_update_adv"
                                suffix-icon="el-icon-postcard"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Loại nhân viên</el-col>
                            <el-col :span="12">
                              <el-select
                                v-model="user.employee_type_id"
                                class="w-100"
                                :disabled="!user.can_update_adv"
                              >
                                <el-option
                                  v-for="(type,index) in infoCompany.employee_types"
                                  :label="type.name"
                                  :value="type.id"
                                  :key="index"
                                ></el-option>
                              </el-select>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Vị trí</el-col>
                            <el-col :span="12">
                              <el-select
                                v-model="user.position_id"
                                class="w-100"
                                :disabled="!user.can_update_adv"
                              >
                                <el-option
                                  v-for="(type,index) in infoCompany.positions"
                                  :label="type.name"
                                  :value="type.id"
                                  :key="index"
                                ></el-option>
                              </el-select>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Phòng ban</el-col>
                            <el-col :span="12">
                              <el-cascader
                                v-model="user.group_id"
                                :options="infoCompany.groups"
                                :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                                :change="handleGroupChange()"
                                clearable
                                class="w-100"
                                :disabled="!user.can_update_adv"
                              ></el-cascader>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Ngày bắt đầu làm việc</el-col>
                            <el-col :span="12">
                              <el-date-picker
                                type="date"
                                placeholder="Chọn ngày bắt đầu làm việc"
                                v-model="user.official_start_day"
                                style="width: 100%;"
                                format="dd-MM-yyyy"
                                value-format="dd-MM-yyyy"
                                :disabled="!user.can_update_adv"
                              ></el-date-picker>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Chi nhánh</el-col>
                            <el-col :span="12">
                              <el-select
                                v-model="user.branch_id"
                                class="w-100"
                                :disabled="!user.can_update_adv"
                              >
                                <el-option
                                  v-for="(type,index) in infoCompany.branches"
                                  :label="type.name"
                                  :value="type.id"
                                  :key="index"
                                ></el-option>
                              </el-select>
                            </el-col>
                          </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="Vai trò và quyền">
                          <el-row :gutter="20" class="mb-3 mx-0">
                            <el-col :span="24" class="label text-left">
                              Vai trò trong hệ thống
                              <span style="color: red">(*)</span>
                              <el-alert
                                class="mb-2"
                                title="Khi bạn chọn một vai trò, những quyền thuộc vài vai trò ý sẽ không được tắt"
                                type="info"
                              ></el-alert>
                            </el-col>
                            <el-col :span="24">
                              <el-select
                                v-model="roles"
                                multiple
                                placeholder="Chọn vai trò"
                                style="width: 100%"
                                :change="handleChangeRole"
                                v-if="componentLoaded"
                              >
                                <el-option
                                  v-for="role in infoCompany.roles"
                                  :key="role.id"
                                  :label="role.name"
                                  :value="role.id"
                                ></el-option>
                              </el-select>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3 mx-0">
                            <el-col :span="24" class="label text-left">Quyền trong hệ thống</el-col>
                            <el-col :span="24">
                              <el-checkbox-group v-model="permissions">
                                <el-checkbox
                                  v-for="permission in infoCompany.permissions"
                                  :label="permission.id"
                                  :key="permission.id"
                                  :disabled="role_has_permissions.includes(permission.id)"
                                  border
                                  size="medium"
                                  class="m-2"
                                >{{permission.name}}</el-checkbox>
                              </el-checkbox-group>
                            </el-col>
                          </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="Thông tin liên hệ">
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Email cá nhân</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.personal_email"
                                class="input"
                                suffix-icon="el-icon-message"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Số điện thoại</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.phone_number"
                                class="input"
                                suffix-icon="el-icon-phone-outline"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Nơi sinh</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.placebirth"
                                class="input"
                                suffix-icon="el-icon-add-location"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Địa chỉ thường chú</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.permanent_address"
                                class="input"
                                suffix-icon="el-icon-map-location"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Địa chỉ hiện tại</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.current_address"
                                class="input"
                                suffix-icon="el-icon-place"
                              ></el-input>
                            </el-col>
                          </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="Thông tin giáo dục">
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Trường</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.education.school"
                                class="input"
                                suffix-icon="el-icon-school"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Chuyên ngành</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.education.specialized"
                                class="input"
                                suffix-icon="el-icon-reading"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Năm tốt nghiệp</el-col>
                            <el-col :span="12">
                              <el-input
                                v-model="user.education.graduation_years"
                                class="input"
                                suffix-icon="el-icon-date"
                              ></el-input>
                            </el-col>
                          </el-row>
                        </el-tab-pane>
                        <el-tab-pane label="Thông tin khác" class="px-5">
                          <el-row :gutter="15" class="mb-3">
                            <el-col :span="24" class="label text-left">Phương tiện đi lại</el-col>
                            <el-col :span="6" class="label">Loại xe</el-col>
                            <el-col :span="6">
                              <el-select v-model="user.vehicle.type" class="w-100">
                                <el-option label="Xe máy" value="Xe máy"></el-option>
                                <el-option label="Ô tô" value="Ô tô"></el-option>
                                <el-option label="Xe buýt" value="Xe buýt"></el-option>
                                <el-option label="Đi bộ" value="Đi bộ"></el-option>
                              </el-select>
                            </el-col>
                            <el-col :span="6" class="label">Dòng xe</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.vehicle.brand"
                                class="input"
                                suffix-icon="el-icon-tickets"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Biển số xe</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.vehicle.license_plates"
                                class="input"
                                suffix-icon="el-icon-s-unfold"
                              ></el-input>
                            </el-col>
                          </el-row>

                          <el-row :gutter="15" class="mb-3">
                            <el-col :span="24" class="label text-left">Tài khoản ngân hàng</el-col>
                            <el-col :span="6" class="label">Loại thẻ</el-col>
                            <el-col :span="6">
                              <el-select v-model="user.bank.type" class="w-100">
                                <el-option label="Thanh toán" value="Thanh toán"></el-option>
                                <el-option label="Doanh nghiệp" value="Doanh nghiệp"></el-option>
                                <el-option label="Tiết kiệm" value="Tiết kiệm"></el-option>
                                <el-option label="Tín dụng" value="Tín dụng"></el-option>
                                <el-option label="Ký gửi" value="Ký gửi"></el-option>
                              </el-select>
                            </el-col>
                            <el-col :span="6" class="label">Ngân hàng</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.bank.name"
                                class="input"
                                suffix-icon="el-icon-s-finance"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="15" class="mb-3">
                            <el-col :span="6" class="label">Số tài khoản</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.bank.account_number"
                                class="input"
                                suffix-icon="el-icon-tickets"
                              ></el-input>
                            </el-col>
                            <el-col :span="6" class="label">Mã số thuế</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.tax_code"
                                class="input"
                                suffix-icon="el-icon-date"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="15" class="mb-3">
                            <el-col :span="24" class="label text-left">CMND/Hộ chiếu</el-col>
                            <el-col :span="6" class="label">Loại</el-col>
                            <el-col :span="6">
                              <el-select v-model="user.identity_card_passport.type" class="w-100">
                                <el-option label="CMND" value="CMND"></el-option>
                                <el-option label="Hộ chiếu" value="Hộ chiếu"></el-option>
                              </el-select>
                            </el-col>
                            <el-col :span="6" class="label">Mã số</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.identity_card_passport.code"
                                class="input"
                                suffix-icon="el-icon-postcard"
                              ></el-input>
                            </el-col>
                          </el-row>
                          <el-row :gutter="20" class="mb-3">
                            <el-col :span="6" class="label">Nơi cấp</el-col>
                            <el-col :span="6">
                              <el-input
                                v-model="user.identity_card_passport.issued_by"
                                class="input"
                                suffix-icon="eel-icon-location-outline"
                              ></el-input>
                            </el-col>
                            <el-col :span="6" class="label">Ngày cấp</el-col>
                            <el-col :span="6">
                              <el-date-picker
                                type="date"
                                placeholder="Ngày cấp"
                                v-model="user.identity_card_passport.efective_date"
                                style="width: 100%;"
                                format="dd-MM-yyyy"
                                value-format="dd-MM-yyyy"
                              ></el-date-picker>
                            </el-col>
                          </el-row>
                        </el-tab-pane>
                      </el-tabs>
                    </el-form>
                  </div>
                </div>
              </div>
              <el-row class="mt-3">
                <el-button type="primary" @click="updateUser">Cập nhật</el-button>
              </el-row>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      role_has_permissions: [],
      roles: [],
      permissions: [],
      componentLoaded: false,
      error: {
        message: ""
      },
      noti: "",
      imageFile: "",
      rules: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        email: [{ required: true, message: "Hãy nhập email", trigger: "blur" }]
      },
      flag: false
    };
  },
  created() {
    this.getUser();
  },
  computed: mapState({
    user: state => state.user,
    infoCompany: state => state.infoCompany,
    handleChangeRole(state) {
      let user_roles = [];
      state.user.roles.map(function(role) {
        user_roles.push(role.id);
      });
      let user_pers = [];
      state.user.permissions.map(function(per) {
        user_pers.push(per.id);
      });
      if (this.permissions.length != 0) {
        this.flag = true;
      }
      if (!this.flag) {
        this.permissions = user_pers;
      }
      let list_roles = this.roles.length == 0 ? user_roles : this.roles;
      let new_roles = [];
      let role_has_permissions = [];
      let before_role_has_permissions = this.role_has_permissions;
      state.infoCompany.roles.map(function(role) {
        let n = list_roles.includes(role.id);
        if (n) {
          let permissions = role.permissions;
          new_roles.push(role.id);
          permissions.map(function(per) {
            if (role_has_permissions.indexOf(per.id) === -1) {
              role_has_permissions.push(per.id);
            }
          });
        }
      });
      this.roles = new_roles;
      this.role_has_permissions = role_has_permissions;
      this.permissions = $(this.permissions)
        .not(before_role_has_permissions)
        .get();
      $.merge(this.permissions, this.role_has_permissions);
      let unique_per = [];
      this.permissions.map(function(perId) {
        if (!unique_per.includes(perId)) {
          unique_per.push(perId);
        }
      });
      this.permissions = unique_per;
    }
  }),
  methods: {
    handleGroupChange() {
      if (Array.isArray(this.user.group_id)) {
        this.user.group_id = this.user.group_id[this.user.group_id.length - 1];
      }
    },
    getUser() {
      this.$store.dispatch("fetchOne", this.$route.params.id).then(
        response => {
          this.componentLoaded = true;
          if (response.data.status === false) {
            this.error.message = response.data.message;
          }
        },
        error => {
          console.log(error);
        }
      );
    },
    handleImageUpload() {
      this.imageFile = this.$refs.file.files[0];
      let formData = new FormData();
      formData.append("image", this.imageFile);
      formData.append("_method", "PATCH");
      axios
        .post("/api/users/" + this.user.id, formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
            this.$notify.error({
              title: "Thất bại",
              message: response.data.message,
              position: "bottom-right"
            });
          } else {
            this.user.avatar = response.data.avatar;
            this.$notify({
              title: "Hoàn thành",
              message: "Cập nhật thông tin nhân viên thành công",
              type: "success",
              position: "bottom-right"
            });
          }
        });
    },
    updateUser(formName) {
      this.user.permissions = this.permissions.sort();
      this.user.roles = this.roles.sort();
      this.user.roles_has_pers = this.role_has_permissions.sort();
      axios.put("/api/users/" + this.user.id, this.user).then(response => {
        if (response.data.status === false) {
          this.error.message = response.data.message;
          this.$notify.error({
            title: "Thất bại",
            message: response.data.message,
            position: "bottom-right"
          });
        } else {
          this.$notify({
            title: "Hoàn thành",
            message: "Cập nhật thông tin nhân viên thành công",
            type: "success",
            position: "bottom-right"
          });
        }
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
}

.el-form-item__label {
  font-weight: bold !important;
  font-size: 16px;
}

.label {
  line-height: 2.5;
  text-align: right;
  font-size: 16px;
  font-weight: bold;
}

.input .el-input-group__prepend {
  background-color: #fff;
}

.upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.btn {
  border: 2px solid gray;
  color: gray;
  background-color: white;
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: bold;
}

.upload-btn-wrapper input[type="file"] {
  font-size: 100px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  height: 50px;
}
</style>