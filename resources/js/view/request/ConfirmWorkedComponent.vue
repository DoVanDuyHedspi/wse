<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>XÁC NHẬN ĐÃ THỰC HIỆN OT VÀ REMOTE</h2>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="24" class="text-center">
        <h4>Ngày: {{confirm_date}}</h4>
      </el-col>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="5">
        <el-select class="w-100" v-model="filter.branch_id" placeholder="Chi nhánh">
          <el-option
            v-for="(type,index) in infoCompany.branches"
            :label="type.name"
            :value="type.id"
            :key="index"
          ></el-option>
        </el-select>
      </el-col>
      <el-col :span="5">
        <el-cascader
          v-model="filter.group_id"
          :options="infoCompany.groups"
          :props="{ checkStrictly: true, label: 'name', value: 'id' }"
          :change="handleGroupChange()"
          placeholder="Bộ phận"
          class="w-100"
        ></el-cascader>
      </el-col>
      <el-col :span="5">
        <el-input
          placeholder="Tìm kiếm theo tên, mã nhân viên"
          prefix-icon="el-icon-search"
          v-model="filter.search"
        ></el-input>
      </el-col>
      <el-col :span="6">
        <el-date-picker
          v-model="filter.date"
          type="date"
          format="dd-MM-yyyy"
          value-format="dd-MM-yyyy"
          placeholder="Ngày ot, remote"
          class="w-100"
        ></el-date-picker>
      </el-col>
      <el-col :span="3">
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
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150">
          <template slot-scope="scope">
            <router-link :to="'/users/'+scope.row.user.id">
              <span>{{scope.row.user.name}}</span>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column label="Loại hình" class-name="text-center" width="120">
          <template slot-scope="scope">
            <div v-if="scope.row.type == 'RM'">Làm remote</div>
            <div v-if="scope.row.type == 'OT'">Làm ngoài giờ</div>
          </template>
        </el-table-column>
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
        <el-table-column property="created_at" label="Thời gian tạo" width="120"></el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="160">
          <template slot-scope="scope">
            <el-tooltip content="Xác nhận đã làm" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="success"
                icon="el-icon-s-claim"
                @click.native.prevent="handleConfirm(scope.row, scope.$index)"
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
import moment from "moment";
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
      confirm_date: moment().format('DD-MM-YYYY'),
      filter: {
        date: "",
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
      axios.get("/api/form_requests/users/ot_rm").then(response => {
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
        .get("/api/form_requests/users/ot_rm", {
          params: {
            date: this.filter.date,
            branch_id: this.filter.branch_id,
            group_id: this.filter.group_id,
            search: this.filter.search
          }
        })
        .then(response => {
          this.form_requests = response.data;
        });
      
      this.dataTable = this.getDataTable();
      this.confirm_date = this.filter.date ? this.filter.date : this.confirm_date;
    },
    handleConfirm: async function(form_request, index) {
      await this.$confirm(
        "Bạn có chắc chắn muốn xác nhận nhân viên đã làm việc?",
        "Cảnh báo",
        {
          confirmButtonText: "Đồng ý",
          cancelButtonText: "Hủy",
          type: "warning"
        }
      ).then( async () => {
        await axios
          .post("/api/form_requests/users/ot_rm/confirm", {
            request_id: form_request.id,
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
              this.form_requests.splice(form_index, 1);
              this.$notify({
                title: "Hoàn thành",
                message: "Cập nhật thành công",
                type: "success",
                position: "bottom-right"
              });
            }
          });
      });
      this.dataTable = this.getDataTable();
    }
  }
};
</script>

<style scope>
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