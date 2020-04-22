<template>
  <div class="new_request bg-white">
    <div>
      <div class="header text-center">
        <h4 class="m-0">Yêu cầu IL, LE, LO, QQ</h4>
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
              <el-input :disabled="true" v-model="form.user_name"></el-input>
            </el-form-item>
            <el-form-item label="Mã số nhân viên">
              <el-input :disabled="true" v-model="form.user_code"></el-input>
            </el-form-item>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item label="Chi nhánh">
                  <el-select v-model="form.user_branch" class="w-100" :disabled="true">
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
                    v-model="form.user_group"
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
                :disabled="true"
              >
                <el-option label="Đến muộn sáng" value="ILM"></el-option>
                <el-option label="Đến muộn chiều" value="ILA"></el-option>
                <el-option label="Về sớm sáng" value="LEM"></el-option>
                <el-option label="Về sớm chiều" value="LEA"></el-option>
                <el-option label="Rời khỏi vị trí" value="LO"></el-option>
                <el-option label="Quên chấm công đến" value="QQD"></el-option>
                <el-option label="Quên chấm công về" value="QQV"></el-option>
                <el-option label="Quân chấm công cả ngày" value="QQF"></el-option>
              </el-select>
            </el-form-item>
            <div>
              <el-form-item
                label="Thời gian đến buổi sáng"
                v-if="form.type == 'ILM'"
                prop="leave_time_end"
              >
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.leave_time_end"
                  style="width: 50%"
                  @blur="validateIL('ILM',specified_working_time.morning_late, specified_working_time.morning_begin)"
                ></el-date-picker>
              </el-form-item>
              <el-form-item
                label="Thời gian đến buổi chiều"
                v-if="form.type == 'ILA'"
                prop="leave_time_end"
              >
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.leave_time_end"
                  style="width: 50%"
                  @blur="validateIL('ILA',specified_working_time.afternoon_late, specified_working_time.afternoon_begin)"
                ></el-date-picker>
              </el-form-item>
              <el-form-item
                label="Thời gian về buổi sáng"
                v-if="form.type == 'LEM'"
                prop="leave_time_begin"
              >
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.leave_time_begin"
                  style="width: 50%"
                  @blur="validateLE('LEM',specified_working_time.morning_late, specified_working_time.morning_end)"
                ></el-date-picker>
              </el-form-item>
              <el-form-item
                label="Thời gian về buổi chiều"
                v-if="form.type == 'LEA'"
                prop="leave_time_begin"
              >
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.leave_time_begin"
                  style="width: 50%"
                  @blur="validateLE('LEA',specified_working_time.afternoon_late, specified_working_time.afternoon_end)"
                ></el-date-picker>
              </el-form-item>
              <el-form-item label="Thời gian đến" v-if="form.type == 'QQD'" prop="work_time_begin">
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.work_time_begin"
                  style="width: 50%"
                ></el-date-picker>
              </el-form-item>
              <el-form-item label="Thời gian về " v-if="form.type == 'QQV'" prop="work_time_end">
                <el-date-picker
                  type="datetime"
                  format="dd-MM-yyyy HH:mm"
                  value-format="dd-MM-yyyy HH:mm"
                  v-model="form.work_time_end"
                  style="width: 50%"
                ></el-date-picker>
              </el-form-item>
              <el-row :gutter="20" v-if="form.type == 'LO'">
                <el-col :span="12">
                  <el-form-item label="Từ" prop="leave_time_begin">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.leave_time_begin"
                      style="width: 100%"
                      @blur="validateLO()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Đến" prop="leave_time_end">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.leave_time_end"
                      style="width: 100%"
                      @blur="validateLO()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row :gutter="20" v-if="form.type == 'QQF'">
                <el-col :span="12">
                  <el-form-item label="Thời gian đến" prop="work_time_begin">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.work_time_begin"
                      style="width: 100%"
                      @blur="validateQQF()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Thời gian về " prop="work_time_end">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.work_time_end"
                      style="width: 100%"
                      @blur="validateQQF()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
            <div v-if="form.type != 'QQD' && form.type != 'QQV' && form.type != 'QQF'">
              <b>Đăng ký thời gian làm bù</b>
              <el-row :gutter="20">
                <el-col :span="12">
                  <el-form-item label="Làm bù từ" prop="work_time_begin">
                    <el-date-picker
                      type="datetime"
                      v-model="form.work_time_begin"
                      style="width: 100%"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      @blur="validateWorkBegin()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Làm bù đến" prop="work_time_end">
                    <el-date-picker
                      type="datetime"
                      format="dd-MM-yyyy HH:mm"
                      value-format="dd-MM-yyyy HH:mm"
                      v-model="form.work_time_end"
                      style="width: 100%"
                      @blur="validateWorkEnd()"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-form-item label="Lý do" prop="reason">
                <el-input type="textarea" v-model="form.reason"></el-input>
              </el-form-item>
            </div>
          </el-form>
          <div class="text-center">
            <el-button
              type="primary"
              @click="updateRequest('form-request')"
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
        leave_time_begin: "",
        leave_time_end: "",
        work_time_begin: "",
        work_time_end: "",
        reason: "",
        range_time: "",
        id: "",
        user_name: "",
        user_branch: "",
        user_group: "",
      },
      rules: {
        reason: { required: true, message: "Hãy nhập lý do", trigger: "blur" },
        type: {
          required: true,
          message: "Hãy nhập hình thức",
          trigger: "blur"
        },
        leave_time_begin: {
          required: true,
          message: "Hãy nhập thời gian",
          trigger: "blur"
        },
        leave_time_end: {
          required: true,
          message: "Hãy nhập thời gian",
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
      },
      validate: false
    };
  },
  created() {
    // this.getWorkingTimeInfo();
    this.getFormRequest();
  },
  computed: mapState({
    infoCompany: state => state.infoCompany,
    specified_working_time: state => state.timekeeping
  }),
  methods: {
    getFormRequest() {
      console.log(this.$route.params.id);
      axios.get("/api/form_requests/" + this.$route.params.id).then(response => {
        this.form = response.data;
      })
    },
    getWorkingTimeInfo() {
      axios.get("/api/specifiedWorkingTime").then(response => {
        this.specified_working_time = response.data;
      });
    },
    hanldeChangeType() {
      this.form.leave_time_begin = "";
      this.form.leave_time_end = "";
      this.form.work_time_begin = "";
      this.form.work_time_end = "";
      this.form.range_time = "";
      this.validate = false;
    },
    validateIL(type, specified_late_time, specified_begin_time) {
      let leave_time_end = moment(this.form.leave_time_end, 'DD-MM-YYYY HH:mm').format("HH:mm:ss");

      if (this.compareTime(leave_time_end, specified_late_time)) {
        this.$alert(
          "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa",
          "Thời gian không hợp lệ",
          {
            confirmButtonText: "OK",
            type: "warning",
            center: true,
            callback: action => {
              this.form.leave_time_end = "";
              this.validate = false;
            }
          }
        );
      } else if (this.compareTime(specified_begin_time, leave_time_end)) {
        this.$alert(
          "Đây là form xin đi muộn, thời gian đến của bạn không chính xác",
          "Thời gian không hợp lệ",
          {
            confirmButtonText: "OK",
            type: "warning",
            center: true,
            callback: action => {
              this.form.leave_time_end = "";
              this.validate = false;
            }
          }
        );
      } else {
        this.validate = true;
        this.form.range_time = this.calculateRangeTime(
          specified_begin_time,
          leave_time_end
        );
      }
    },
    validateLE(type, specified_late_time, specified_end_time) {
      let leave_time_begin = moment(this.form.leave_time_begin, 'DD-MM-YYYY HH:mm').format(
        "HH:mm:ss"
      );

      if (this.compareTime(specified_late_time, leave_time_begin)) {
        this.$alert(
          "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa",
          "Thời gian không hợp lệ",
          {
            confirmButtonText: "OK",
            type: "warning",
            center: true,
            callback: action => {
              this.form.leave_time_begin = "";
              this.validate = false;
            }
          }
        );
      } else if (this.compareTime(leave_time_begin, specified_end_time)) {
        this.$alert(
          "Đây là form xin về sơm, thời gian đến của bạn không chính xác",
          "Thời gian không hợp lệ",
          {
            confirmButtonText: "OK",
            type: "warning",
            center: true,
            callback: action => {
              this.form.leave_time_begin = "";
              this.validate = false;
            }
          }
        );
      } else {
        this.validate = true;
        this.form.range_time = this.calculateRangeTime(
          leave_time_begin,
          specified_end_time
        );
      }
    },
    validateWorkBegin() {
      let work_time_begin = moment(this.form.work_time_begin, 'DD-MM-YYYY HH:mm').format("HH:mm");
      this.validate = true;
      if (
        this.compareTime(
          this.specified_working_time.afternoon_end,
          work_time_begin
        )
      ) {
        this.$alert(
          "Thời gian làm bù không được bắt đầu trong thời gian làm việc chính thức",
          "Thời gian không hợp lệ",
          {
            confirmButtonText: "OK",
            type: "warning",
            center: true,
            callback: action => {
              this.form.work_time_begin = "";
              this.validate = false;
            }
          }
        );
      } else if (this.form.work_time_end) {
        this.validateWorkEnd();
      } else {
        this.validate = true;
      }
    },
    validateWorkEnd() {
      if (this.form.range_time && this.form.work_time_begin) {
        if (
          this.validateDate(this.form.work_time_begin, this.form.work_time_end)
        ) {
          let work_time_begin = moment(this.form.work_time_begin, 'DD-MM-YYYY HH:mm').format(
            "HH:mm"
          );
          let work_time_end = moment(this.form.work_time_end, 'DD-MM-YYYY HH:mm').format("HH:mm");
          let work_range_time = this.calculateRangeTime(
            work_time_begin,
            work_time_end
          );
          if (work_range_time < this.form.range_time) {
            this.$alert("Thời gian làm bù không đủ", "Thời gian không hợp lệ", {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                this.form.work_time_end = "";
                this.validate = false;
              }
            });
          } else {
            this.validate = true;
          }
        }
      }
    },
    validateQQF() {
      if (this.form.work_time_begin && this.form.work_time_end) {
        if (
          this.validateDate(this.form.work_time_begin, this.form.work_time_end)
        ) {
          let work_time_begin = moment(this.form.work_time_begin, 'DD-MM-YYYY HH:mm').format(
            "HH:mm:ss"
          );
          let work_time_end = moment(this.form.work_time_end, 'DD-MM-YYYY HH:mm').format(
            "HH:mm:ss"
          );

          if (this.compareTime(work_time_begin, work_time_end)) {
            this.$alert(
              "Yêu cầu thời gian về phải lớn hơn thời gian đến",
              "Thời gian không hợp lệ",
              {
                confirmButtonText: "OK",
                type: "warning",
                center: true,
                callback: action => {
                  this.form.work_time_end = "";
                  this.validate = false;
                }
              }
            );
          } else {
            this.validate = true;
          }
        }
      }
    },
    validateLO() {
      if (this.form.leave_time_begin) {
        let leave_time_begin = moment(this.form.leave_time_begin, 'DD-MM-YYYY HH:mm').format(
          "HH:mm:ss"
        );
        if (
          this.compareTime(
            this.specified_working_time.morning_begin,
            leave_time_begin
          )
        ) {
          this.$alert(
            "Thời gian đăng ký của bạn không thể sớm hơn thời gian bắt đầu làm việc của công ty",
            "Thời gian không hợp lệ",
            {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                this.form.leave_time_begin = "";
                this.validate = false;
              }
            }
          );
        } else {
          this.validate = true;
        }
      }
      if (this.form.leave_time_end) {
        let leave_time_end = moment(this.form.leave_time_end, 'DD-MM-YYYY HH:mm').format(
          "HH:mm:ss"
        );
        if (
          this.compareTime(
            leave_time_end,
            this.specified_working_time.afternoon_end
          )
        ) {
          this.$alert(
            "Thời gian đăng ký của bạn không thể muộn hơn thời gian kết thúc làm việc của công ty",
            "Thời gian không hợp lệ",
            {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                this.form.leave_time_end = "";
                this.validate = false;
              }
            }
          );
        } else {
          this.validate = true;
        }
      }
      if (this.form.leave_time_begin && this.form.leave_time_end) {
        if (
          this.validateDate(this.form.leave_time_begin, this.form.leave_time_end)
        ) {
          let leave_time_begin = moment(this.form.leave_time_begin, 'DD-MM-YYYY HH:mm').format(
            "HH:mm:ss"
          );
          let leave_time_end = moment(this.form.leave_time_end, 'DD-MM-YYYY HH:mm').format(
            "HH:mm:ss"
          );

          if (this.compareTime(leave_time_begin, leave_time_end)) {
            this.$alert(
              "Yêu cầu thời gian về phải lớn hơn thời gian đi",
              "Thời gian không hợp lệ",
              {
                confirmButtonText: "OK",
                type: "warning",
                center: true,
                callback: action => {
                  this.form.leave_time_end = "";
                  this.validate = false;
                }
              }
            );
          } else {
            this.validate = true;
          }
        }
      }
    },
    validateDate(date1, date2) {
      let date_1 = moment(date1, 'DD-MM-YYYY HH:mm').format("YYYY:MM:dddd");
      let date_2 = moment(date2, 'DD-MM-YYYY HH:mm').format("YYYY:MM:dddd");
      console.log(date_1);
      console.log(date_2);
      console.log(date_1 != date_2);
      if (date_1 != date_2) {
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
                this.validate = false;
                this.form.work_time_end = "";
              }
            }
          }
        );
        return false;
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
      let datetime1 = new Date("01/01/2000 " + time1);
      let datetime2 = new Date("01/01/2000 " + time2);
      if (datetime1 > datetime2) {
        return true;
      }
      return false;
    },
    updateRequest(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios.put("/api/form_requests/" + this.form.id, this.form).then(response => {
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

              this.$notify({
                title: "Hoàn thành",
                message: "Sửa yêu cầu thành công",
                type: "success",
                position: "bottom-right"
              });
              this.$router.push("/request_leaves");
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