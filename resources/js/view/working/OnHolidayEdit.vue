<template>
  <div class="p-4 bg-white" id="on-holiday-create">
    <div>
      <el-button-group>
        <el-button type="primary" size="medium">Thiết lập nghỉ lễ</el-button>
        <router-link to="/working-day/on-leave-type">
          <el-button type="default" size="medium">Thiết lập nghỉ phép</el-button>
        </router-link>
      </el-button-group>
    </div>
    <el-divider></el-divider>
    <div class="text-right mb-2">
      <router-link to="/working-day/on-holiday">
        <el-button type="primary" size="medium">
          <i class="el-icon-s-order"></i> Danh sách
        </el-button>
      </router-link>
    </div>
    <div>
      <el-form
        :model="form"
        :rules="rules"
        ref="form-holiday"
        label-width="200px"
        class="form-request"
        label-position="top"
      >
        <div class="p-3">
          <el-row :gutter="20" class="mb-0">
            <el-col :span="12">
              <el-form-item label="Tên ngày nghỉ" prop="name" size="medium">
                <el-input v-model="form.name" placeholder="Tên ngày lễ"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item
                label="Hệ số lương đi làm ngày nghỉ"
                class="w-50"
                prop="coefficients_salary"
                size="medium"
              >
                <el-input type="number" v-model="form.coefficients_salary"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="20">
            <el-col :span="12">
              <el-form-item label="Ngày bắt đầu" prop="start_date">
                <el-date-picker
                  v-model="form.start_date"
                  type="date"
                  format="dd-MM-yyyy"
                  value-format="dd-MM-yyyy"
                  placeholder="Chọn một ngày"
                  class="w-100"
                  size="medium"
                ></el-date-picker>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="Ngày kết thúc" prop="end_date">
                <el-date-picker
                  v-model="form.end_date"
                  type="date"
                  format="dd-MM-yyyy"
                  value-format="dd-MM-yyyy"
                  placeholder="Chọn một ngày"
                  class="w-100"
                  size="medium"
                ></el-date-picker>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </el-form>
    </div>
    <div class="text-center mt-3">
      <el-button type="default" @click="cancel" size="medium">Hủy</el-button>
      <el-button type="primary" @click="updateHoliday('form-holiday')" size="medium">Cập nhật</el-button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      error: {
        message: ""
      },
      form: {
        name: "",
        start_date: "",
        end_date: "",
        coefficients_salary: 1,
        id: "",
      },
      rules: {
        name: {
          required: true,
          message: "Hãy nhập tên ngày nghỉ",
          trigger: "blur"
        },
        coefficients_salary: [
          {
            required: true,
            message: "Hãy nhập hệ số lương",
            trigger: "blur"
          }
        ],
        start_date: {
          required: true,
          message: "Hãy nhập ngày bắt đầu",
          trigger: "blur"
        },
        end_date: {
          required: true,
          message: "Hãy nhập ngày kết thúc",
          trigger: "blur"
        }
      }
    };
  },
  created() {
    this.fetchHoliday();
  },
  methods: {
    cancel(formName) {
      this.$router.push("/working-day/on-holiday");
    },
    fetchHoliday: async function() {
      axios.get("/api/holiday/" + this.$route.params.id).then(response => {
        this.form = response.data;
      });
    },
    updateHoliday(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios.put("/api/holiday/"+ this.form.id, this.form).then(response => {
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
                message: "Thêm mới nghỉ lễ thành công",
                type: "success",
                position: "bottom-right"
              });
              this.$router.push("/working-day/on-holiday");
            }
          });
        }
      });
    }
  }
};
</script>

<style lang="scss" >
#on-holiday-create {
  .form-request {
    label {
      font-size: 16px;
      font-weight: bolder;
      padding: 0px;
      color: black;
      padding: 0 !important;
    }
  }
}
</style>