<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Thành viên</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="p-4 mt-3">
      <div class="mb-2">
        <el-row :gutter="20">
          <el-col :span="12">
            <div class="grid-content">
              <h3>
                Danh sách thành viên
                <el-tooltip effect="dark" :content="info" placement="right-start">
                  <i class="el-icon-question" style="font-size: 20px"></i>
                </el-tooltip>
              </h3>
            </div>
          </el-col>
          <el-col :span="12" class="text-right">
            <el-button
              round
              icon="el-icon-download"
              type="primary"
              @click="downloadCsv"
            >Download CSV</el-button>
            <router-link to="/users/create">
              <el-button round icon="el-icon-plus" type="success">Thêm mới</el-button>
            </router-link>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="text-right">
              <el-select v-model="filter.branch_id" class="filter" placeholder="Chi nhánh">
                <el-option
                  v-for="(type,index) in infoCompany.branches"
                  :label="type.name"
                  :value="type.id"
                  :key="index"
                ></el-option>
              </el-select>
              <el-cascader
                v-model="filter.group_id"
                :options="infoCompany.groups"
                :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                :change="handleGroupChange()"
                placeholder="Bộ phận"
                class="filter"
              ></el-cascader>
              <el-select v-model="filter.position_id" class="filter" placeholder="Vị trí">
                <el-option
                  v-for="(type,index) in infoCompany.positions"
                  :label="type.name"
                  :value="type.id"
                  :key="index"
                ></el-option>
              </el-select>
              <el-select
                v-model="filter.employee_type_id"
                class="filter"
                placeholder="Loại nhân viên"
              >
                <el-option
                  v-for="(type,index) in infoCompany.employee_types"
                  :label="type.name"
                  :value="type.id"
                  :key="index"
                ></el-option>
              </el-select>
              <el-input
                placeholder="Tìm kiếm"
                prefix-icon="el-icon-search"
                v-model="filter.search"
                class="filter"
              ></el-input>
              <el-button type="primary" icon="el-icon-search" @click="filterUsers()">Lọc</el-button>
            </div>
          </el-col>
        </el-row>

        <el-row>
          <h5>
            Số lượng:
            <span style="color: blue">{{users.length}}</span> thành viên
          </h5>
          <el-table
            :data="dataTable.length ? dataTable : getUsersDataTable"
            style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
            stripe
          >
            <el-table-column fixed prop="employee_code" label="Mã nhân viên" width="150"></el-table-column>
            <el-table-column fixed prop="name" label="Tên" width="200"></el-table-column>
            <el-table-column prop="email" label="Email" width="200"></el-table-column>
            <el-table-column prop="position.name" label="Vị trí" width="150"></el-table-column>
            <el-table-column prop="gender" label="Giới tính" width="100"></el-table-column>
            <el-table-column prop="birthday" label="Ngày sinh" width="150"></el-table-column>
            <el-table-column prop="employee_type.name" label="Loại nhân viên" width="200"></el-table-column>
            <el-table-column prop="group.name" label="Phòng ban" width="200"></el-table-column>
            <el-table-column prop="branch.name" label="Chi nhánh" width="150"></el-table-column>
            <el-table-column prop="phone_number" label="SĐT" width="150"></el-table-column>
            <el-table-column prop="current_address" label="Địa chỉ hiện tại" width="250"></el-table-column>
            <el-table-column prop="pernentment_address" label="Địa chỉ thường chú" width="250"></el-table-column>
            <el-table-column prop="identity_card_passport.code" label="CMND/Hộ chiếu" width="150"></el-table-column>
            <el-table-column fixed="right" label="Thao tác" width="200" align="center">
              <template slot-scope="scope" class="text-center">
                <router-link :to="'/users/edit/' + scope.row.id">
                  <el-button size="mini">
                    <i class="el-icon-edit"></i>
                  </el-button>
                </router-link>
                <router-link :to="'/users/' + scope.row.id">
                  <el-button size="mini" type="primary">
                    <i class="el-icon-view"></i>
                  </el-button>
                </router-link>
                <el-button
                  size="mini"
                  type="danger"
                  @click.native.prevent="deleteUser(scope.$index, scope.row)"
                >
                  <i class="el-icon-delete"></i>
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-row>
      </div>

      <div class="my-5 text-center">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page.sync="currentPage"
          :page-sizes="[5, 10, 20, 30]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next"
          :total="users.length"
        ></el-pagination>
      </div>
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
      filter: {
        branch_id: "",
        group_id: "",
        position_id: "",
        employee_type_id: "",
        search: ""
      },
      currentPage: 1,
      pageSize: 5,
      dataTable: [],
      info:
        "Danh sách thành viên có thể xem thông tin trong quyền của người dùng này"
    };
  },
  created() {
    if (Object.keys(this.$route.query).length !== 0) {
      if (this.$route.query.branch_id) {
        this.filter.branch_id = parseInt(this.$route.query.branch_id);
      }
    }
    this.filterUsers();
  },
  computed: mapState({
    users: state => state.users,
    infoCompany: state => state.infoCompany,
    getUsersDataTable(state) {
      return state.users.slice(0, 5);
    }
  }),
  methods: {
    forceFileDownload(response) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "users.xlsx"); //or any other extension
      document.body.appendChild(link);
      link.click();
    },
    downloadCsv() {
      axios({
        method: "post",
        url: "/api/users/export/csv/",
        responseType: "arraybuffer",
        data: {
          listUserIds: this.$store.getters.getListUserIds
        }
      })
        .then(response => {
          this.forceFileDownload(response);
        })
        .catch(() => console.log("error occured"));
    },
    handleGroupChange() {
      if (Array.isArray(this.filter.group_id)) {
        this.filter.group_id = this.filter.group_id[
          this.filter.group_id.length - 1
        ];
      }
    },
    handleSizeChange(val) {
      this.pageSize = val;
      this.dataTable = this.getDataTable();
    },
    handleCurrentChange(val) {
      this.currentPage = val;
      this.dataTable = this.getDataTable();
    },
    filterUsers() {
      axios
        .get("/api/users", {
          params: {
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            position_id: this.filter.position_id,
            employee_type_id: this.filter.employee_type_id,
            search: this.filter.search
          }
        })
        .then(response => {
          this.$store.dispatch("updateUsers", response.data);
          this.currentPage = 1;
          this.dataTable = this.getDataTable();
        });
    },
    getDataTable() {
      let begin = (this.currentPage - 1) * this.pageSize;
      let end = begin + this.pageSize;
      return this.users.slice(begin, end);
    },
    deleteUser(index, user) {
      this.$confirm(
        "Bạn có chắc chắn muốn xóa thành viên " +
          user.name +
          "(" +
          user.employee_code +
          ")" +
          " ?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      )
        .then(() => {
          this.$store.dispatch("deleteUser", user.id).then(
            response => {
              if (response.data.status === false) {
                this.error.message = response.data.message;
                this.$notify.error({
                  title: "Thất bại",
                  message: response.data.message,
                  position: "bottom-right"
                });
              }
              this.dataTable.splice(index, 1);
              this.$store.dispatch("fetch");
              this.$notify({
                title: "Hoàn thành",
                message: "Xóa thành viên thành công",
                type: "success",
                position: "bottom-right"
              });
            },
            error => {
              console.log(error);
            }
          );
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

<style>
.filter {
  max-width: 15%;
}
</style>