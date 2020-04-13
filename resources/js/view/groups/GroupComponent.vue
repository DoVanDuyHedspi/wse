<template>
  <div id="group-setting">
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Sơ đồ tổ chức</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="text-center mt-3 mb-5">
        <h3>THIẾT LẬP TỔ CHỨC</h3>
      </div>
    </div>
    <div class="container">
      <div class="my-4">
        <div>
          <h3>Phòng ban, bộ phận</h3>
        </div>
        <el-row :gutter="20">
          <el-col :span="12">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div class="text-center">
                <span style="font-size: 20px; font-weight: bolder">Danh sách</span>
              </div>
              <el-input
                placeholder="Tìm kiếm..."
                v-model="filterText"
                class="m-3"
                style="max-width:95%"
              ></el-input>
              <el-tree
                class="filter-tree tree"
                :data="groups"
                :props="defaultProps"
                default-expand-all
                :filter-node-method="filterNode"
                :expand-on-click-node="false"
                ref="tree"
              >
                <span class="custom-tree-node" slot-scope="{ node, groups }">
                  <span>
                    <el-button type="text" class="group">
                      <span>{{ node.label }}</span>
                    </el-button>
                  </span>
                  <span>
                    <el-button type="text">Sửa</el-button>
                    <el-button type="text">Xóa</el-button>
                  </span>
                </span>
              </el-tree>
            </div>
          </el-col>
          <el-col :span="12">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div class="text-center">
                <span style="font-size: 20px; font-weight: bolder">Thêm mới</span>
              </div>
              <el-form
                :model="form_group"
                :rules="rules_group"
                ref="groupForm"
                label-width="120px"
                label-position="top"
                class="form-request"
              >
                <el-form-item label="Tên" prop="name">
                  <el-input v-model="form_group.name"></el-input>
                </el-form-item>
                <el-form-item label="Nhóm quản lý" prop="description">
                  <el-cascader
                    v-model="form_group.parent_id"
                    :options="groups"
                    :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                    placeholder="Thuộc quản lý của nhóm"
                    clearable
                    class="w-100"
                  ></el-cascader>
                </el-form-item>
              </el-form>
              <div class="text-center">
                <el-button type="primary" @click="createGroup('groupForm')">Tạo mới</el-button>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="my-4">
        <div>
          <h3>Chi nhánh</h3>
        </div>
        <el-row :gutter="20">
          <el-col :span="12">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div class="text-center">
                <span style="font-size: 20px; font-weight: bolder">Danh sách</span>
              </div>
              <el-table :data="branches" stripe style="width: 100%">
                <el-table-column prop="id" label="#Id" width="50"></el-table-column>
                <el-table-column prop="name" label="Tên"></el-table-column>
                <el-table-column prop="description" label="Mô tả"></el-table-column>
                <el-table-column label="Thao tác">
                  <template slot-scope="scope">
                    <el-button size="mini">
                      <i class="el-icon-edit"></i>
                    </el-button>
                    <el-button size="mini" type="danger">
                      <i class="el-icon-delete"></i>
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </el-col>
          <el-col :span="12">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div>
                <span style="font-size: 20px; font-weight: bolder">Thêm mới</span>
                <el-form
                  :model="form_branch"
                  :rules="rules_branch"
                  ref="branchForm"
                  label-width="120px"
                  label-position="top"
                  class="form-request"
                >
                  <el-form-item label="Tên" prop="name">
                    <el-input v-model="form_branch.name"></el-input>
                  </el-form-item>
                  <el-form-item label="Mô tả" prop="description">
                    <el-input type="textarea" :rows="2" v-model="form_branch.description"></el-input>
                  </el-form-item>
                </el-form>
                <el-row :gutter="20">
                  <el-col :span="24">
                    <b>Ảnh đại diện</b>
                  </el-col>
                  <el-col :span="24" class="text-center">
                    <img ref="image" id="image" v-if="imageUrl" :src="imageUrl" class="avatar" />
                  </el-col>
                  <el-col :span="24" class="text-center my-2">
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
                  <el-col :span="24" class="text-center">
                    <el-button type="primary" @click="createBranch('branchForm')">Tạo mới</el-button>
                  </el-col>
                </el-row>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      defaultProps: {
        children: "children",
        label: "name",
        value: "id"
      },
      error: {
        message: ""
      },
      filterText: "",
      dialogCreateVisible: false,
      form_group: {
        name: "",
        parent_id: ""
      },
      form_branch: {
        name: "",
        description: ""
      },
      imageBranch: "",
      imageUrl: "",
      rules_group: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }]
      },
      disabled: false,
      rules_branch: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        description: [
          { required: true, message: "Hãy nhập mô tả", trigger: "blur" }
        ]
      }
    };
  },
  created() {},
  computed: mapState({
    groups: state => state.infoCompany.groups,
    branches: state => state.infoCompany.branches
  }),
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.name.indexOf(value) !== -1;
    },
    createGroup(formName) {
      console.log("1");
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios
            .post("/api/groups", {
              name: this.form_group.name,
              parent_id: this.form_group.parent_id[
                this.form_group.parent_id.length - 1
              ]
            })
            .then(response => {
              this.form_group.name = "";
              this.form_group.parent_id = "";
              if (response.data.status === false) {
                this.error.message = response.data.message;
                this.$notify.error({
                  title: "Thất bại",
                  message: response.data.message,
                  position: "bottom-right"
                });
              } else {
                this.groups = response.data;
                this.$store.dispatch("fetchCompanyInfo");
                this.$notify({
                  title: "Hoàn thành",
                  message: "Tạo mới thành công!",
                  type: "success",
                  position: "bottom-right"
                });
              }
            });
        }
      });
    },
    handlePreviewAvatar(file) {
      this.imageUrl = URL.createObjectURL(file.raw);
      this.imageBranch = file.raw;
    },
    handleRemove(file) {
      this.imageBranch = "";
    },
    createBranch(formName) {
      this.$refs[formName].validate(valid => {
        let rawData = {
          name: this.form_branch.name,
          description: this.form_branch.description
        };
        rawData = JSON.stringify(rawData);
        let formData = new FormData();
        formData.append("image", this.imageBranch);
        formData.append("data", rawData);
        if (valid) {
          axios
            .post("/api/branches", formData, {
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
                this.branches.push(response.data);
                (this.imageBranch = ""), (this.imageUrl = "");
                this.$refs[formName].resetFields();
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

<style lang="scss">
#group-setting {
  .tree .group {
    font-size: large;
    font-weight: bold;
    color: black;
  }

  .custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
  }

  label {
    margin: 0;
  }

  .el-dialog__body {
    border-top: 1px solid #dcdfe6;
    border-bottom: 1px solid #dcdfe6;
  }

  .avatar {
    max-width: 100%;
    max-height: 200px;
    display: block;
    margin: auto;
    margin-bottom: 15px;
  }

  .form-request label,
  b {
    font-size: 16px;
    font-weight: bolder;
    padding: 0px;
    color: black;
  }
}
</style>