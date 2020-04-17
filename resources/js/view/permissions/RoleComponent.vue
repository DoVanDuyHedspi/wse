<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Phân quyền hệ thống</el-breadcrumb-item>
        <el-breadcrumb-item>Nhóm quyền</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container mt-3">
      <div class="my-4">
        <el-row>
          <el-col :span="24">
            <div class="text-center">
              <h3>
                DANH SÁCH NHÓM QUYỀN
                <!-- <el-tooltip effect="dark" :content="info" placement="right-start">
                  <i class="el-icon-question" style="font-size: 20px"></i>
                </el-tooltip>-->
              </h3>
            </div>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="6">
          </el-col>
          <el-col :span="12" :offset="12">
            <div class="grid-content float-right">
              <el-button
                icon="el-icon-plus"
                type="success"
                @click="dialogCreateVisible = true"
              >Thêm mới</el-button>
              <router-link to="/permission">
                <el-button icon="el-icon-s-custom" type="primary">Quyền hạn</el-button>
              </router-link>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-dialog title="Tạo vai trò mới" width="80%" center :visible.sync="dialogCreateVisible">
        <el-form
          :model="form"
          label-position="top"
          :rules="rules"
          ref="roleForm"
          label-width="120px"
        >
          <el-row :gutter="30">
            <el-col :span="10">
              <el-form-item label="Tên vai trò" prop="slug">
                <el-input v-model="form.slug"></el-input>
              </el-form-item>
              <el-form-item label="Nội dung vai trò" prop="name">
                <el-input v-model="form.name"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="14">
              <p>Vai trò này có các quyền</p>
              <el-checkbox-group v-model="form.permissions">
                <el-checkbox
                  v-for="permission in permissions"
                  :label="permission.id"
                  :key="permission.id"
                  border
                  size="medium"
                  class="m-2"
                >{{permission.name}}</el-checkbox>
              </el-checkbox-group>
            </el-col>
          </el-row>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogCreateVisible = false">Hủy</el-button>
          <el-button type="primary" @click="createRole('roleForm')">Tạo mới</el-button>
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
        header-cell-class-name="text-center"
      >
        <el-table-column property="id" label="#Id" width="50" class-name="text-center"></el-table-column>
        <el-table-column property="slug" label="Tên" width="150"></el-table-column>
        <el-table-column property="name" label="Nội dung" width="200"></el-table-column>
        <el-table-column label="Quyền hạn">
          <template slot-scope="scope">
            <el-tag
              class="m-1"
              type="primary"
              v-for="per in scope.row.permissions"
              :key="per.id"
            >{{per.slug}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column align="right" class-name="text-center" width="200">
          <template slot="header" slot-scope="scope">
            <el-input v-model="search" size="mini" placeholder="Tìm kiếm" />
          </template>
          <template slot-scope="scope">
            <el-button size="mini" @click="handleEdit(scope.$index, scope.row)"><i class="el-icon-edit"></i></el-button>
            <el-button
              size="mini"
              type="danger"
              @click.native.prevent="deleteRole(scope.$index, scope.row)"
            ><i class="el-icon-delete"></i></el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog title="Chỉnh sửa vai trò" width="80%" center :visible.sync="dialogEditVisible">
        <el-form :model="editForm" :rules="rules" ref="editForm" label-width="120px">
          <el-row :gutter="30">
            <el-col :span="10">
              <el-form-item label="Tên" prop="slug">
                <el-input v-model="editForm.slug"></el-input>
              </el-form-item>
              <el-form-item label="Nội dung" prop="name">
                <el-input v-model="editForm.name"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="14">
              <p>Vai trò này có các quyền</p>
              <el-checkbox-group v-model="editForm.permissions">
                <el-checkbox
                  v-for="permission in permissions"
                  :label="permission.id"
                  :key="permission.id"
                  border
                  size="medium"
                  class="m-2"
                >{{permission.name}}</el-checkbox>
              </el-checkbox-group>
            </el-col>
          </el-row>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">Hủy</el-button>
          <el-button type="primary" @click="editRole('editForm')">Cập nhật</el-button>
        </span>
      </el-dialog>
      <div class="mt-3">
        <pagination :data="roles" :align="'center'" @pagination-change-page="getRoles"></pagination>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      info: "Danh sách các vai trò của người dùng trong hệ thống",
      roles: {},
      dataTable: [],
      error: {
        message: ""
      },
      permissions: {},
      noti: "",
      search: "",
      dialogCreateVisible: false,
      dialogEditVisible: false,
      form: {
        slug: "",
        name: "",
        permissions: []
      },
      editForm: {
        slug: "",
        name: "",
        index: "",
        id: "",
        permissions: []
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
    this.getRoles();
  },
  methods: {
    getRoles(page = 1) {
      axios
        .get("/api/roles?page=" + page)
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            this.roles = response.data.roles;
            this.permissions = response.data.permissions;
            this.dataTable = this.roles.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    createRole(formName) {
      this.$refs[formName].validate(valid => {
        console.log(valid);
        if (valid) {
          axios.post("/api/roles", this.form).then(response => {
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
    handleEdit(index, role) {
      this.editForm.slug = role.slug;
      this.editForm.name = role.name;
      this.editForm.index = index;
      this.editForm.id = role.id;
      let permissions = this.roles.data[index].permissions;
      let list = [];
      permissions.map(function(per) {
        list.push(per.id);
      });
      this.editForm.permissions = list;
      this.dialogEditVisible = true;
    },
    editRole(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios
            .put("/api/roles/" + this.editForm.id, this.editForm)
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
                this.dataTable[this.editForm.index].slug =
                  response.data.role.slug;
                this.dataTable[this.editForm.index].name =
                  response.data.role.name;
                this.roles.data[this.editForm.index].permissions =
                  response.data.permissions;
                setTimeout(() => {
                  this.noti = "";
                }, 3000);
              }
            });
        }
      });
    },
    deleteRole(index, role) {
      this.$confirm("Bạn có chắc chắn muốn xóa vai trò này?", "Cảnh báo", {
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        type: "warning"
      })
        .then(() => {
          axios.delete("/api/roles/" + role.id).then(response => {
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