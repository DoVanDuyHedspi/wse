<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Dữ liệu workspace</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="text-center mt-3 mb-5">
        <h3>Danh sách văn phòng</h3>
      </div>
      <el-row :gutter="20">
        <el-col :span="6">
          <h5 class="grid-content">Có tổng số 2 văn phòng</h5>
        </el-col>
        <el-col :span="6" :offset="12">
          <div class="grid-content float-right">
            <el-button
              icon="el-icon-plus"
              type="success"
              @click="dialogCreateVisible = true"
            >Thêm mới</el-button>
          </div>
          <el-dialog
            title="Tạo chi nhánh mới"
            width="40%"
            center
            :visible.sync="dialogCreateVisible"
          >
            <el-form :model="form" :rules="rules" ref="branchForm" label-width="120px">
              <el-form-item label="Tên" prop="name">
                <el-input v-model="form.name"></el-input>
              </el-form-item>
              <el-form-item label="Mô tả" prop="description">
                <el-input type="textarea" :rows="2" v-model="form.description"></el-input>
              </el-form-item>
              <el-form-item label="Ảnh" prop="image">
                <el-input type="file" v-model="form.image"></el-input>
              </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogCreateVisible = false">Hủy</el-button>
              <el-button type="primary" @click="createBranch('branchForm')">Tạo mới</el-button>
            </span>
          </el-dialog>
        </el-col>
      </el-row>
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
      noti: "",
      search: "",
      dialogCreateVisible: false,
      form: {
        name: "",
        description: "",
        image: ""
      },
      rules: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        description: [
          { required: true, message: "Hãy nhập mô tả", trigger: "blur" }
        ],
        image: [{ required: true, message: "Hãy thêm ảnh", trigger: "blur" }]
      }
    };
  }
};
</script>

<style>
</style>