<template>
  <div>
    <!-- <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Dữ liệu workspace</el-breadcrumb-item>
      </el-breadcrumb>
    </div> -->
    <div class="container">
      <div class="text-center mt-3 mb-5">
        <h3>DANH SÁCH VĂN PHÒNG</h3>
      </div>
      <div class="error" v-if="error.message.length">
        <div class="alert alert-danger" role="alert">{{ error.message }}</div>
      </div>
      <div class="noti" v-if="noti.length">
        <div class="alert alert-success" role="alert">{{ noti }}</div>
      </div>
      <el-row :gutter="50">
        <el-col :span="24">
          <h5 class="grid-content">
            Có tổng số
            <b>{{ branches.length}}</b> văn phòng
          </h5>
        </el-col>
      </el-row>
      <el-row :gutter="50">
        <el-col
          :span="6"
          v-for="(branch, index) in branches"
          :key="index"
          v-bind:class="{clearboth: index%4==0}"
          class="mb-4 pr-2"
        >
          <el-card :body-style="{ padding: '10px' }">
            <img :src="branch.imageUrl" class="image" />
            <div style="padding: 14px;">
              <h5>Văn phòng: {{branch.name}}</h5>
              <span><b>Địa chỉ:</b> {{branch.description}}</span>
              <p class="mb-1"><b>Quản lý:</b> </p>
              <div class="bottom clearfix">
                <el-button
                  @click="directToManageTimesheets(branch)"
                  type="text"
                  class="button p-0 float-left"
                >Chấm công</el-button>
                <el-button @click="directToUsers(branch)" type="text" class="button p-0">Nhân sự</el-button>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      error: {
        message: ""
      },
      noti: "",
      search: "",
      dialogCreateVisible: false,
      form: {
        name: "",
        description: ""
      },
      imageFile: "",
      disabled: false,
      rules: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        description: [
          { required: true, message: "Hãy nhập mô tả", trigger: "blur" }
        ]
      }
    };
  },
  computed: mapState({
    branches: state => state.infoCompany.branches
  }),
  methods: {
    directToUsers(branch) {
      this.$router.push("/users?branch_id=" + branch.id);
    },
    directToManageTimesheets(branch) {
      this.$router.push("/manage_timesheets?branch_id=" + branch.id);
    },
  }
};
</script>

<style>
.clearboth {
  clear: both;
}

.bottom {
  line-height: 12px;
}

.button {
  padding: 0;
  float: right;
}

.image {
  width: 100%;
  display: block;
}

.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}

.clearfix:after {
  clear: both;
}
</style>