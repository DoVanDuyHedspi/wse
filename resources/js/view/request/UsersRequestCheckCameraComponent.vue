<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>DANH SÁCH KHIẾU LẠI CHẤM CÔNG</h2>
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
      <el-col :span="24" class="text-center">
        <router-link to="/users_requests/check_camera">
          <el-button type="success" icon="el-icon-video-play">Tiến hành xác minh</el-button>
        </router-link>   
      </el-col>
    </el-row>
    <el-row>
      <el-table
        ref="multipleTable"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        border
        :data="dataTable.length ? dataTable : getFormRequestData"
      >
        <el-table-column fixed property="user_code" label="Mã nhân viên" width="120"></el-table-column>
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150">
          <template slot-scope="scope">
            <router-link :to="'/users/'+scope.row.user.id">
              <span>{{scope.row.user.name}}</span>
            </router-link>
          </template>
        </el-table-column>
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
        <el-table-column property="created_at" label="Thời gian tạo" width="120"></el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
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
    }
  },
  methods: {
    getFormRequest() {
      axios.get("/api/form_complain/manage/requests").then(response => {
        this.form_requests = response.data;
      });
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
      await axios
        .get("/api/form_complain/manage/requests", {
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
          this.form_requests = response.data;
        });

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
          .post("/api/form_complain/manage/approve", {
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
              this.form_requests[form_index].status =
                response.data.form_complain.status;
              if (action == "refuse") {
                this.form_requests[form_index].result =
                  response.data.form_complain.result;
              }
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