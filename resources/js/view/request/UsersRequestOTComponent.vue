<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>DANH SÁCH YÊU CẦU OT VÀ REMOTE</h2>
      </el-col>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="8">
        <el-select class="w-100" v-model="filter.branch_id" placeholder="Chi nhánh">
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
        ></el-cascader>
      </el-col>
      <el-col :span="8">
        <el-input
          placeholder="Tìm kiếm theo tên, mã nhân viên"
          prefix-icon="el-icon-search"
          v-model="filter.search"
        ></el-input>
      </el-col>
    </el-row>
    <el-row :gutter="20" class="text-right">
      <el-col :span="8">
        <el-select class="w-100" v-model="filter.status" placeholder="Chọn trạng thái">
          <el-option value="waiting" label="Đang chờ"></el-option>
          <el-option value="cancel" label="Hủy bỏ"></el-option>
          <el-option value="forward" label="Chuyển tiếp"></el-option>
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
        ></el-date-picker>
      </el-col>
      <el-col :span="4">
        <el-button class="w-100" type="primary" @click="filterFormRequests">Lọc</el-button>
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
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150"></el-table-column>
        <el-table-column label="Loại hình" class-name="text-center" width="120">
          <template slot-scope="scope">
            <div v-if="scope.row.type == 'RM'">Làm remote</div>
            <div v-if="scope.row.type == 'OT'">Làm ngoài giờ</div>
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

        <el-table-column label="Thời gian làm" width="150">
          <template slot-scope="scope">
            <div>{{scope.row.work_time_begin + " - " + scope.row.work_time_end }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Ngày" width="110">
          <template slot-scope="scope">
            <div>{{scope.row.work_date}}</div>
          </template>
        </el-table-column>
        <el-table-column
          property="range_time"
          label="Số phút đăng ký"
          width="150"
          class-name="text-center"
        ></el-table-column>
        <el-table-column property="reason" label="Lý do" width="120"></el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
            <el-tooltip content="Hủy bỏ" placement="top">
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
            </el-tooltip>
            <el-tooltip content="Từ chối" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="info"
                icon="el-icon-s-release"
                v-if="scope.row.status == 'forward'"
                @click.native.prevent="handleApprove(scope.row, scope.$index, 'refuse')"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Chấp nhận" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="success"
                icon="el-icon-s-claim"
                v-if="scope.row.status == 'forward'"
                @click.native.prevent="handleApprove(scope.row, scope.$index,'accept')"
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
    }
  },
  methods: {
    getFormRequest() {
      let that = this;
      axios.get("/api/form_requests/users/requests").then(response => {
        response.data.map(function(request) {
          if (request.type == "OT" || request.type == "RM") {
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
    filterFormRequests() {
      let form_requests = [];
      axios
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
            if (request.type == "OT" || request.type == "RM") {
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