<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>DANH SÁCH KHIẾU LẠI</h2>
      </el-col>
      <el-col :span="8" class="text-left">
        <router-link to="/request_check_camera/new">
          <el-button type="success" round>
            <i class="el-icon-plus"></i>Thêm mới
          </el-button>
        </router-link>
      </el-col>
      <el-col :span="16" class="text-right">
        <el-select v-model="filter.status" placeholder="Chọn trạng thái">
          <el-option value="waiting" label="Đang chờ"></el-option>
          <el-option value="accept" label="Chấp nhận"></el-option>
          <el-option value="refuse" label="Từ chối"></el-option>
        </el-select>
        <el-date-picker
          v-model="filter.month"
          format="MM-yyyy"
          type="month"
          placeholder="Chọn tháng"
        ></el-date-picker>
        <el-button type="primary" @click="filterFormRequests">Lọc</el-button>
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
        <el-table-column label="Trạng thái" width="120" class-name="text-center">
          <template slot-scope="scope">
            <span v-if="scope.row.status == 'waiting'">
              <el-tag type="warning">Đang chờ</el-tag>
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

        <el-table-column label="Khoảng thời gian" width="150">
          <template slot-scope="scope">
            <div>{{scope.row.begin_time + " - " + scope.row.end_time }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Ngày" width="110">
          <template slot-scope="scope">
            <div>{{scope.row.date}}</div>
          </template>
        </el-table-column>
        <el-table-column property="note" label="Ghi chú" width="120"></el-table-column>
        <el-table-column label="Kết quả" width="120" class-name="text-center">
          <template slot-scope="scope">
            <span v-if="scope.row.result == 'waiting'">
              <el-tag type="warning">Đang chờ</el-tag>
            </span>
            <span v-if="scope.row.result == 'success'">
              <el-tag type="success">Thành công</el-tag>
            </span>
            <span v-if="scope.row.result == 'fail'">
              <el-tag type="info">Thất bại</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column property="reply" label="Trả lời" width="120"></el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
            <router-link :to="'/request_check_camera/edit/' + scope.row.id">
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
      axios.get("/api/form_complain").then(response => {
        this.form_requests = response.data;
      });
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
      axios
        .get("/api/form_complain", {
          params: {
            month: this.filter.month,
            status: this.filter.status
          }
        })
        .then(response => {
          this.form_requests = response.data;
        });

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
        axios.delete("/api/form_complain/" + form_request.id).then(response => {
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