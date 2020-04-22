<template>
  <div id="group-setting">
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Thiết lập tổ chức</el-breadcrumb-item>
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
          <h3>Thời gian chấm công</h3>
        </div>
        <el-row :gutter="20">
          <el-col :span="24">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid #0be80b96;"
            >
              <el-form
                :model="timekeeping"
                :rules="rules_timekeeping"
                ref="timekeepingForm"
                label-width="120px"
                label-position="top"
                class="form-request"
              >
                <el-row :gutter="20">
                  <el-col :span="8">
                    <el-form-item label="Bắt đầu ca sáng:" prop="morning_begin">
                      <el-time-select
                        v-model="timekeeping.morning_begin"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="8">
                    <el-form-item label="Ca sáng cho phép muộn đến:" prop="morning_late">
                      <el-time-select
                        v-model="timekeeping.morning_late"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="8">
                    <el-form-item label="Kết thúc ca sáng:" prop="morning_end">
                      <el-time-select
                        v-model="timekeeping.morning_end"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                </el-row>
                <el-row :gutter="20">
                  <el-col :span="8">
                    <el-form-item label="Bắt đầu ca chiều:" prop="afternoon_begin">
                      <el-time-select
                        v-model="timekeeping.afternoon_begin"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="8">
                    <el-form-item label="Ca chiều cho phép muộn đến:" prop="afternoon_late">
                      <el-time-select
                        v-model="timekeeping.afternoon_late"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="8">
                    <el-form-item label="Kết thúc ca chiều:" prop="afternoon_end">
                      <el-time-select
                        v-model="timekeeping.afternoon_end"
                        :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                        placeholder="Select time"
                      ></el-time-select>
                    </el-form-item>
                  </el-col>
                </el-row>
              </el-form>
              <div class="text-center">
                <el-button type="primary" @click="settingTimekeeping('timekeepingForm')">Cập nhật</el-button>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="my-4">
        <div>
          <h3>Phòng ban, bộ phận</h3>
        </div>
        <el-row :gutter="20">
          <el-col :span="14">
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
          <el-col :span="10">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div class="text-center">
                <span style="font-size: 20px; font-weight: bolder">Thêm bộ phận mới</span>
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
          <el-col :span="14">
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
          <el-col :span="10">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid lightblue;"
            >
              <div>
                <div class="text-center">
                  <span style="font-size: 20px; font-weight: bolder">Thêm chi nhánh mới</span>
                </div>
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
      <div class="my-4">
        <div>
          <h3>Vị trí, chức vụ</h3>
        </div>
        <el-row :gutter="20">
          <el-col :span="14">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid gray;"
            >
              <div class="text-center">
                <span style="font-size: 20px; font-weight: bolder">Danh sách</span>
              </div>
              <el-table :data="positions" stripe style="width: 100%">
                <el-table-column prop="id" label="#Id" width="50"></el-table-column>
                <el-table-column prop="name" label="Tên vị trí"></el-table-column>
                <el-table-column label="Khung thời gian được phép chấm công" width="250">
                  <template slot-scope="scope">
                    <span>{{scope.row.begin_allowed_access}} <i class="el-icon-right"></i> {{scope.row.end_allowed_access}}</span>
                  </template>
                </el-table-column>
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
          <el-col :span="10">
            <div
              class="bg-white p-3"
              style="box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04); border-top: 5px solid gray;"
            >
              <div>
                <div class="text-center">
                  <span style="font-size: 20px; font-weight: bolder">Thêm vị trí mới</span>
                </div>
                <el-form
                  :model="form_position"
                  :rules="rules_position"
                  ref="positionForm"
                  label-width="120px"
                  label-position="top"
                  class="form-request"
                >
                  <el-form-item label="Tên" prop="name">
                    <el-input v-model="form_position.name"></el-input>
                  </el-form-item>
                  <el-form-item
                    label="Thời gian bắt đầu được phép chấm công:"
                    prop="begin_allowed_access"
                  >
                    <el-time-select
                      v-model="form_position.begin_allowed_access"
                      :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                      placeholder="Chọn thời gian"
                      class="w-100"
                    ></el-time-select>
                  </el-form-item>
                  <el-form-item label="Thời gian kết thúc chấm công:" prop="end_allowed_access">
                    <el-time-select
                      v-model="form_position.end_allowed_access"
                      :picker-options="{start: '00:00',step: '00:15',end: '24:00'}"
                      placeholder="Chọn thời gian"
                      class="w-100"
                    ></el-time-select>
                  </el-form-item>
                </el-form>
                <el-row :gutter="20">
                  <el-col :span="24" class="text-center">
                    <el-button type="primary" @click="createPosition('positionForm')">Tạo mới</el-button>
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
      form_group: {
        name: "",
        parent_id: ""
      },
      form_position: {
        name: "",
        begin_allowed_access: "",
        end_allowed_access: ""
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
      },
      rules_timekeeping: {
        morning_begin: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        morning_late: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        morning_end: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        afternoon_begin: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        afternoon_late: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        afternoon_end: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ]
      },
      rules_position: {
        name: [{ required: true, message: "Hãy nhập tên", trigger: "blur" }],
        begin_allowed_access: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ],
        end_allowed_access: [
          { required: true, message: "Hãy nhập thời gian", trigger: "blur" }
        ]
      }
    };
  },
  created() {},
  computed: mapState({
    groups: state => state.infoCompany.groups,
    branches: state => state.infoCompany.branches,
    positions: state => state.infoCompany.positions,
    timekeeping: state => state.timekeeping
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
    },
    createPosition(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          axios
            .post("/api/positions", this.form_position)
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
    },
    settingTimekeeping(formName) {
      this.$confirm(
        "Bạn có chắc chắn muốn thiết lập lại thời gian chấm công của công ty?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      ).then(() => {
        this.$refs[formName].validate(valid => {
          if (valid) {
            axios
              .post("/api/company/settingTimekeeping", this.timekeeping)
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
                    message: "Cập nhật thời gian chấm công thành công!",
                    type: "success",
                    position: "bottom-right"
                  });
                  this.$store.dispatch("fetchTimekeeping");
                }
              });
          }
        });
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