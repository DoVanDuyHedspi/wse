<template>
  <div class="new_request bg-white">
    <div>
      <div class="header text-center">
        <h4 class="m-0">Yêu cầu OT, REMOTE</h4>
      </div>
      <div class="body">
        <div class="content p-3">
          <el-form
            :model="form"
            :rules="rules"
            ref="form-request"
            label-width="120px"
            class="form-request"
            label-position="top"
          >
            <el-form-item label="Tên nhân viên">
              <el-input :disabled="true" v-model="user.name"></el-input>
            </el-form-item>
            <el-form-item label="Mã số nhân viên">
              <el-input :disabled="true" v-model="user.employee_code"></el-input>
            </el-form-item>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="Chi nhánh">
                  <el-select v-model="user.branch_id" class="w-100" :disabled="true">
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
                  ></el-cascader>
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item label="Hình thức xin phép" prop="type">
              <el-select
                v-model="form.type"
                @change="hanldeChangeType()"
                placeholder="Chọn hình thức"
                style="width: 100%"
              >
                <el-option label="Làm thêm giờ" value="OT"></el-option>
                <el-option label="Làm remote" value="RM"></el-option>
              </el-select>
            </el-form-item>
            <div>
              <el-row :gutter="20">
                <el-col :span="12">
                  <el-form-item label="Thời gian bắt đầu " prop="work_time_begin">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.work_time_begin"
                      style="width: 100%"
                      @blur="validateWorkBegin()"
                      :disabled="!form.type"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Thời gian kết thúc " prop="work_time_end">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.work_time_end"
                      style="width: 100%"
                      @blur="validateWorkEnd()"
                      :disabled="!form.type"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>

            <el-form-item label="Lý do" prop="reason">
              <el-input type="textarea" v-model="form.reason"></el-input>
            </el-form-item>
          </el-form>
          <div class="text-center">
            <el-button
              type="primary"
              @click="createRequest('form-request')"
              style="padding: 15px 100px 15px 100px;"
            >Lưu</el-button>
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
      form: {
        user_code: "",
        type: "",
        work_time_begin: "",
        work_time_end: "",
        reason: "",
        range_time: ""
      },
      rules: {
        reason: { required: true, message: "Hãy nhập lý do", trigger: "blur" },
        type: {
          required: true,
          message: "Hãy nhập hình thức",
          trigger: "blur"
        },
        work_time_begin: {
          required: true,
          message: "Hãy nhập thời gian",
          trigger: "blur"
        },
        work_time_end: {
          required: true,
          message: "Hãy nhập thời gian",
          trigger: "blur"
        }
      }
    };
  },
  created() {
    this.getUser();
    if (Object.keys(this.$route.query).length !== 0) {
      if (this.$route.query.type) {
        this.form.type = this.$route.query.type;
        if (this.form.type == "RM" && this.$route.query.date) {
          this.form.work_time_begin =
            this.$route.query.date +
            " " +
            this.specified_working_time.morning_begin;
          this.form.work_time_end =
            this.$route.query.date +
            " " +
            this.specified_working_time.afternoon_end;
        } else if (this.form.type == "OT" && this.$route.query.date) {
          this.form.work_time_begin =
            this.$route.query.date +
            " " +
            this.specified_working_time.afternoon_end;
        }
      }
    }
  },
  computed: mapState({
    user: state => state.user,
    infoCompany: state => state.infoCompany,
    specified_working_time: state => state.timekeeping
  }),
  methods: {
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
    getWorkingTimeInfo() {
      axios.get("/api/specifiedWorkingTime").then(response => {
        this.specified_working_time = response.data;
      });
    },
    hanldeChangeType() {
      this.form.work_time_begin = "";
      this.form.work_time_end = "";
      this.form.range_time = "";
    },
    validateWorkBegin() {
      if (this.validateDate()) {
        let work_time_begin = moment(
          this.form.work_time_begin,
          "DD-MM-YYYY HH:mm"
        ).format("HH:mm");
        if (this.form.type == "OT") {
          if (
            this.compareTime(
              this.specified_working_time.afternoon_end,
              work_time_begin
            )
          ) {
            this.$alert(
              "Thời gian làm thêm không được bắt đầu trong thời gian làm việc chính thức",
              "Thời gian không hợp lệ",
              {
                confirmButtonText: "OK",
                type: "warning",
                center: true,
                callback: action => {
                  this.form.work_time_begin = "";
                  this.form.range_time = "";
                }
              }
            );
          }
        } else {
          if (
            this.compareTime(
              this.specified_working_time.morning_begin,
              work_time_begin
            )
          ) {
            this.$alert(
              "Thời gian làm sớm hơn thời gian bắt đầu làm việc chính thức",
              "Thời gian không hợp lệ",
              {
                confirmButtonText: "OK",
                type: "warning",
                center: true,
                callback: action => {
                  this.form.work_time_begin = "";
                  this.form.range_time = "";
                }
              }
            );
          }
        }
      }
    },
    validateWorkEnd() {
      this.validateDate();
    },
    validateDate() {
      if (this.form.work_time_begin && this.form.work_time_end) {
        let date_begin = moment(
          this.form.work_time_begin,
          "DD-MM-YYYY HH:mm"
        ).format("YYYY:MM:dddd");
        let date_end = moment(
          this.form.work_time_end,
          "DD-MM-YYYY HH:mm"
        ).format("YYYY:MM:dddd");
        if (date_begin != date_end) {
          this.$alert(
            "Thời gian làm đăng ký phải cùng trong một ngày",
            "Thời gian không hợp lệ",
            {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                if (this.form.type == "LO") {
                  this.form.leave_time_end = "";
                } else {
                  this.form.work_time_end = "";
                }
              }
            }
          );
          return false;
        } else {
          let work_time_begin = moment(
            this.form.work_time_begin,
            "DD-MM-YYYY HH:mm"
          ).format("HH:mm:ss");
          let work_time_end = moment(
            this.form.work_time_end,
            "DD-MM-YYYY HH:mm"
          ).format("HH:mm:ss");
          if (this.compareTime(work_time_begin, work_time_end)) {
            this.$alert(
              "Yêu cầu thời gian kết thúc phải lớn hơn thời gian bắt đầu",
              "Thời gian không hợp lệ",
              {
                confirmButtonText: "OK",
                type: "warning",
                center: true,
                callback: action => {
                  this.form.work_time_end = "";
                }
              }
            );
            return false;
          } else {
            this.form.range_time = this.calculateRangeTime(
              work_time_begin,
              work_time_end
            );
            return true;
          }
        }
      }
      return true;
    },
    calculateRangeTime(begin_time, end_time) {
      console.log(begin_time);
      console.log(end_time);
      let begin = new Date("01/01/2000 " + begin_time);
      let end = new Date("01/01/2000 " + end_time);
      return (end.getTime() - begin.getTime()) / 1000 / 60;
    },
    compareTime(time1, time2) {
      console.log(time1);
      console.log(time2);

      let datetime1 = new Date("01/01/2000 " + time1);
      let datetime2 = new Date("01/01/2000 " + time2);
      if (datetime1 > datetime2) {
        return true;
      }
      return false;
    },
    createRequest(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.form.user_code = this.user.employee_code;
          axios.post("/api/form_requests", this.form).then(response => {
            if (response.data.status === false) {
              this.error.message = response.data.message;
              setTimeout(() => {
                this.error.message = "";
              }, 3000);
              this.$notify.error({
                title: "Thất bại",
                message: response.data.message,
                position: "bottom-right"
              });
            } else {
              this.$refs[formName].resetFields();
              this.$notify({
                title: "Hoàn thành",
                message: "Gửi yêu cầu thành công",
                type: "success",
                position: "bottom-right"
              });
              this.$router.push("/request_ot");
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
        }
      }
    }
  }
}
</style>

<style lang="scss ">
</style>