<template>
  <div>
    <!-- <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Phân quyền hệ thống</el-breadcrumb-item>
        <el-breadcrumb-item>Quyền hạn</el-breadcrumb-item>
      </el-breadcrumb>
    </div> -->
    <div class="container mt-3">
      <div class="my-4">
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="text-center">
              <h3>
                DANH SÁCH QUYỀN HẠN
                <!-- <el-tooltip effect="dark" :content="info" placement="right-start">
                  <i class="el-icon-question" style="font-size: 20px"></i>
                </el-tooltip> -->
              </h3>
            </div>
          </el-col>
          <el-col :span="24">
            <div class="grid-content float-right">
              <el-button
                icon="el-icon-plus"
                type="success"
                @click="dialogCreateVisible = true"
              >Thêm mới</el-button>
              <router-link to="/role">
                <el-button icon="el-icon-s-custom" type="primary">Nhóm quyền</el-button>
              </router-link>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-dialog title="Tạo quyền mới" width="40%" center :visible.sync="dialogCreateVisible">
        <el-form :model="form" :rules="rules" ref="permissionForm" label-width="120px">
          <el-form-item label="Tên" prop="slug">
            <el-input v-model="form.slug"></el-input>
          </el-form-item>
          <el-form-item label="Nội dung" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogCreateVisible = false">Hủy</el-button>
          <el-button type="primary" @click="createPermission('permissionForm')">Tạo mới</el-button>
        </span>
      </el-dialog>
      <div class="error" v-if="error.message.length">
        <div class="alert alert-danger" role="alert">{{ error.message }}</div>
      </div>
      <div class="noti" v-if="noti.length">
        <div class="alert alert-success" role="alert">{{ noti }}</div>
      </div>
      <el-table
        ref="multipleTable"
        :data="dataTable.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        border
      >
        <el-table-column property="id" label="#Id" width="50" class-name="text-center"></el-table-column>
        <el-table-column property="slug" label="Tên" class-name="text-center"></el-table-column>
        <el-table-column property="name" label="Nội dung" class-name="text-center"></el-table-column>
        <el-table-column align="right" class-name="text-center">
          <template slot="header" slot-scope="scope">
            <el-input v-model="search" size="mini" placeholder="Tìm kiếm" />
          </template>
          <template slot-scope="scope">
            <el-button size="mini" @click="handleEdit(scope.$index, scope.row)"><i class="el-icon-edit"></i></el-button>
            <el-button
              size="mini"
              type="danger"
              @click.native.prevent="deletePermisson(scope.$index, scope.row)"
            ><i class="el-icon-delete"></i></el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog title="Chỉnh sửa quyền" width="40%" center :visible.sync="dialogEditVisible">
        <el-form :model="editForm" :rules="rules" ref="editForm" label-width="120px">
          <el-form-item label="Tên" prop="slug">
            <el-input v-model="editForm.slug"></el-input>
          </el-form-item>
          <el-form-item label="Nội dung" prop="name">
            <el-input v-model="editForm.name"></el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">Hủy</el-button>
          <el-button type="primary" @click="editPermission('editForm')">Cập nhật</el-button>
        </span>
      </el-dialog>
      <div class="mt-3">
        <pagination :data="permissions" :align="'center'" @pagination-change-page="getPermissions"></pagination>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      info: "Danh sách các quyền của người dùng trong hệ thống",
      permissions: {},
      dataTable: [],
      error: {
        message: ""
      },
      noti: "",
      search: "",
      dialogCreateVisible: false,
      dialogEditVisible: false,
      form: {
        slug: "",
        name: ""
      },
      editForm: {
        slug: "",
        name: "",
        index: "",
        id: ""
      },
      rules: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        slug: [
          { required: true, message: "Hãy nhập nội dung", trigger: "blur" }
        ]
      }
    };
  },
  created() {
    this.getPermissions();
  },
  methods: {
    getPermissions(page = 1) {
      axios
        .get("/api/permissions?page=" + page)
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            console.log(response.data);
            this.permissions = response.data;
            this.dataTable = this.permissions.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    createPermission(formName) {
      this.$refs[formName].validate(valid => {
        console.log(valid);
        if (valid) {
          axios
            .post("/api/permissions", {
              slug: this.form.slug,
              name: this.form.name
            })
            .then(response => {
              this.dialogCreateVisible = false;
              this.$refs[formName].resetFields();
              if (response.data.status === false) {
                this.error.message = response.data.message;
                setTimeout(() => {
                  this.error.message = "";
                }, 3000);
              } else {
                this.noti = "Tạo mới thành công!";
                this.dataTable.push(response.data);
                setTimeout(() => {
                  this.noti = "";
                }, 3000);
              }
            });
        }
      });
    },
    handleEdit(index, permission) {
      this.editForm.slug = permission.slug;
      this.editForm.name = permission.name;
      this.editForm.index = index;
      this.editForm.id = permission.id;
      this.dialogEditVisible = true;
    },
    editPermission(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios
            .put("/api/permissions/" + this.editForm.id, {
              slug: this.editForm.slug,
              name: this.editForm.name,
              id: this.editForm.id
            })
            .then(response => {
              this.dialogEditVisible = false;
              this.$refs[formName].resetFields();
              if (response.data.status === false) {
                this.error.message = response.data.message;
                setTimeout(() => {
                  this.error.message = "";
                }, 3000);
              } else {
                this.noti = "Sửa quyền thành công!";
                this.dataTable[this.editForm.index].slug = response.data.slug;
                this.dataTable[this.editForm.index].name = response.data.name;
                setTimeout(() => {
                  this.noti = "";
                }, 3000);
              }
            });
        }
      });
    },
    deletePermisson(index, permisson) {
      this.$confirm("Bạn có chắc chắn muốn xóa quyền này?", "Cảnh báo", {
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        type: "warning"
      })
        .then(() => {
          axios.delete("/api/permissions/" + permisson.id).then(response => {
            if (response.data.status === false) {
              this.error.message = response.data.message;
              setTimeout(() => {
                this.error.message = "";
              }, 3000);
            } else {
              this.dataTable.splice(index, 1);
              this.noti = "Xóa thành công!";
            }
          });
        })
        .catch(() => {
          this.error.message = "Xóa thất bại!";
          setTimeout(() => {
            this.error.message = "";
          }, 3000);
        });
    }
  }
};
</script>

<style lang="scss">
.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}
.el-col {
  border-radius: 4px;
}
</style>