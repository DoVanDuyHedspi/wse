<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Chấm công</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="p-4 mt-3">
      <div class="mb-2">
        <el-row :gutter="20">
          <el-col :span="24" class="text-center">
            <h2>BẢNG CHẤM CÔNG</h2>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="12">
            <el-popover placement="bottom" width="300" trigger="click">
              <el-row :gutter="20">
                <el-col :span="24" class="my-2 text-left">
                  <b class="mb-1">Chi nhánh</b>
                  <el-select
                    v-model="filter.branch_id"
                    class="w-100"
                    placeholder="Chi nhánh"
                    @change="filterEvents"
                    size="medium"
                  >
                    <el-option
                      v-for="(type,index) in infoCompany.branches"
                      :label="type.name"
                      :value="type.id"
                      :key="index"
                    ></el-option>
                  </el-select>
                </el-col>
                <el-col :span="24" class="my-2 text-left">
                  <b class="mb-1">Phòng ban</b>
                  <el-cascader
                    v-model="filter.group_id"
                    :options="infoCompany.groups"
                    :props="{ checkStrictly: true, label: 'name', value: 'id' }"
                    :change="handleGroupChange()"
                    placeholder="Phòng ban"
                    class="w-100"
                    size="medium"
                  ></el-cascader>
                </el-col>
                <el-col :span="24" class="my-2 text-left">
                  <b class="mb-1">Vị trí</b>
                  <el-select
                    v-model="filter.position_id"
                    class="w-100"
                    placeholder="Vị trí"
                    @change="filterEvents"
                    size="medium"
                  >
                    <el-option
                      v-for="(type,index) in infoCompany.positions"
                      :label="type.name"
                      :value="type.id"
                      :key="index"
                    ></el-option>
                  </el-select>
                </el-col>
                <el-col :span="24" class="my-2 text-left">
                  <b class="mb-1">Loại nhân viên</b>
                  <el-select
                    v-model="filter.employee_type_id"
                    class="w-100"
                    placeholder="Loại nhân viên"
                    @change="filterEvents"
                    size="medium"
                  >
                    <el-option
                      v-for="(type,index) in infoCompany.employee_types"
                      :label="type.name"
                      :value="type.id"
                      :key="index"
                    ></el-option>
                  </el-select>
                </el-col>
                <el-col :span="24" class="my-2 text-left">
                  <b class="mb-1">Tìm theo tên và mã số</b>
                  <el-autocomplete
                    class="inline-input w-100"
                    prefix-icon="el-icon-search"
                    v-model="filter.search"
                    :fetch-suggestions="querySearch"
                    placeholder="Tìm kiếm"
                    @select="handleSelect"
                    @change="filterEvents"
                    size="medium"
                  ></el-autocomplete>
                  <!-- <el-button type="primary" icon="el-icon-search" @click="filterEvents()">Lọc</el-button> -->
                </el-col>
              </el-row>
              <el-button slot="reference" size="medium">Lọc</el-button>
            </el-popover>
            <el-date-picker
              v-model="filter.month"
              type="month"
              size="medium"
              placeholder="Pick a month"
              format="MM-yyyy"
              value-format="DD-MM-yyyy"
              @change="filterEvents"
            ></el-date-picker>
          </el-col>
          <el-col :span="12" class="text-right">
            <el-dropdown size="medium" split-button type="primary">
              Chấm công
              <el-dropdown-menu slot="dropdown">
                <router-link to="/history_checkinout">
                  <el-dropdown-item>
                    <i class="el-icon-timer"></i> Lịch sử vào/ ra
                  </el-dropdown-item>
                </router-link>
              </el-dropdown-menu>
            </el-dropdown>
            <el-dropdown size="medium" split-button type="primary">
              Xuất excel
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>
                  <i class="el-icon-download"></i> Xuất bảng ngày công
                </el-dropdown-item>
                <el-dropdown-item>
                  <i class="el-icon-download"></i> Xuất bảng đi muộn/về sớm
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </el-col>
        </el-row>
        <el-row>
          <el-table
            :data="dataTable.length ? dataTable : getUsersDataTable"
            style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
            stripe
            border
            header-cell-class-name="text-center"
            v-loading="loading"
            element-loading-text="Loading..."
            element-loading-spinner="el-icon-loading"
          >
            <el-table-column
              fixed
              prop="employee_code"
              label="Mã nhân viên"
              width="150"
              class-name="text-center"
            ></el-table-column>
            <el-table-column fixed property="name" label="Tên nhân viên" width="150">
              <template slot-scope="scope">
                <router-link :to="'/users/'+scope.row.id">
                  <span>{{scope.row.name}}</span>
                </router-link>
              </template>
            </el-table-column>
            <el-table-column
              prop="number_working_days"
              label="Số ngày làm"
              class-name="text-center"
              width="100"
            ></el-table-column>
            <el-table-column
              prop="number_penalty"
              label="Tổng số lỗi"
              class-name="text-center text-red"
              width="100"
            ></el-table-column>
            <!-- <el-table-column prop="ILM" label="ILM" class-name="text-center" width="60"></el-table-column>
            <el-table-column prop="LEM" label="LEM" class-name="text-center" width="60"></el-table-column>
            <el-table-column prop="ILA" label="ILA" class-name="text-center" width="60"></el-table-column>
            <el-table-column prop="LEA" label="LEA" class-name="text-center" width="60"></el-table-column>-->
            <el-table-column
              v-for="(date, index) in dateOfMonth"
              :key="index"
              :label="getDay(date)"
              width="135"
              class-name="text-center"
            >
              <template slot-scope="scope" class="text-center">
                <el-button
                  plain
                  size="mini"
                  type="warning"
                  @click="handleOpen(scope.row, scope.row.events[index])"
                  v-if="isSunday(date)"
                >{{scope.row.events[index].time_in}} | {{scope.row.events[index].time_out}}</el-button>
                <el-button
                  plain
                  size="mini"
                  type="success"
                  @click="handleOpen(scope.row, scope.row.events[index])"
                  v-else-if="scope.row.events[index].type === null"
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
              :picker-options="{start: '07:45',step: '00:15',end: '18:45'}"
            ></el-time-select>
            <el-time-select
              placeholder="Thời gian về"
              v-model="form.time_out"
              :picker-options="{start: '07:45',step: '00:15',end: '18:45',minTime: form.time_in}"
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
    <div id="footer" class="p-3">
      <span class="mr-3">
        <span style="background: #E6A23C" class="dot"></span> Không tính công
      </span>
      <span class="mr-3">
        <span style="background: #F56C6C" class="dot"></span>Vào trễ, ra sớm
      </span>
      <span class="mr-3">
        <span style="background: #FFFFFF; border: 1px black solid" class="dot"></span>Chấm công đúng giờ
      </span>
      <span class="mr-3">
        <span style="background: #67C23A" class="dot"></span>Không chấm công/Không đủ thời gian làm tối thiểu
      </span>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import moment from "moment";
