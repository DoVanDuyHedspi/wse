<template>
  <div class="p-4 mt-3">
    <div>
      <router-link to="/working-day/on-holiday">
        <el-button type="default" size="medium">Thiết lập nghỉ lễ</el-button>
      </router-link>
      <el-button type="primary" size="medium">Thiết lập nghỉ phép</el-button>
    </div>
    <el-divider class="mb-0"></el-divider>
    <div class="p-3">
      <div class="text-right mb-3">
        <router-link to="/working-day/on-leave-type/create">
          <el-button type="primary" size="medium">
            <i class="el-icon-plus"></i> Tạo mới
          </el-button>
        </router-link>
      </div>
      <div>
        <el-table
          :data="dataTable"
          style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        >
          <el-table-column label="Stt" width="50" class-name="text-center">
            <template slot-scope="scope">{{scope.$index + 1}}</template>
          </el-table-column>
          <el-table-column property="slug" label="Từ khóa loại" class-name="text-center"></el-table-column>
          <el-table-column property="name" label="Tên ngày nghỉ" class-name="text-center"></el-table-column>
          <el-table-column property="number_days" label="Số ngày" class-name="text-center"></el-table-column>
          <el-table-column align="right" label="Thao tác" class-name="text-center">
            <template slot-scope="scope">
              <el-button size="mini" @click="editLeaveType(scope.row)">
                <i class="el-icon-edit"></i>
              </el-button>
              <el-button
                size="mini"
                type="danger"
                @click.native.prevent="deleteLeaveType(scope.$index, scope.row)"
              >
                <i class="el-icon-delete"></i>
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dataTable: []
    };
  },
  created() {
    this.fetchLeaveType();
  },
  methods: {
    fetchLeaveType: async function(page = 1) {
      await axios.get("/api/leave_type").then(response => {
        if ((response.status = true)) {
          this.dataTable = response.data;
        }
      });
    },

    editLeaveType(holiday) {
      this.$router.push("/working-day/on-leave-type/edit/" + holiday.id);
    },

    deleteLeaveType: async function(index, leave_type) {
      this.$confirm("Bạn có chắc chắn muốn xóa ngày nghỉ lễ này?", "Cảnh báo", {
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        type: "warning"
      }).then(() => {
        axios.delete("/api/leave_type/" + leave_type.id).then(response => {
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
              message: "Xóa kiểu nghỉ phép thành công",
              type: "success",
              position: "bottom-right"
            });
            this.dataTable.splice(index, 1);
          }
        });
      });
    }
  }
};
</script>