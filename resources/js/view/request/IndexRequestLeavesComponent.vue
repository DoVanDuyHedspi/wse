<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>DANH SÁCH YÊU CẦU</h2>
      </el-col>
      <el-col :span="24" class="text-right">
        <router-link to="/request_leaves/new">
          <el-button type="success" round>
            <i class="el-icon-plus"></i>Thêm mới
          </el-button>
        </router-link>
      </el-col>
      <el-col :span="24"></el-col>
    </el-row>
    <el-row :span="24" class="text-right">
      <el-select v-model="filter.status" placeholder="Chọn trạng thái">
        <el-option value="waiting" label="Đang chờ"></el-option>
        <el-option value="cancel" label="Hủy bỏ"></el-option>
        <el-option value="forward" label="Chuyển tiếp"></el-option>
        <el-option value="accept" label="Chấp nhận"></el-option>
        <el-option value="refuse" label="Từ chối"></el-option>
      </el-select>
      <el-date-picker v-model="filter.month" format="MM-yyyy" type="month" placeholder="Chọn tháng"></el-date-picker>
      <el-button type="primary" @click="filterFormRequests">Lọc</el-button>
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
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150"></el-table-column>
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
            <span v-if="scope.row.status == 'cancel'">
              <el-tag type="danger">Hủy bỏ</el-tag>
            </span>
            <span v-if="scope.row.status == 'forward'">
              <el-tag>Chuyển tiếp</el-tag>
            </span>
            <span v-if="scope.row.status == 'accept'">
              <el-tag type="success">Chấp nhận</el-tag>
            </span>
            <span v-if="scope.row.status == 'refuse'">
              <el-tag type="info">Từ chối</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column property="created_at" label="Thời gian tạo" width="120"></el-table-column>
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
        <el-table-column property="reason" label="Lý do" width="120"></el-table-column>
        <el-table-column label="Làm bù" width="150">
          <template
            slot-scope="scope"
            v-if="['ILM','ILA','LEM','LEA','LO'].includes(scope.row.type)"
          >
            <div>{{scope.row.work_time_begin + " - " + scope.row.work_time_end + " " + scope.row.work_date}}</div>
          </template>
        </el-table-column>

        <el-table-column
          property="range_time"
          label="Thời gian (phút)"
          width="70"
          class-name="text-center"
        ></el-table-column>

        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
            <el-button-group>
              <router-link :to="'/request_leaves/edit/' + scope.row.id">
                <el-button
                  size="mini"
                  type="primary"
                  icon="el-icon-edit"
                  v-if="scope.row.status == 'waiting'"
                ></el-button>
              </router-link>
              <el-button
                size="mini"
                type="danger"
                icon="el-icon-delete"
                v-if="scope.row.status == 'waiting'"
                @click.native.prevent="deleteFormRequest(scope.$index, scope.row)"
              ></el-button>
              <el-tooltip content="Đã xử lý" placement="top">
                <el-button
                  class="mx-0 my-1"
                  size="mini"
                  icon="el-icon-s-check"
                  disabled
                  v-if="scope.row.status == 'accept' || scope.row.status == 'refuse' || scope.row.status == 'cancel'"
                ></el-button>
              </el-tooltip>
            </el-button-group>
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
export default {
  data() {
    return {
      form_requests: [],
      currentPage: 1,
      pageSize: 5,
      dataTable: [],
      filter: {
        month: "",
        status: ""
      }
    };
  },
  created() {
    this.getFormRequest();
    this.dataTable = this.getDataTable();
  },
  computed: {
    getFormRequestData() {
      return this.getDataTable();
    }
  },
  methods: {
    getFormRequest() {
      let that = this;
      axios.get("/api/form_requests").then(response => {
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
    filterFormRequests() {
      let form_requests = [];
      axios
        .get("/api/form_requests", {
          params: {
            month: this.filter.month,
            status: this.filter.status
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
    deleteFormRequest(index, form_request) {
      this.$confirm(
        "Bạn có chắc chắn muốn xóa yêu cầu này không?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      ).then(() => {
        axios.delete("/api/form_requests/" + form_request.id).then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
            this.$notify.error({
              title: "Thất bại",
              message: response.data.message,
              position: "bottom-right"
            });
          }
          this.dataTable.splice(index, 1);
          this.form_requests.splice(
            (this.currentPage - 1) * this.pageSize + index,
            1
          );
          this.$notify({
            title: "Hoàn thành",
            message: "Xóa yêu cầu thành công",
            type: "success",
            position: "bottom-right"
          });
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