export default {
  data() {
    return {
      error: {
        message: ""
      },
      loading: false,
      filter: {
        branch_id: "",
        group_id: "",
        position_id: "",
        employee_type_id: "",
        search: "",
        month: moment().format("DD-MM-YYYY")
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
    if (Object.keys(this.$route.query).length !== 0) {
      console.log("test");
      if (this.$route.query.branch_id) {
        this.filter.branch_id = parseInt(this.$route.query.branch_id);
      }
      this.filterEvents();
    }
  },
  computed: mapState({
    users: state => state.users_timesheets,
    infoCompany: state => state.infoCompany,
    getUsersDataTable(state) {
      return state.users_timesheets.slice(0, this.pageSize);
    },
    listSuggestions() {
      return this.$store.getters.getListSuggestions;
    },
    dateOfMonth() {
      return this.$store.getters.getDateOfMonth;
    }
  }),
  methods: {
    querySearch(queryString, cb) {
      var suggestions = this.listSuggestions;
      var results = queryString
        ? suggestions.filter(this.createFilter(queryString))
        : suggestions;
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
      this.filterEvents();
    },
    isSunday(date) {
      var myDate = moment(date, "DD-MM-YYYY").format("MM-DD-YYYYY");
      myDate = new Date(myDate);
      if (myDate.getDay() == 0) {
        return true;
      } else {
        return false;
      }
    },
    getDay(date) {
      var myDate = moment(date, "DD-MM-YYYY").format("MM-DD-YYYYY");
      myDate = new Date(myDate);
      var day = myDate.getDay();
      switch (day) {
        case 0:
          return date + " CN";
        case 1:
          return date + " T2";
        case 2:
          return date + " T3";
        case 3:
          return date + " T4";
        case 4:
          return date + " T5";
        case 5:
          return date + " T6";
        case 6:
          return date + " T7";
      }
    },
    handleGroupChange() {
      if (Array.isArray(this.filter.group_id)) {
        this.filter.group_id = this.filter.group_id[
          this.filter.group_id.length - 1
        ];
        this.filterEvents();
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
    filterEvents: async function() {
      this.loading = true;
      await axios
        .get("/api/events", {
          params: {
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            position_id: this.filter.position_id,
            employee_type_id: this.filter.employee_type_id,
            search: this.filter.search,
            month: this.filter.month
          }
        })
        .then(response => {
          this.$store.dispatch("updateUsersTimesheets", response.data.data);
          this.currentPage = 1;
        });
      this.dataTable = this.getDataTable();
      this.loading = false;
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
    updateEvent: async function() {
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
            this.drawer = false;
          }
        });
      }
      await this.$store.dispatch("fetchUsersTimesheets", this.filter);
      this.dataTable = this.getDataTable();
    }
  }
};
</script>

<style lang="scss" scope>
.filter {
  width: 100%;
}

.text-red {
  color: red;
}
.th-date {
  background: #e3e3e3;
}
#footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  background: white;
  border-top: 1px solid rgba(128, 128, 128, 0.3);
  padding-left: 10% !important;
}
.dot {
  height: 10px;
  width: 10px;
  margin-right: 10px;
  border-radius: 100%;
  display: inline-block;
}
</style>