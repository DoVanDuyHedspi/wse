<template>
  <div class="p-3">
    <el-row class="my-2">
      <el-col :span="24">
        <!-- <h2>DANH SÁCH YÊU CẦU IL, LE, LO, QQ</h2> -->
        <div>
          <router-link to="/users_requests/request_check_camera">
            <el-button type="default" size="medium">Yêu cầu khiếu nại</el-button>
          </router-link>
          <router-link to="/users_requests/leave">
            <el-button type="default" size="medium">Yêu cầu nghỉ phép</el-button>
          </router-link>
          <router-link to="/users_requests/ot_remote">
            <el-button type="default" size="medium">Yêu cầu OT, Remote</el-button>
          </router-link>
          <el-button type="primary" size="medium">Yêu cầu khác</el-button>
        </div>
        <el-divider></el-divider>
      </el-col>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="8">
        <el-select class="w-100" v-model="filter.branch_id" placeholder="Chi nhánh" size="medium">
          <el-option
            v-for="(type,index) in infoCompany.branches"
            :label="type.name"
            :value="type.id"
            :key="index"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="8">
        <el-cascader
          v-model="filter.group_id"
          :options="infoCompany.groups"
          :props="{ checkStrictly: true, label: 'name', value: 'id' }"
          :change="handleGroupChange()"
          placeholder="Bộ phận"
          class="w-100"
          size="medium"
        ></el-cascader>
      </el-col>
      <el-col :span="8">
        <el-autocomplete
          class="inline-input w-100"
          prefix-icon="el-icon-search"
          v-model="filter.search"
          :fetch-suggestions="querySearch"
          placeholder="Tìm kiếm theo tên, mã nhân viên"
          @select="handleSelect"
          size="medium"
        ></el-autocomplete>
      </el-col>
    </el-row>
    <el-row :gutter="20" class="text-right">
      <el-col :span="8">
        <el-select
          class="w-100"
          v-model="filter.status"
          placeholder="Chọn trạng thái"
          size="medium"
        >
          <el-option value="waiting" label="Đang chờ"></el-option>
          <!-- <el-option value="cancel" label="Hủy bỏ"></el-option>
          <el-option value="forward" label="Chuyển tiếp"></el-option>-->
          <el-option value="accept" label="Chấp nhận"></el-option>
          <el-option value="refuse" label="Từ chối"></el-option>
        </el-select>
      </el-col>
      <el-col :span="12">
        <el-date-picker
          v-model="filter.range_date"
          type="daterange"
          range-separator="-"
          start-placeholder="Ngày bắt đầu"
          end-placeholder="Ngày kết thúc"
          format="dd-MM-yyyy"
          class="w-100"
          size="medium"
        ></el-date-picker>
      </el-col>
      <el-col :span="4">
        <el-button class="w-100" type="primary" @click="filterFormRequests" size="medium">Lọc</el-button>
      </el-col>
    </el-row>

    <el-row>
      <el-table
        ref="multipleTable"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        border
        :data="dataTable.length ? dataTable : getFormRequestData"
      >
        <!-- <el-table-column type="selection" width="55"></el-table-column> -->
        <el-table-column fixed property="user_code" label="Mã nhân viên" width="120"></el-table-column>
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150">
          <template slot-scope="scope">
            <router-link :to="'/users/'+scope.row.user.id">
              <span>{{scope.row.user.name}}</span>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column label="Loại hình" class-name="text-center" width="120">
          <template slot-scope="scope">
            <div v-if="scope.row.type == 'ILM'">Đến muộn (sáng)</div>
            <div v-if="scope.row.type == 'ILA'">Đến muộn (chiều)</div>
            <div v-if="scope.row.type == 'LO'">Rời khỏi vị trí</div>
            <div v-if="scope.row.type == 'LEM'">Về sớm (sáng)</div>
            <div v-if="scope.row.type == 'LEA'">Về sớm (chiều)</div>
            <div v-if="scope.row.type == 'QQD'">Quên chấm (sáng)</div>
            <div v-if="scope.row.type == 'QQV'">Quên chấm (chiều)</div>
            <div v-if="scope.row.type == 'QQF'">Quên chấm (cả ngày)</div>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" width="120" class-name="text-center">
          <template slot-scope="scope">
            <span v-if="scope.row.status == 'waiting'">
              <el-tag type="warning">Đang chờ</el-tag>
            </span>
            <!-- <span v-if="scope.row.status == 'cancel'">
              <el-tag type="danger">Hủy bỏ</el-tag>
            </span>
            <span v-if="scope.row.status == 'forward'">
              <el-tag>Chuyển tiếp</el-tag>
            </span>-->
            <span v-if="scope.row.status == 'accept'">
              <el-tag type="success">Chấp nhận</el-tag>
            </span>
            <span v-if="scope.row.status == 'refuse'">
              <el-tag type="info">Từ chối</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian xin nghỉ" width="170">
          <template slot-scope="scope">
            <div
              v-if="['ILM','ILA','LEM','LEA','LO'].includes(scope.row.type)"
            >{{scope.row.leave_time_begin + " - " + scope.row.leave_time_end + " " + scope.row.leave_date}}</div>
            <div
              v-else-if="scope.row.type == 'QQD'"
            >{{scope.row.work_time_begin + " " + scope.row.work_date }}</div>
            <div
              v-else-if="scope.row.type == 'QQV'"
            >{{scope.row.work_time_end + " " + scope.row.work_date }}</div>
            <div
              v-else-if="scope.row.type == 'QQF'"
            >{{scope.row.work_time_begin + " - " + scope.row.work_time_end + " " + scope.row.work_date }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian làm bù" width="150">
          <template
            slot-scope="scope"
            v-if="['ILM','ILA','LEM','LEA','LO'].includes(scope.row.type)"
          >
            <div>{{scope.row.work_time_begin + " - " + scope.row.work_time_end + " " + scope.row.work_date}}</div>
          </template>
        </el-table-column>
        <el-table-column property="reason" label="Lý do" width="200"></el-table-column>
        <el-table-column
          property="range_time"
          label="Thời gian (phút)"
          width="70"
          class-name="text-center"
        ></el-table-column>
        <el-table-column property="created_at" label="Thời gian tạo" width="120"></el-table-column>
        <el-table-column label="Đã làm bù?" width="120" class-name="text-center">
          <template
            slot-scope="scope"
            v-if="['ILM','ILA','LEM','LEA','LO'].includes(scope.row.type)"
          >
            <div v-if="scope.row.has_worked">
              <el-tag type="success">Đã làm</el-tag>
            </div>
            <div v-else>
              <el-tag type="warning">Chưa làm</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
            <!-- <el-tooltip content="Hủy bỏ" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="danger"
                icon="el-icon-close"
                v-if="scope.row.status == 'waiting'"
                @click.native.prevent="handleApprove(scope.row, scope.$index, 'cancel')"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Chuyển tiếp" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="warning"
                icon="el-icon-s-promotion"
                v-if="scope.row.status == 'waiting'"
                @click.native.prevent="handleApprove(scope.row, scope.$index, 'forward')"
              ></el-button>
            </el-tooltip>-->
            <el-tooltip content="Từ chối" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="info"
                icon="el-icon-s-release"
                v-if="scope.row.status == 'waiting'"
                @click.native.prevent="handleApprove(scope.row, scope.$index, 'refuse')"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Chấp nhận" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="success"
                icon="el-icon-s-claim"
                v-if="scope.row.status == 'waiting'"
                @click.native.prevent="handleApprove(scope.row, scope.$index,'accept')"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Đã xử lý" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                icon="el-icon-s-check"
                disabled
                v-if="scope.row.status == 'accept' || scope.row.status == 'refuse'"
              ></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="my-5 text-center">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page.sync="currentPage"
          :page-sizes="[5, 10, 20, 30]"
          :page-size="pageSize"
          layout="total, sizes, prev, pager, next"
          :total="form_requests.length"
        ></el-pagination>
      </div>
    </el-row>
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
      form_requests: [],
      currentPage: 1,
      pageSize: 5,
      dataTable: [],
      filter: {
        range_date: "",
        status: "",
        branch_id: "",
        group_id: "",
        search: ""
      }
    };
  },
  created() {
    this.getFormRequest();
    this.dataTable = this.getDataTable();
  },
  computed: {
    ...mapState(["infoCompany"]),
    getFormRequestData() {
      return this.getDataTable();
    },
    listSuggestions() {
      return this.$store.getters.getListSuggestions;
    }
  },
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
    },
    getFormRequest() {
      let that = this;
      axios.get("/api/form_requests/users/requests").then(response => {
        response.data.map(function(request) {
          if (
            ["ILM", "ILA", "LEM", "LEA", "QQD", "QQV", "QQF", "LO"].includes(
              request.type
            )
          ) {
            that.form_requests.push(request);
          }
        });
      });
      this.form_requests = that.form_requests;
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
    getDataTable() {
      let begin = (this.currentPage - 1) * this.pageSize;
      let end = begin + this.pageSize;
      return this.form_requests.slice(begin, end);
    },
    filterFormRequests: async function() {
      let form_requests = [];
      await axios
        .get("/api/form_requests/users/requests", {
          params: {
            date_begin: this.filter.range_date ? this.filter.range_date[0] : "",
            date_end: this.filter.range_date ? this.filter.range_date[1] : "",
            status: this.filter.status,
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            search: this.filter.search
          }
        })
        .then(response => {
          response.data.map(function(request) {
            if (
              ["ILM", "ILA", "LEM", "LEA", "QQD", "QQV", "QQF", "LO"].includes(
                request.type
              )
            ) {
              form_requests.push(request);
            }
          });
        });
      this.form_requests = form_requests;
      this.dataTable = this.getDataTable();
    },
    handleApprove(form_request, index, action) {
      console.log(form_request.id);
      this.$confirm(
        "Bạn có chắc chắn muốn " + action + " yêu cầu này không?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      ).then(() => {
        axios
          .post("/api/form_requests/approve_request", {
            request_id: form_request.id,
            action: action
          })
          .then(response => {
            console.log(response.data.message);
            if (response.data.status === false) {
              this.error.message = response.data.message;
              this.$notify.error({
                title: "Thất bại",
                message: response.data.message,
                position: "bottom-right"
              });
            } else {
              let form_index = (this.currentPage - 1) * this.pageSize + index;
              this.form_requests[form_index].status = action;
              this.$notify({
                title: "Hoàn thành",
                message: "Cập nhật thành công",
                type: "success",
                position: "bottom-right"
              });
            }
          });
      });
    }
  }
};
</script>

<style>
.el-table th {
  font-size: initial;
  color: black;
}

.el-table .cell {
  word-break: break-word !important;
  /* text-transform: uppercase; */
}

h3,
h2 {
  font-weight: bolder;
}
</style>