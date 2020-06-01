<template>
  <div class="p-4 bg-white" id="on-leave-type">
    <div>
      <el-button type="primary" size="medium">Thiết lập nghỉ lễ</el-button>
      <router-link to="/working-day/on-leave-type">
        <el-button type="default" size="medium">Thiết lập nghỉ phép</el-button>
      </router-link>
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
        ref="form-leave-type"
        label-width="200px"
        class="form-request"
        label-position="top"
      >
        <div class="p-3">
          <el-row :gutter="50" class="mb-0">
            <el-col :span="12">
              <el-form-item label="Tên loại nghỉ phép" prop="name" size="medium">
                <el-input v-model="form.name" placeholder="Tên loại nghỉ phép"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="Từ khóa" prop="slug" size="medium">
                <el-input v-model="form.slug" placeholder="Từ khóa"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="50" class="mb-0">
            <el-col :span="12">
              <el-form-item label="Số ngày" prop="number_days" size="medium">
                <el-input type="number" v-model="form.number_days"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="Loại" size="medium">
                <el-select v-model="form.type" class="w-100">
                  <el-option key="year" label="Theo năm" value="year"></el-option>
                  <el-option key="month" label="Theo tháng" value="month"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="50" class="mb-0">
            <el-col :span="12">
              <el-form-item label="Hình thức trả lương" size="medium">
                <el-select v-model="form.has_salary" class="w-100">
                  <el-option key="0" label="Không trả lương" :value="0"></el-option>
                  <el-option key="1" label="Có trả lương" :value="1"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="4">
              <el-form-item label="Cộng dồn">
                <el-switch v-model="has_accumulated" :width="60"></el-switch>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="Ngày hết hạn">
                <el-date-picker
                  v-model="form.expiry_date"
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
          <el-row>
            <el-col :span="24">
              <el-form-item label="Mô tả" prop="description">
                <el-input type="textarea" v-model="form.description"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </el-form>
    </div>
    <div class="text-center mt-3">
      <el-button type="default" @click="cancel('form-leave-type')" size="medium">Hủy</el-button>
      <el-button type="primary" @click="updateLeaveType('form-leave-type')" size="medium">Cập nhật</el-button>
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
        slug: "",
        description: "",
        number_days: 0,
        type: "year",
        has_salary: 0,
        has_accumulated: 0,
        expiry_date: null
      },
      has_accumulated: false,
      rules: {
        name: {
          required: true,
          message: "Hãy nhập tên loại ngày nghỉ",
          trigger: "blur"
        },
        slug: [
          {
            required: true,
            message: "Hãy nhập ký hiệu",
            trigger: "blur"
          }
        ],
        description: {
          required: true,
          message: "Hãy nhập mô tả",
          trigger: "blur"
        },
        number_days: {
          required: true,
          message: "Hãy nhập số ngày",
          trigger: "blur"
        }
      }
    };
  },
  created() {
    this.fetchLeaveType();
  },
  watch: {
    form: function(val) {
      this.has_accumulated = val.has_accumulated ? true : false;
    } 
  },
  methods: {
    cancel(formName) {
      this.$router.push("/working-day/on-leave-type");
    },
    fetchLeaveType: async function() {
      await axios
        .get("/api/leave_type/" + this.$route.params.id)
        .then(response => {
          this.form = response.data;
        });
    },
    updateLeaveType(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.has_accumulated) {
            this.form.has_accumulated = 1;
          } else {
            this.form.has_accumulated = 0;
          }
          axios
            .put("/api/leave_type/" + this.form.id, this.form)
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
                  message: "Cập nhật loại ngày nghỉ phép thành công",
                  type: "success",
                  position: "bottom-right"
                });
                this.$router.push("/working-day/on-leave-type");
              }
            });
        }
      });
    }
  }
};
</script>

<style lang="scss" >
#on-leave-type {
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