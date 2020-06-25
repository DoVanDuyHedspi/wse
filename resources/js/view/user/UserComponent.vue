<template>
  <div>
    <!-- <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Thành viên</el-breadcrumb-item>
      </el-breadcrumb>
    </div>-->
    <div class="p-4">
      <div class="mb-2">
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="grid-content text-center">
              <h2>
                Danh sách thành viên
              </h2>
            </div>
          </el-col>
          <el-col :span="24" class="text-right">
            <router-link to="/users/create">
              <el-button icon="el-icon-plus" type="primary" size="medium">Thêm mới</el-button>
            </router-link>
            <el-dropdown size="medium" split-button type="primary">
              Xuất 
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>
                  <span @click="downloadCsv('xlsx')">
                    <i class="el-icon-download"></i> Xuất xlsx
                  </span>
                </el-dropdown-item>
                <el-dropdown-item>
                  <span @click="downloadCsv('csv')">
                    <i class="el-icon-download"></i> Xuất csv
                  </span>
                </el-dropdown-item>
                <!-- <el-dropdown-item>
                  <span @click="downloadCsv('pdf')">
                    <i class="el-icon-download"></i> In pdf
                  </span>
                </el-dropdown-item> -->
              </el-dropdown-menu>
            </el-dropdown>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="text-right">
              <el-select
                v-model="filter.branch_id"
                class="filter"
                placeholder="Chi nhánh"
                size="medium"
              >
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
                size="medium"
              ></el-cascader>
              <el-select
                v-model="filter.position_id"
                class="filter"
                placeholder="Vị trí"
                size="medium"
              >
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
                size="medium"
              >
                <el-option
                  v-for="(type,index) in infoCompany.employee_types"
                  :label="type.name"
                  :value="type.id"
                  :key="index"
                ></el-option>
              </el-select>
              <el-autocomplete
                class="inline-input"
                prefix-icon="el-icon-search"
                v-model="filter.search"
                :fetch-suggestions="querySearch"
                placeholder="Tìm kiếm"
                @select="handleSelect"
                @change="filterUsers"
                size="medium"
              ></el-autocomplete>
              <el-button type="primary" icon="el-icon-search" @click="filterUsers()" size="medium"></el-button>
            </div>
          </el-col>
        </el-row>

        <el-table
          :data="dataTable"
          style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
          stripe
          id="printMe"
        >
          <el-table-column fixed prop="employee_code" label="Mã nhân viên" width="120"></el-table-column>
          <el-table-column fixed property="name" label="Tên nhân viên" width="150">
            <template slot-scope="scope">
              <router-link :to="'/users/'+scope.row.id">
                <span>{{scope.row.name}}</span>
              </router-link>
            </template>
          </el-table-column>
          <el-table-column prop="email" label="Email" width="200"></el-table-column>
          <el-table-column prop="group.name" label="Phòng ban" width="150"></el-table-column>
          <el-table-column prop="position.name" label="Vị trí" width="120"></el-table-column>
          <el-table-column prop="gender" label="Giới tính" width="100"></el-table-column>
          <el-table-column prop="birthday" label="Ngày sinh" width="150"></el-table-column>
          <el-table-column prop="employee_type.name" label="Loại nhân viên" width="200"></el-table-column>
          
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
      users: [],
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
      pageSize: 10,
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
    // users: state => state.users,
    infoCompany: state => state.infoCompany,
    listSuggestions() {
      return this.$store.getters.getListSuggestions;
    }
  }),
  methods: {
    querySearch(queryString, cb) {
      var suggestions = this.listSuggestions;
      var results = queryString
        ? suggestions.filter(this.createFilter(queryString))
        : suggestions;
      // call callback function to return suggestions
      console.log(results);
      cb(results);
    },
    createFilter(queryString) {
      return suggestion => {
        return (
          suggestion.value.toLowerCase().indexOf(queryString.toLowerCase()) !==
          -1
        );
      };
    },
    handleSelect(item) {
      this.filter.search = this.filter.search.split(" ")[0];
      this.filterUsers();
    },
    forceFileDownload(response, type) {
      // if (type == pdf) {
      //   const url = window.URL.createObjectURL(
      //     new Blob([response.data], { type: "application/pdf" })
      //   );
      //   printJS(pdfUrl);
      // } else {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        if (type == "csv") {
          link.setAttribute("download", "users.csv");
        } else if (type == "xlsx") {
          link.setAttribute("download", "users.xlsx");
        } 

        document.body.appendChild(link);
        link.click();
      // }
    },
    downloadCsv(type) {
      axios({
        method: "post",
        url: "/api/users/export/users/",
        responseType: "arraybuffer",
        data: {
          listUserIds: this.$store.getters.getListUserIds('users'),
          type: type
        }
      })
        .then(response => {
          this.forceFileDownload(response, type);
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
          // this.$store.dispatch("updateUsers", response.data);
          this.users = response.data;
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