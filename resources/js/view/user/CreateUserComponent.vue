<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Thành viên</el-breadcrumb-item>
        <el-breadcrumb-item>Thêm mới</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="my-3 text-center">
      <h3>Thành viên mới</h3>
    </div>
    <el-alert :title="error.message" v-if="error.message" type="error"></el-alert>
    <div class="container">
      <div
        class="bg-white p-3"
        style=" box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
      >
        <el-steps :active="step">
          <el-step title="Tài khoản" icon="el-icon-edit"></el-step>
          <el-step title="Cá nhân" icon="el-icon-user-solid"></el-step>
          <el-step title="Ảnh" icon="el-icon-picture"></el-step>
          <el-step title="Công việc" icon="el-icon-s-cooperation"></el-step>
        </el-steps>
        <div v-if="step == 1" class="mt-5">
          <el-row :gutter="50">
            <el-col :span="10">
              <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label">
                  Email
                  <span style="color: red">(*)</span>
                </el-col>
                <el-col :span="24">
                  <el-input
                    type="email"
                    auto-complete="off"
                    v-model="form.email"
                    class="input"
                    suffix-icon="el-icon-message"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label">
                  Mật khẩu
                  <span style="color: red">(*)</span>
                </el-col>
                <el-col :span="24">
                  <el-input v-model="form.password" class="input" suffix-icon="el-icon-key"></el-input>
                </el-col>
              </el-row>
              <!-- <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label">
                  Xác nhận mật khẩu
                  <span style="color: red">(*)</span>
                </el-col>
                <el-col :span="24">
                  <el-input
                    v-model="form.confirm_password"
                    class="input"
                    suffix-icon="el-icon-key"
                  ></el-input>
                </el-col>
              </el-row>-->
            </el-col>
            <el-col :span="14">
              <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label">
                  Vai trò trong hệ thống
                  <span style="color: red">(*)</span>
                </el-col>
                <el-col :span="24">
                  <el-select
                    v-model="form.roles"
                    multiple
                    placeholder="Chọn vai trò"
                    style="width: 100%"
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
              <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label">Quyền trong hệ thống</el-col>
                <el-col :span="24">
                  <el-checkbox-group v-model="form.permissions">
                    <el-checkbox
                      v-for="permission in infoCompany.permissions"
                      :label="permission.id"
                      :key="permission.id"
                      border
                      size="medium"
                      class="m-2"
                    >{{permission.name}}</el-checkbox>
                  </el-checkbox-group>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
        </div>
        <div v-if="step == 2" class="mt-5">
          <el-row :gutter="20">
            <el-col :span="12">
              <el-col :span="24" class="label text-left p-0" style="color: blue">Thông tin cơ bản</el-col>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">
                  Tên
                  <span style="color: red">(*)</span>
                </el-col>
                <el-col :span="12">
                  <el-input v-model="form.name" class="input" suffix-icon="el-icon-user-solid"></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Giới tính</el-col>
                <el-col :span="12" style="line-height: 2.5">
                  <el-radio-group v-model="form.gender">
                    <el-radio label="Nam"></el-radio>
                    <el-radio label="Nữ"></el-radio>
                  </el-radio-group>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Ngày sinh</el-col>
                <el-col :span="12">
                  <el-date-picker
                    type="date"
                    placeholder="Chọn ngày sinh"
                    v-model="form.birthday"
                    style="width: 100%;"
                    format="dd-MM-yyyy"
                    value-format="dd-MM-yyyy"
                  ></el-date-picker>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Quốc tịch</el-col>
                <el-col :span="12">
                  <el-input v-model="form.nationality" class="input" suffix-icon="el-icon-s-flag"></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Email cá nhân</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.personal_email"
                    class="input"
                    suffix-icon="el-icon-message"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Số điện thoại</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.phone_number"
                    class="input"
                    suffix-icon="el-icon-phone-outline"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Nơi sinh</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.placebirth"
                    class="input"
                    suffix-icon="el-icon-add-location"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Địa chỉ thường chú</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.permanent_address"
                    class="input"
                    suffix-icon="el-icon-map-location"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Địa chỉ hiện tại</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.current_address"
                    class="input"
                    suffix-icon="el-icon-place"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="24" class="label text-left" style="color: blue">Trình độ học vấn</el-col>
                <el-col :span="8" class="label">Trường</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.education.school"
                    class="input"
                    suffix-icon="el-icon-school"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Chuyên ngành</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.education.specialized"
                    class="input"
                    suffix-icon="el-icon-reading"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Năm tốt nghiệp</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.education.graduation_years"
                    class="input"
                    suffix-icon="el-icon-date"
                  ></el-input>
                </el-col>
              </el-row>
            </el-col>
            <el-col :span="12">
              <el-row :gutter="15" class="mb-3">
                <el-col :span="24" class="label text-left" style="color: blue">Phương tiện đi lại</el-col>
                <el-col :span="8" class="label">Loại xe</el-col>
                <el-col :span="12">
                  <el-select v-model="form.vehicle.type" class="w-100">
                    <el-option label="Xe máy" value="Xe máy"></el-option>
                    <el-option label="Ô tô" value="Ô tô"></el-option>
                    <el-option label="Xe buýt" value="Xe buýt"></el-option>
                    <el-option label="Đi bộ" value="Đi bộ"></el-option>
                  </el-select>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="8" class="label">Dòng xe</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.vehicle.brand"
                    class="input"
                    suffix-icon="el-icon-tickets"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Biển số xe</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.vehicle.license_plates"
                    class="input"
                    suffix-icon="el-icon-s-unfold"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="15" class="mb-3">
                <el-col :span="24" class="label text-left" style="color: blue">Tài khoản ngân hàng</el-col>
                <el-col :span="8" class="label">Loại thẻ</el-col>
                <el-col :span="12">
                  <el-select v-model="form.bank.type" class="w-100">
                    <el-option label="Thanh toán" value="Thanh toán"></el-option>
                    <el-option label="Doanh nghiệp" value="Doanh nghiệp"></el-option>
                    <el-option label="Tiết kiệm" value="Tiết kiệm"></el-option>
                    <el-option label="Tín dụng" value="Tín dụng"></el-option>
                    <el-option label="Ký gửi" value="Ký gửi"></el-option>
                  </el-select>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="8" class="label">Ngân hàng</el-col>
                <el-col :span="12">
                  <el-input v-model="form.bank.name" class="input" suffix-icon="el-icon-s-finance"></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="15" class="mb-3">
                <el-col :span="8" class="label">Số tài khoản</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.bank.account_number"
                    class="input"
                    suffix-icon="el-icon-tickets"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="8" class="label">Mã số thuế</el-col>
                <el-col :span="12">
                  <el-input v-model="form.tax_code" class="input" suffix-icon="el-icon-date"></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="15" class="mb-3">
                <el-col :span="24" class="label text-left" style="color: blue">CMND/Hộ chiếu</el-col>
                <el-col :span="8" class="label">Loại</el-col>
                <el-col :span="12">
                  <el-select v-model="form.identity_card_passport.type" class="w-100">
                    <el-option label="CMND" value="CMND"></el-option>
                    <el-option label="Hộ chiếu" value="Hộ chiếu"></el-option>
                  </el-select>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="8" class="label">Mã số</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.identity_card_passport.code"
                    class="input"
                    suffix-icon="el-icon-postcard"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mb-3">
                <el-col :span="8" class="label">Nơi cấp</el-col>
                <el-col :span="12">
                  <el-input
                    v-model="form.identity_card_passport.issued_by"
                    class="input"
                    suffix-icon="eel-icon-location-outline"
                  ></el-input>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="8" class="label">Ngày cấp</el-col>
                <el-col :span="12">
                  <el-date-picker
                    type="date"
                    placeholder="Ngày cấp"
                    v-model="form.identity_card_passport.efective_date"
                    style="width: 100%;"
                    format="dd-MM-yyyy"
                    value-format="dd-MM-yyyy"
                  ></el-date-picker>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
        </div>
        <div v-if="step == 3" class="mt-5">
          <div class="infor mb-4">
            <p>Lưu ý ảnh này sẽ được sử dụng để nhận diện khuôn mặt nhân viên trong việc chấm công hằng ngày.</p>
            <p>Hãy sử dụng ảnh to và rõ khuôn mặt.</p>
          </div>
          <div>
            <el-row :gutter="20">
              <el-col :span="12" class="text-center">
                <el-upload
                  class="upload-demo"
                  drag
                  action
                  :on-change="handlePreviewAvatar"
                  :on-remove="handleRemove"
                  :auto-upload="false"
                >
                  <i class="el-icon-upload"></i>
                  <div class="el-upload__text">
                    Thả ảnh vào đây hoặc
                    <em>nhấn vào đây để upload</em>
                  </div>
                  <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 1Mb</div>
                </el-upload>
              </el-col>
              <el-col :span="12" class="text-center">
                <img ref="image" id="image" v-if="imageUrl" :src="imageUrl" class="avatar" />
              </el-col>
            </el-row>
          </div>
        </div>
        <div v-if="step == 4" class="mt-5">
          <el-row :gutter="20" class="mb-3">
            <el-col :span="6" :offset="4" class="label">
              Loại nhân viên
              <span style="color: red">(*)</span>
            </el-col>
            <el-col :span="10">
              <el-select v-model="form.employee_type_id" class="w-100">
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
            <el-col :span="6" :offset="4" class="label">
              Vị trí
              <span style="color: red">(*)</span>
            </el-col>
            <el-col :span="10">
              <el-select v-model="form.position_id" class="w-100">
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
            <el-col :span="6" :offset="4" class="label">
              Phòng ban
              <span style="color: red">(*)</span>
            </el-col>
            <el-col :span="10">
              <el-cascader
                v-model="form.group_id"
                :options="infoCompany.groups"
                :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                :change="handleGroupChange()"
                clearable
                class="w-100"
              ></el-cascader>
            </el-col>
          </el-row>
          <el-row :gutter="20" class="mb-3">
            <el-col :span="6" :offset="4" class="label">
              Chi nhánh
              <span style="color: red">(*)</span>
            </el-col>
            <el-col :span="10">
              <el-select v-model="form.branch_id" class="w-100">
                <el-option
                  v-for="(type,index) in infoCompany.branches"
                  :label="type.name"
                  :value="type.id"
                  :key="index"
                ></el-option>
              </el-select>
            </el-col>
          </el-row>
          <el-row :gutter="20" class="mb-3">
            <el-col :span="6" :offset="4" class="label">
              Ngày bắt đầu làm việc
              <span style="color: red">(*)</span>
            </el-col>
            <el-col :span="10">
              <el-date-picker
                type="date"
                placeholder="Chọn ngày bắt đầu làm việc"
                v-model="form.official_start_day"
                style="width: 100%;"
                format="dd-MM-yyyy"
                value-format="dd-MM-yyyy"
              ></el-date-picker>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
    <div class="my-4 text-center">
      <el-button-group>
        <el-button
          type="primary"
          icon="el-icon-arrow-left"
          :disabled="step == 1"
          @click="handleBack"
        >Quay lại</el-button>
        <el-button type="primary" @click="handleNext">
          Tiếp tục
          <i class="el-icon-arrow-right el-icon-right"></i>
        </el-button>
      </el-button-group>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      step: 1,
      error: {
        message: ""
      },
      imageUrl: "",
      imageFile: "",
      form: {
        name: "",
        roles: [],
        permissions: [],
        email: "",
        password: "",
        confirm_password: "",
        phone_number: "",
        nationality: "",
        gender: "",
        birthday: "",
        official_start_day: "",
        personal_email: "",
        birthplace: "",
        current_address: "",
        permanent_address: "",
        tax_code: "",
        roles: [],
        permissions: [],
        position_id: "",
        branch_id: "",
        employee_type_id: "",
        group_id: [],
        vehicle: {
          type: "",
          brand: "",
          license_plates: ""
        },
        bank: {
          type: "",
          account_number: "",
          name: ""
        },
        education: {
          school: "",
          specialized: "",
          graduation_years: ""
        },
        identity_card_passport: {
          type: "",
          code: "",
          efective_date: "",
          expiry_date: ""
        }
      },
      infoCompany: {
        branches: [],
        positions: [],
        groups: [],
        employee_types: [],
        roles: [],
        permissions: []
      }
    };
  },
  created() {
    this.getInfoOfCompany();
  },
  methods: {
    handleGroupChange() {
      if (Array.isArray(this.form.group_id)) {
        console.log(this.form.group_id)
        this.form.group_id = this.form.group_id[this.form.group_id.length - 1];
        console.log(this.form.group_id)
      }
    },
    handleNext() {
      if (this.step < 4) {
        this.step = this.step + 1;
      } else {
        this.createUser();
      }
    },
    handleBack() {
      if (this.step > 1) {
        this.step = this.step - 1;
      }
    },
    handlePreviewAvatar(file) {
      this.imageUrl = URL.createObjectURL(file.raw);
      this.imageFile = file.raw;
    },
    handleRemove(file) {
      this.imageFile = "";
    },
    getInfoOfCompany() {
      axios
        .get("/users/create")
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            console.log(response.data);
            this.infoCompany = response.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    createUser() {
      let rawData = this.form;
      rawData = JSON.stringify(rawData);
      let formData = new FormData();
      formData.append("new_user", rawData);
      formData.append("image", this.imageFile);
      axios
        .post("/users", formData, {
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
            this.$notify({
              title: "Hoàn thành",
              message: "Thêm nhân viên mới thành công",
              type: "success",
              position: "bottom-right"
            });
          }
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.label {
  line-height: 2.5;
  // text-align: right;
  font-size: 16px;
  font-weight: bold;
}

.infor {
  min-height: 75px;
  background: #ecf8ff;
  border-left: 5px solid #50bfff;
  padding: 8px 16px;
  border-radius: 4px;

  p {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
  }
}

.avatar {
  width: 300px;
  height: 300px;
  display: block;
}
</style>