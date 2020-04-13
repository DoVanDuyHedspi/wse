<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý bảng thời gian</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="p-4 mt-3">
      <div class="mb-2">
        <el-row :gutter="20">
          <el-col :span="24" class="text-center">
            <h2>QUẢN LÝ BẢNG THỜI GIAN</h2>
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
              <el-button type="primary" icon="el-icon-search" @click="filterEvents()">Lọc</el-button>
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
            border
            header-cell-class-name="text-center"
          >
            <el-table-column
              fixed
              prop="employee_code"
              label="Mã nhân viên"
              width="150"
              class-name="text-center"
            ></el-table-column>
            <el-table-column fixed prop="name" label="Tên" width="150"></el-table-column>
            <el-table-column
              fixed
              prop="number_working_days"
              label="Số ngày làm"
              class-name="text-center"
              width="100"
            ></el-table-column>
            <el-table-column
              fixed
              prop="number_penalty"
              label="Tổng số lỗi"
              class-name="text-center text-red"
              width="100"
            ></el-table-column>
            <el-table-column fixed prop="ILM" label="ILM" class-name="text-center" width="60"></el-table-column>
            <el-table-column fixed prop="LEM" label="LEM" class-name="text-center" width="60"></el-table-column>
            <el-table-column fixed prop="ILA" label="ILA" class-name="text-center" width="60"></el-table-column>
            <el-table-column fixed prop="LEA" label="LEA" class-name="text-center" width="60"></el-table-column>
            <el-table-column
              v-for="(date, index) in month"
              :key="index"
              :label="date"
              width="135"
              class-name="text-center"
            >
              <template slot-scope="scope" class="text-center">
                <el-button
                  plain
                  size="mini"
                  type="success"
                  @click="handleOpen(scope.row, scope.row.events[index])"
                  v-if="scope.row.events[index].type === null"
                >{{scope.row.events[index].time_in}} | {{scope.row.events[index].time_out}}</el-button>
                <el-button
                  plain
                  size="mini"
                  type="danger"
                  @click="handleOpen(scope.row, scope.row.events[index])"
                  v-else-if="scope.row.events[index].status == 1"
                >{{scope.row.events[index].time_in}} | {{scope.row.events[index].time_out}}</el-button>
                <el-button
                  plain
                  size="mini"
                  @click="handleOpen(scope.row, scope.row.events[index])"
                  v-else
                >{{scope.row.events[index].time_in}} | {{scope.row.events[index].time_out}}</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-row>
      </div>
      <el-drawer
        title="Sửa thời gian chấm công"
        :visible.sync="drawer"
        direction="ltr"
        :before-close="handleClose"
        size="40%"
      >
        <div class="m-3">
          <div>
            <b>Nhân viên: {{form.user_name}}</b>
          </div>
          <div>
            <b>Mã số nhân viên: {{form.user_code}}</b>
          </div>
          <div>
            <b>Ngày thực hiện: {{form.date}}</b>
          </div>
          <div class="my-2">
            <el-time-select
              placeholder="Thời gian đến"
              v-model="form.time_in"
              :picker-options="{
      start: '07:45',
      step: '00:15',
      end: '18:45'
    }"
            ></el-time-select>
            <el-time-select
              placeholder="Thời gian về"
              v-model="form.time_out"
              :picker-options="{
      start: '07:45',
      step: '00:15',
      end: '18:45',
      minTime: form.time_in
    }"
            ></el-time-select>
          </div>
          <div class="text-center mt-3">
            <el-button type="primary" @click="updateEvent">Cập nhật</el-button>
          </div>
        </div>
      </el-drawer>

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
      month: [],
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
      drawer: false,
      form: {
        time_in: "",
        time_out: "",
        user_name: "",
        user_code: "",
        date: ""
      }
    };
  },
  created() {
    this.$store.dispatch("fetchUsersTimesheets");
    if(Object.keys(this.$route.query).length !== 0){
      if(this.$route.query.branch_id){
        this.filter.branch_id = parseInt(this.$route.query.branch_id);
      }
    }
    this.filterEvents();
  },
  computed: mapState({
    users: state => state.users_timesheets,
    infoCompany: state => state.infoCompany,
    getUsersDataTable(state) {
      this.month = this.$store.getters.getDateOfMonth;
      return state.users_timesheets.slice(0, this.pageSize);
    }
  }),
  methods: {
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
    filterEvents() {
      axios
        .get("/api/events", {
          params: {
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            position_id: this.filter.position_id,
            employee_type_id: this.filter.employee_type_id,
            search: this.filter.search
          }
        })
        .then(response => {
          this.$store.dispatch("updateUsersTimesheets", response.data.data);
          this.currentPage = 1;
          this.dataTable = this.getDataTable();
        });
    },
    getDataTable() {
      let begin = (this.currentPage - 1) * this.pageSize;
      let end = begin + this.pageSize;
      return this.users.slice(begin, end);
    },
    handleClose(done) {
      this.$confirm("Bạn muốn hủy cập nhập?")
        .then(_ => {
          done();
        })
        .catch(_ => {});
    },
    handleOpen(user, event) {
      this.drawer = true;
      this.form.user_name = user.name;
      this.form.user_code = user.employee_code;
      this.form.date = event.date;
      this.form.time_in = event.time_in;
      this.form.time_out = event.time_out;
    },
    updateEvent() {
      if (this.form.time_in == "" || this.form.time_out == "") {
        this.$notify.error({
          title: "Thất bại",
          message: "Hãy điền đủ thời gian đến và về",
          position: "bottom-right"
        });
      } else {
        axios.post("/api/events", this.form).then(response => {
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
              message: "Cập nhật thành công!",
              type: "success",
              position: "bottom-right"
            });
          }
          this.drawer = false;
          this.$store.dispatch("fetchUsersTimesheets");
        });
      }
    }
  }
};
</script>

<style lang="scss">
.filter {
  max-width: 15%;
}

.text-red {
  color: red;
}
</style>