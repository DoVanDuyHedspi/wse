<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Sơ đồ tổ chức</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="text-center mt-3 mb-5">
        <h3>Danh sách tổ chức trong công ty</h3>
      </div>
      <div class="error" v-if="error.message.length">
        <div class="alert alert-danger" role="alert">{{ error.message }}</div>
      </div>
      <div class="noti" v-if="noti.length">
        <div class="alert alert-success" role="alert">{{ noti }}</div>
      </div>
    </div>
    <div class="container">
      <div>
        <el-button icon="el-icon-plus" type="success" @click="dialogCreateVisible = true">Thêm mới</el-button>
      </div>
      <el-dialog title="Tạo nhóm mới" width="40%" center :visible.sync="dialogCreateVisible">
        <el-form :model="form" :rules="rules" ref="groupForm" label-width="120px">
          <el-form-item label="Tên" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item label="Nhóm quản lý" prop="description">
            <el-cascader
              v-model="form.parent_id"
              :options="groups"
              :props="{ checkStrictly: true, label: 'name', value: 'id' }"
              placeholder="Thuộc quản lý của nhóm"
              clearable
              class="w-100"
            ></el-cascader>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogCreateVisible = false">Hủy</el-button>
          <el-button type="primary" @click="createGroup('groupForm')">Tạo mới</el-button>
        </span>
      </el-dialog>
      <div class="my-4">
        <el-input placeholder="Tìm kiếm..." v-model="filterText" class="mb-3"></el-input>

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
              <el-button type="text">
                <i class="el-icon-s-unfold"></i>
                <span>{{ node.label }}</span>
              </el-button>
            </span>
          </span>
        </el-tree>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      groups: [],
      defaultProps: {
        children: "children",
        label: "name",
        value: "id"
      },
      error: {
        message: ""
      },
      noti: "",
      filterText: "",
      dialogCreateVisible: false,
      form: {
        name: "",
        parent_id: ""
      },
      rules: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }]
      }
    };
  },
  created() {
    this.getGroups();
  },
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
    getGroups() {
      axios.get("/groups").then(response => {
        if (response.data.status === false) {
          this.error.message = response.data.message;
        } else {
          console.log(response.data);
          this.groups = response.data;
        }
      });
    },
    createGroup(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios
            .post("/groups", {
              name: this.form.name,
              parent_id: this.form.parent_id[this.form.parent_id.length - 1]
            })
            .then(response => {
              this.dialogCreateVisible = false;
              this.form.name = "";
              this.form.parent_id = "";
              if (response.data.status === false) {
                this.error.message = response.data.message;
                setTimeout(() => {
                  this.error.message = "";
                }, 3000);
              } else {
                this.noti = "Tạo mới thành công!";
                this.groups = response.data;
                setTimeout(() => {
                  this.noti = "";
                }, 3000);
              }
            });
        }
      });
    }
  }
};
</script>

<style>
.tree button {
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
  padding-right: 700px;
}

label {
  margin: 0;
}

.el-dialog__body {
  border-top: 1px solid #dcdfe6;
  border-bottom: 1px solid #dcdfe6;
}
</style>