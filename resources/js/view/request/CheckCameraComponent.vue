<template>
  <div class="p-3">
    <el-row class="my-4">
      <el-col :span="24" class="text-center">
        <h2>GIẢI QUYẾT KHIẾU LẠI</h2>
      </el-col>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="24" class="text-center">
        <el-date-picker
          v-model="date"
          type="date"
          format="dd-MM-yyyy"
          value-format="dd-MM-yyyy"
          placeholder="Ngày xác minh"
        ></el-date-picker>
        <el-button type="primary" @click="fetchData">Tìm</el-button>
      </el-col>
      <el-col :span="24">
        <h4>Ngày: {{date}}</h4>
      </el-col>
    </el-row>
    <el-row>
      <el-table
        ref="multipleTable"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        border
        :data="dataTable"
        v-loading="loading"
      >
        <el-table-column fixed property="user_code" label="Mã nhân viên" width="120"></el-table-column>
        <el-table-column fixed property="user.name" label="Tên nhân viên" width="150">
          <template slot-scope="scope">
            <router-link :to="'/users/'+scope.row.user_id">
              <span>{{scope.row.name}}</span>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian xác minh" width="150">
          <template slot-scope="scope">
            <div>{{scope.row.begin_time + " - " + scope.row.end_time }}</div>
          </template>
        </el-table-column>
        <!-- <el-table-column label="Ngày" width="110">
          <template slot-scope="scope">
            <div>{{scope.row.date}}</div>
          </template>
        </el-table-column>-->
        <el-table-column property="note" label="Ghi chú" width="200"></el-table-column>
        <el-table-column label="Kết quả ở máy chấm" width="110">
          <template slot-scope="scope">
            <div v-if="scope.row.search_info.length != 0">
              <div
                v-for="(info, index) in scope.row.search_info"
                :key="index"
              >{{scope.row.search_info[index]}}</div>
            </div>
            <div v-else>
              <div>
                <el-tag type="info">Không có</el-tag>
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column property="created_at" label="Thời gian tạo" width="120"></el-table-column>
        <el-table-column align="center" fixed="right" label="Thao tác" width="170">
          <template slot-scope="scope">
            <el-tooltip content="Thông tin nhân viên" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                icon="el-icon-user"
                @click="openUserInfo(scope.row)"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Thất bại" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="info"
                icon="el-icon-s-release"
                @click.native.prevent="handleApproveFail(scope.row, scope.$index)"
              ></el-button>
            </el-tooltip>
            <el-tooltip content="Thành công" placement="top">
              <el-button
                class="mx-0 my-1"
                size="mini"
                type="success"
                icon="el-icon-s-claim"
                @click.native.prevent="openApproveDrawer(scope.row, scope.$index)"
              ></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog title="Thông tin nhân viên" :visible.sync="dialogVisible" width="60%" center>
        <el-row :gutter="15">
          <el-col :span="12">
            <el-image :src="dialog.user_avatar"></el-image>
          </el-col>
          <el-col :span="12">
            <el-row :gutter="15">
              <el-col :span="10" class="label">Tên nhân viên:</el-col>
              <el-col :span="14">{{dialog.name}}</el-col>
            </el-row>
            <el-row :gutter="15">
              <el-col :span="10" class="label">Mã nhân viên:</el-col>
              <el-col :span="14">{{dialog.user_code}}</el-col>
            </el-row>
            <el-row :gutter="15">
              <el-col :span="10" class="label">Chi nhánh:</el-col>
              <el-col :span="14">{{dialog.branch}}</el-col>
            </el-row>
            <el-row :gutter="15">
              <el-col :span="10" class="label">Phòng ban:</el-col>
              <el-col :span="14">{{dialog.group}}</el-col>
            </el-row>
            <el-row :gutter="15">
              <el-col :span="10" class="label">Vị trí:</el-col>
              <el-col :span="14">{{dialog.position}}</el-col>
            </el-row>
          </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">Đóng</el-button>
        </span>
      </el-dialog>
      <el-drawer
        title="Cập nhật thời gian chấm công"
        :visible.sync="drawerVisible"
        direction="ltr"
        :before-close="handleClose"
        size="30%"
      >
        <div class="m-3">
          <div>
            <b>Nhân viên: {{drawer.name}}</b>
          </div>
          <div>
            <b>Mã số nhân viên: {{drawer.user_code}}</b>
          </div>
          <div>
            <b>Ngày xác minh: {{date}}</b>
          </div>
          <div class="my-2">
            <b>Thời điểm chấm công</b>
            <el-time-select
              placeholder="Thời điểm chấm công"
              v-model="drawer.time"
              :picker-options="{
                start: '06:00',
                step: '00:15',
                end: '24:00'
              }"
              class="w-100 mb-2"
            ></el-time-select>
            <b>Trả lời khiếu lại</b>
            <el-input type="textarea" v-model="drawer.reply"></el-input>
          </div>
          <div class="text-center mt-3">
            <el-button type="primary" @click="approveSuccess()">Cập nhật</el-button>
          </div>
        </div>
      </el-drawer>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="24">
        <h4>Danh danh video</h4>
      </el-col>
      <el-col v-for="(videoUrl, index) in listVideoUrl" :key="index" class="text-center">
        <h4>Video số {{index+1}}</h4>
        <iframe :src="videoUrl" width="100%" height="480"></iframe>
      </el-col>
      <el-col v-if="listVideoUrl.length == 0">
        <el-alert title="Không có dữ liệu camera của ngày này!" type="warning"></el-alert>
      </el-col>
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
      date: "",
      dataTable: [],
      listVideoUrl: [],
      dialogVisible: false,
      dialog: {
        user_avatar: "",
        name: "",
        user_code: "",
        branch: "",
        position: "",
        group: ""
      },
      drawerVisible: false,
      drawer: {
        name: "",
        user_code: "",
        time: "",
        index: "",
        reply: "",
        request_id: ""
      },
      loading: false
    };
  },
  methods: {
    fetchData: async function() {
      this.loading = true;
      await axios
        .post("/api/form_complain/manage/check_camera", {
          date: this.date
        })
        .then(response => {
          this.dataTable = response.data.forms;
          this.listVideoUrl = response.data.list_video_url;
        });
      this.loading = false;
    },
    openUserInfo(user) {
      this.dialog.user_avatar = user.user_avatar;
      this.dialog.name = user.name;
      this.dialog.user_code = user.user_code;
      this.dialog.branch = user.branch;
      this.dialog.position = user.position;
      this.dialog.group = user.group;
      this.dialogVisible = true;
    },
    handleApproveFail(form_request, index) {
      this.$confirm("Bạn có chắc chắn khiếu lại này thất bại?", "Cảnh báo", {
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        type: "warning"
      }).then(() => {
        axios
          .post("/api/form_complain/manage/approve", {
            request_id: form_request.id,
            action: "fail"
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
              this.dataTable.splice(index, 1);

              this.$notify({
                title: "Hoàn thành",
                message: "Cập nhật thành công",
                type: "success",
                position: "bottom-right"
              });
            }
          });
      });
    },
    openApproveDrawer(form_request, index) {
      this.drawerVisible = true;
      this.drawer.name = form_request.name;
      this.drawer.user_code = form_request.user_code;
      this.drawer.index = index;
      this.drawer.time = "";
      this.drawer.reply = "";
      this.drawer.request_id = form_request.id;
    },
    handleClose(done) {
      this.$confirm("Bạn muốn hủy cập nhập?")
        .then(_ => {
          done();
        })
        .catch(_ => {});
    },
    approveSuccess() {
      if (this.drawer.time == "") {
        this.$notify.error({
          title: "Thất bại",
          message: "Hãy nhập thời gian chấm công",
          position: "bottom-right"
        });
      } else {
        axios
          .post("/api/form_complain/manage/approve", {
            request_id: this.drawer.request_id,
            action: "success",
            time: this.drawer.time,
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
              this.dataTable.splice(this.drawer.index, 1);
              this.$store.dispatch("fetchUsersTimesheets");
              this.drawerVisible =false;
              this.$notify({
                title: "Hoàn thành",
                message: "Cập nhật thành công",
                type: "success",
                position: "bottom-right"
              });
            }
          });
      }
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

.label {
  font-size: 16px;
  font-weight: bold;
}
</style>