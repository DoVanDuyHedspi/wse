<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Lịch sử vào ra</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="p-4 mt-3">
      <div class="mb-2">
        <el-row :gutter="20">
          <el-col :span="24" class="text-center">
            <h2>LỊCH SỬ VÀO/RA</h2>
          </el-col>
          <el-col :span="24">
            <router-link to="/manage_timesheets">
              <el-button type="primary" size="medium">
                <i class="el-icon-s-management"></i> Bảng chấm công
              </el-button>
            </router-link>
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
              v-model="filter.date"
              type="date"
              size="medium"
              placeholder="Pick a date"
              format="dd-MM-yyyy"
              value-format="dd-MM-yyyy"
              @change="filterEvents"
            ></el-date-picker>
          </el-col>
          <el-col :span="12" class="text-right">
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
              </el-dropdown-menu>
            </el-dropdown>
            <el-button size="medium" type="primary" v-print="'#table'">
              <i class="el-icon-printer"></i> In
            </el-button>
          </el-col>
        </el-row>
        <el-row id="table">
          <el-table
            :data="dataTable"
            style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
            stripe
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
            <el-table-column fixed property="name" label="Tên nhân viên">
              <template slot-scope="scope">
                <router-link :to="'/users/'+scope.row.id">
                  <span>{{scope.row.name}}</span>
                </router-link>
              </template>
            </el-table-column>
            <el-table-column prop="group.name" label="Phòng ban"></el-table-column>
            <el-table-column prop="position.name" label="Chức vụ"></el-table-column>
            <el-table-column label="Ngày" class-name="text-center">
              <template slot-scope="scope">
                <div v-if="scope.row.event">{{scope.row.event.date}}</div>
                <div v-else>{{filter.date}}</div>
              </template>
            </el-table-column>
            <el-table-column label="Vào đầu tiên" class-name="text-center">
              <template slot-scope="scope">
                <div v-if="scope.row.event">{{scope.row.event.time_in}}</div>
                <div v-else>-</div>
              </template>
            </el-table-column>
            <el-table-column label="Ra cuối cùng" class-name="text-center">
              <template slot-scope="scope">
                <div v-if="scope.row.event && scope.row.event.time_out">{{scope.row.event.time_out}}</div>
                <div v-else>-</div>
              </template>
            </el-table-column>
            <!-- <el-table-column label="Số phút đi muộn/về sớm" class-name="text-center">
              <template slot-scope="scope">
                <div
                  v-if="scope.row.event && scope.row.event.fined_time"
                  class="text-red"
                >{{scope.row.event.fined_time}}</div>
                <div v-else>-</div>
              </template>
            </el-table-column> -->
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
import moment from "moment";
export default {
  data() {
    return {
      error: {
        message: ""
      },
      users: [],
      loading: false,
      filter: {
        branch_id: "",
        group_id: "",
        position_id: "",
        employee_type_id: "",
        search: "",
        date: moment().format("DD-MM-YYYY")
      },
      currentPage: 1,
      pageSize: 5,
      dataTable: []
    };
  },
  created: async function() {
    await axios.get("/api/events/daily/checkinout").then(response => {
      this.users = response.data;
    });
    this.dataTable = this.getDataTable();
  },
  computed: mapState({
    infoCompany: state => state.infoCompany,
    listSuggestions() {
      return this.$store.getters.getListSuggestions;
    }
  }),
  methods: {
    forceFileDownload(response, type) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      if (type == "csv") {
        link.setAttribute("download", "LichSuVaoRa.csv");
      } else if (type == "xlsx") {
        link.setAttribute("download", "LichSuVaoRa.xlsx");
      }

      document.body.appendChild(link);
      link.click();
    },
    downloadCsv(type) {
      axios({
        method: "post",
        url: "/api/users/export/checkInOut/",
        responseType: "arraybuffer",
        data: {
          listUserIds: this.$store.getters.getListUserIds("users"),
          type: type,
          date: this.filter.date,
        }
      })
        .then(response => {
          this.forceFileDownload(response, type);
        })
        .catch(() => console.log("error occured"));
    },
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
        .get("/api/events/daily/checkinout", {
          params: {
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            position_id: this.filter.position_id,
            employee_type_id: this.filter.employee_type_id,
            search: this.filter.search,
            date: this.filter.date
          }
        })
        .then(response => {
          this.users = response.data;
        });
      this.currentPage = 1;
      this.dataTable = this.getDataTable();
      this.loading = false;
    },
    getDataTable() {
      let begin = (this.currentPage - 1) * this.pageSize;
      let end = begin + this.pageSize;
      return this.users.slice(begin, end);
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