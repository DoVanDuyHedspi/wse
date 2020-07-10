<template>
  <div class="p-3">
    <el-row class="my-2">
      <el-col :span="24">
        <!-- <h2>DANH SÁCH YÊU CẦU</h2> -->
        <div>
          <router-link to="/request_check_camera">
            <el-button type="default" size="medium">Yêu cầu khiếu nại</el-button>
          </router-link>
          <el-button type="primary" size="medium">Yêu cầu nghỉ phép</el-button>
          <router-link to="/request_ot">
            <el-button type="default" size="medium">Yêu cầu OT, Remote</el-button>
          </router-link>
          <router-link to="/other_request">
            <el-button type="default" size="medium">Yêu cầu khác</el-button>
          </router-link>
        </div>
        <el-divider></el-divider>
      </el-col>
      <el-col :span="8" class="text-left">
        <router-link to="/request_leave/new">
          <el-button type="primary" size="medium">
            <i class="el-icon-plus"></i>Thêm mới
          </el-button>
        </router-link>
      </el-col>
      <el-col :span="16" class="text-right">
        <el-select v-model="filter.status" placeholder="Chọn trạng thái" size="medium">
          <el-option value="waiting" label="Đang chờ"></el-option>
          <!-- <el-option value="cancel" label="Hủy bỏ"></el-option>
          <el-option value="forward" label="Chuyển tiếp"></el-option>-->
          <el-option value="accept" label="Chấp nhận"></el-option>
          <el-option value="refuse" label="Từ chối"></el-option>
        </el-select>
        <el-date-picker
          v-model="filter.month"
          format="MM-yyyy"
          type="month"
          placeholder="Chọn tháng"
          size="medium"
        ></el-date-picker>
        <el-button type="primary" @click="filterFormLeaves" size="medium">Lọc</el-button>
      </el-col>
    </el-row>

    <el-row>
      <el-table
        ref="multipleTable"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        :data="dataTable.length ? dataTable : getFormLeavesData"
      >
        <!-- <el-table-column type="selection" width="55"></el-table-column> -->
        <!-- <el-table-column fixed property="user_code" label="Mã nhân viên" width="120"></el-table-column> -->
        <el-table-column property="user.name" label="Tên nhân viên"></el-table-column>
        <el-table-column label="Loại nghỉ phép" property="leave_type.slug" class-name="text-center"></el-table-column>
        <el-table-column label="Trạng thái" class-name="text-center">
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
        <el-table-column label="Thời gian xin nghỉ">
          <template slot-scope="scope">
            <div>{{scope.row.begin_leave_date}} -></div>
            <div>{{scope.row.end_leave_date}}</div>
          </template>
        </el-table-column>
        <el-table-column property="reason" label="Lý do"></el-table-column>
        <el-table-column property="created_at" label="Thời gian tạo"></el-table-column>
        <el-table-column align="center" label="Thao tác">
          <template slot-scope="scope">
            <!-- <el-button-group> -->
            <router-link :to="'/request_leave/edit/' + scope.row.id">
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
            <!-- </el-button-group> -->
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
          :total="form_leaves.length"
        ></el-pagination>
      </div>
    </el-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form_leaves: [],
      currentPage: 1,
      pageSize: 20,
      dataTable: [],
      filter: {
        month: "",
        status: ""
      }
    };
  },
  created() {
    this.getFormLeaves();
    this.dataTable = this.getDataTable();
  },
  computed: {
    getFormLeavesData() {
      return this.getDataTable();
    }
  },
  methods: {
    getFormLeaves() {
      axios.get("/api/form_leaves").then(response => {
        this.form_leaves = response.data;
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
      return this.form_leaves.slice(begin, end);
    },
    filterFormLeaves: async function() {
      await axios
        .get("/api/form_leaves", {
          params: {
            month: this.filter.month,
            status: this.filter.status
          }
        })
        .then(response => {
          this.form_leaves = response.data;
        });
      this.dataTable = this.getDataTable();
    },
    deleteFormRequest(index, form_leaves) {
      this.$confirm(
        "Bạn có chắc chắn muốn xóa yêu cầu này không?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      ).then(() => {
        axios.delete("/api/form_leaves/" + form_leaves.id).then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
            this.$notify.error({
              title: "Thất bại",
              message: response.data.message,
              position: "bottom-right"
            });
          } else {
            this.dataTable.splice(index, 1);
            this.form_leaves.splice(
              (this.currentPage - 1) * this.pageSize + index,
              1
            );
            this.$notify({
              title: "Hoàn thành",
              message: "Xóa yêu cầu thành công",
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