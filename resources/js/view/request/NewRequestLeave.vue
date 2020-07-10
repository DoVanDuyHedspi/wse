<template>
  <div class="new_request">
    <div class="text-right my-2">
      <router-link to="/request_leave">
        <el-button size="medium" type="primary">
          <i class="el-icon-s-order"></i> Danh sách
        </el-button>
      </router-link>
    </div>
    <div class="bg-white">
      <div class="header text-center">
        <h4 class="m-0">Yêu cầu nghỉ phép</h4>
      </div>
      <div class="body">
        <el-row :gutter="20">
          <el-col :span="24">
            <el-table :data="infoLeave" style="width: 100%" border>
              <el-table-column prop="name" label="Loại nghỉ phép" width="200"></el-table-column>
              <el-table-column prop="total" label="Tổng số ngày nghỉ" width="200"></el-table-column>
              <el-table-column prop="currentYear" label="Số ngày nghỉ năm nay" width="200"></el-table-column>
              <el-table-column prop="oldYear" label="Tồn năm trước"></el-table-column>
              <el-table-column prop="numberLeavedDays" label="Đã nghỉ"></el-table-column>
              <el-table-column prop="daysLeft" label="Còn lại"></el-table-column>
            </el-table>
          </el-col>
        </el-row>
        <div class="content py-3">
          <el-form
            :model="form"
            :rules="rules"
            ref="form-request"
            label-width="120px"
            class="form-request"
            label-position="top"
          >
            <el-row :gutter="20" class="mb-0">
              <el-col :span="12">
                <el-form-item label="Tên nhân viên">
                  <el-input :disabled="true" v-model="user.name" size="medium"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Mã số nhân viên">
                  <el-input :disabled="true" v-model="user.employee_code" size="medium"></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <!-- <el-row :gutter="20" class="mb-0">
              <el-col :span="12">
                <el-form-item label="Chi nhánh">
                  <el-select v-model="user.branch_id" class="w-100" :disabled="true" size="medium">
                    <el-option
                      v-for="(type,index) in infoCompany.branches"
                      :label="type.name"
                      :value="type.id"
                      :key="index"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Phòng ban">
                  <el-cascader
                    v-model="user.group_id"
                    :options="infoCompany.groups"
                    :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                    clearable
                    class="w-100"
                    :disabled="true"
                    size="medium"
                  ></el-cascader>
                </el-form-item>
              </el-col>
            </el-row>-->

            <el-form-item label="Loại nghỉ phép" prop="leave_type_id">
              <el-select
                v-model="form.leave_type_id"
                placeholder="Chọn loại nghỉ phép"
                style="width: 100%"
                size="medium"
              >
                <el-option
                  v-for="leave_type in leave_types"
                  :key="leave_type.id"
                  :label="leave_type.name"
                  :value="leave_type.id"
                ></el-option>
              </el-select>
            </el-form-item>
            <div>
              <el-row :gutter="20" class="mb-0">
                <el-col :span="12">
                  <el-form-item label="Ngày bắt đầu nghỉ" prop="begin_leave_date">
                    <el-date-picker
                      type="date"
                      format="dd-MM-yyyy"
                      value-format="dd-MM-yyyy"
                      v-model="form.begin_leave_date"
                      style="width: 100%"
                      size="medium"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Ngày kết thúc nghỉ" prop="end_leave_date">
                    <el-date-picker
                      type="date"
                      format="dd-MM-yyyy"
                      value-format="dd-MM-yyyy"
                      v-model="form.end_leave_date"
                      style="width: 100%"
                      @blur="validateLeaveEnd()"
                      size="medium"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>

            <el-form-item label="Lý do xin nghỉ" prop="reason">
              <el-input type="textarea" v-model="form.reason"></el-input>
            </el-form-item>
          </el-form>
          <div class="text-center">
            <el-button
              type="primary"
              @click="createRequest('form-request')"
              style="padding: 15px 100px 15px 100px;"
            >Thêm mới</el-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import moment from "moment";
export default {
  data() {
    return {
      error: {
        message: ""
      },
      infoLeave: [],
      leave_types: [],
      form: {
        user_code: "",
        leave_type_id: "",
        begin_leave_date: "",
        end_leave_date: "",
        reason: ""
      },
      rules: {
        reason: { required: true, message: "Hãy nhập lý do", trigger: "blur" },
        leave_type_id: {
          required: true,
          message: "Hãy chọn loại nghỉ phép",
          trigger: "blur"
        },
        begin_leave_date: {
          required: true,
          message: "Hãy chọn ngày bắt đầu nghỉ",
          trigger: "blur"
        },
        end_leave_date: {
          required: true,
          message: "Hãy chọn ngày kết thúc nghỉ",
          trigger: "blur"
        }
      }
    };
  },
  created() {
    this.getUser();
    this.getLeaveType();
    this.getInfoLeave();
    if (Object.keys(this.$route.query).length !== 0) {
      if (this.$route.query.date) {
        this.form.begin_leave_date = this.$route.query.date;
        this.form.end_leave_date = this.$route.query.date;
      }
    }
  },
  computed: mapState({
    user: state => state.user,
    infoCompany: state => state.infoCompany
  }),
  methods: {
    getLeaveType: async function() {
      await axios.get("/api/leave_type").then(response => {
        if ((response.status = true)) {
          this.leave_types = response.data;
        }
      });
    },
    getUser() {
      this.$store.dispatch("fetchOne", this.$root.user.id).then(
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
    getInfoLeave: async function() {
      await axios
        .post("/api/form_leave/manage/getInfo", {
          user_code: this.$root.user.employee_code
        })
        .then(response => {
          this.infoLeave = response.data;
        });
    },
    validateLeaveEnd() {
      this.validateDate();
    },
    validateDate() {
      if (this.form.begin_leave_date && this.form.end_leave_date) {
        let date_begin = moment(
          this.form.begin_leave_date,
          "DD-MM-YYYY"
        ).format("YYYY:MM:dddd");
        let date_end = moment(this.form.end_leave_date, "DD-MM-YYYY").format(
          "YYYY:MM:dddd"
        );
        if (date_begin > date_end) {
          this.$alert(
            "Ngày kết thúc nghỉ không thể trước ngày bắt đầu nghỉ",
            "Ngày nghỉ không hợp lệ",
            {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                this.form.end_leave_date = "";
              }
            }
          );
          return false;
        }
      }
      return true;
    },
    createRequest(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.form.user_code = this.user.employee_code;
          axios.post("/api/form_leaves", this.form).then(response => {
            if (response.data.status === false) {
              this.$notify.error({
                title: "Thất bại",
                message: response.data.message,
                position: "bottom-right"
              });
            } else {
              this.$notify({
                title: "Hoàn thành",
                message: "Gửi yêu cầu thành công",
                type: "success",
                position: "bottom-right"
              });
              this.$router.push("/request_leave");
            }
          });
        }
      });
    }
  }
};
</script>

<style lang="scss">
.new_request {
  width: 86%;
  margin: auto;
  .header {
    color: #31708f;
    background-color: #d9edf7;
    border: 1px solid #bce8f1;
    padding: 15px;
    border-radius: 5px 5px 0 0;
  }
  .body {
    border: 1px solid #bce8f1;
    .content {
      width: 60%;
      margin: auto;
      .form-request {
        label {
          font-size: 16px;
          font-weight: bolder;
          padding: 0px;
          color: black;
          margin: 0;
        }
      }
    }
  }
}
</style>

<style lang="scss ">
</style>