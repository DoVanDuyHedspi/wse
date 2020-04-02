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
      <div class="error" v-if="error.message.length">
        <div class="alert alert-danger" role="alert">{{ error.message }}</div>
      </div>
      <div class="noti" v-if="noti.length">
        <div class="alert alert-success" role="alert">{{ noti }}</div>
      </div>
      <el-row :gutter="20">
        <el-col :span="6">
          <h5 class="grid-content">
            Có tổng số
            <b>{{ branches.length}}</b> văn phòng
          </h5>
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
            </el-form>
            <el-row>
              <el-col :span="4" class="text-right pr-2">Ảnh</el-col>
              <el-col :span="20">
                <el-upload
                  action="#"
                  list-type="picture-card"
                  :auto-upload="false"
                  :on-change="handleChange"
                  :limit="1"
                >
                  <i slot="default" class="el-icon-plus"></i>
                  <div slot="file" slot-scope="{file}">
                    <img class="el-upload-list__item-thumbnail" :src="file.url" alt />
                    <span class="el-upload-list__item-actions">
                      <span
                        v-if="!disabled"
                        class="el-upload-list__item-delete"
                        @click="handleRemove(file)"
                      >
                        <i class="el-icon-delete"></i>
                      </span>
                    </span>
                  </div>
                </el-upload>
              </el-col>
            </el-row>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogCreateVisible = false">Hủy</el-button>
              <el-button type="primary" @click="createBranch('branchForm')">Tạo mới</el-button>
            </span>
          </el-dialog>
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
              <span>{{branch.description}}</span>
              <div class="bottom clearfix">
                <el-button type="text" class="button">Operating</el-button>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
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
  // created() {
  //   this.getBranches();
  // },
  computed: mapState({
    branches: state => state.infoCompany.branches
  }),
  methods: {
    // getBranches() {
    //   axios.get("/branches").then(response => {
    //     if (response.data.status === false) {
    //       this.error.message = response.data.message;
    //     } else {
    //       console.log(response.data);
    //       this.branches = response.data;
    //     }
    //   });
    // },
    handleChange(file) {
      this.imageFile = file.raw;
    },
    handleRemove(file) {
      console.log(file);
    },
    createBranch(formName) {
      this.$refs[formName].validate(valid => {
        let rawData = {
          name: this.form.name,
          description: this.form.description
        };
        rawData = JSON.stringify(rawData);
        let formData = new FormData();
        formData.append("image", this.imageFile);
        formData.append("data", rawData);
        if (valid) {
          axios
            .post("/branches", formData, {
              headers: {
                "Content-Type": "multipart/form-data"
              }
            })
            .then(response => {
              this.dialogCreateVisible = false;
              if (response.data.status === false) {
                this.error.message = response.data.message;
                this.$notify.error({
                  title: "Thất bại",
                  message: response.data.message,
                  position: "bottom-right"
                });
              } else {
                this.branches.push(response.data);
                this.$notify({
                  title: "Hoàn thành",
                  message: "Tạo mới thành công!",
                  type: "success",
                  position: "bottom-right"
                });
                this.$store.dispatch("fetchCompanyInfo");
              }
            });
        }
      });
    }
  }
};
</script>

<style>
.clearboth {
  clear: both;
}

.bottom {
  margin-top: 13px;
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