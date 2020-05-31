<template>
  <div class="new_request bg-white">
    <div>
      <div class="header text-center">
        <h4 class="m-0">KHIẾU NẠI KẾT QUẢ CHẤM CÔNG</h4>
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
            <div>
              <el-row :gutter="20">
                <el-col :span="24">
                  <el-form-item label="Ngày" prop="date">
                    <el-date-picker
                      v-model="form.date"
                      type="date"
                      format="dd-MM-yyyy"
                      value-format="dd-MM-yyyy"
                      placeholder="Chọn một ngày"
                      class="w-100"
                    ></el-date-picker>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Thời gian bắt đầu " prop="begin_time">
                    <el-time-select
                      v-model="form.begin_time"
                      :picker-options="{start: '07:00',step: '00:05',end: '24:00'}"
                      placeholder="Chọn thời gian bắt đầu"
                      class="w-100"
                      @blur="validateTime()"
                    ></el-time-select>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item label="Thời gian kết thúc " prop="end_time">
                    <el-time-select
                      v-model="form.end_time"
                      :picker-options="{start: form.begin_time,step: '00:05',end: '24:00'}"
                      placeholder="Chọn thời gian kết thúc"
                      class="w-100"
                      @blur="validateTime()"
                    ></el-time-select>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>

            <el-form-item label="Ghi chú" prop="note">
              <el-input type="textarea" v-model="form.note"></el-input>
            </el-form-item>
          </el-form>
          <div class="text-center">
            <el-button
              type="primary"
              @click="createRequest('form-request')"
              style="padding: 15px 100px 15px 100px;"
            >Gửi yêu cầu</el-button>
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
        note: "",
        begin_time: "",
        end_time: "",
        date: ""
      },
      rules: {
        note: { required: true, message: "Hãy nhập ghi chú", trigger: "blur" },
        type: {
          required: true,
          message: "Hãy nhập hình thức",
          trigger: "blur"
        },
        begin_time: {
          required: true,
          message: "Hãy nhập thời gian",
          trigger: "blur"
        },
        end_time: {
          required: true,
          message: "Hãy nhập thời gian",
          trigger: "blur"
        },
        date: {
          required: true,
          message: "Hãy nhập ngày",
          trigger: "blur"
        }
      }
    };
  },
  created() {
    this.getUser();
    if (Object.keys(this.$route.query).length !== 0) {
      if (this.$route.query.date) {
        this.form.date = this.$route.query.date;
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
    validateTime() {
      if (this.form.begin_time && this.form.end_time) {
        let begin_time = moment(this.form.begin_time, "HH:mm").format(
          "HH:mm:ss"
        );
        let end_time = moment(this.form.end_time, "HH:mm").format("HH:mm:ss");
        if (this.compareTime(begin_time, end_time)) {
          this.$alert(
            "Thời điểm kết thúc phải sau thời điểm bắt đầu",
            "Thời gian không hợp lệ",
            {
              confirmButtonText: "OK",
              type: "warning",
              center: true,
              callback: action => {
                this.form.end_time = "";
                this.validate = false;
              }
            }
          );
        }
      }
    },
    compareTime(time1, time2) {
      console.log(time1);
      console.log(time2);
      let datetime1 = new Date("01/01/2000 " + time1);
      let datetime2 = new Date("01/01/2000 " + time2);
      if (datetime1 >= datetime2) {
        return true;
      }
      return false;
    },
    createRequest(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios.post("/api/form_complain", this.form).then(response => {
            if (response.data.status === false) {
              this.error.message = response.data.message;
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
              this.$router.push("/request_check_camera");
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