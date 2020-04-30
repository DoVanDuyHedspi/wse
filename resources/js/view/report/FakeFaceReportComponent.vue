<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Báo cáo</el-breadcrumb-item>
        <el-breadcrumb-item>Mặt giả</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container mt-3">
      <div class="my-4">
        <el-row :gutter="20">
          <el-col :span="24">
            <div class="text-center">
              <h3>
                BÁO CÁO MẶT GIẢ
                <!-- <el-tooltip effect="dark" :content="info" placement="right-start">
                  <i class="el-icon-question" style="font-size: 20px"></i>
                </el-tooltip>-->
              </h3>
            </div>
          </el-col>
          <el-col :span="24">
            <div class></div>
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
            <el-button class="w-100" type="primary" @click="filterReport">Lọc</el-button>
          </el-col>
        </el-row>
        <div class="error" v-if="error.message.length">
          <div class="alert alert-danger" role="alert">{{ error.message }}</div>
        </div>
      </div>
      <el-table
        ref="multipleTable"
        :data="dataTable"
        style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
        border
      >
        <!-- <el-table-column type="selection" width="55"></el-table-column> -->
        <el-table-column property="id" label="#Id" class-name="text-center" width="50px"></el-table-column>
        <el-table-column property="branch" label="Chi nhánh" class-name="text-center"></el-table-column>
        <el-table-column property="date_time" label="Thời gian" class-name="text-center"></el-table-column>
        <el-table-column label="Hình ảnh" class-name="text-center">
          <template slot-scope="scope">
            <el-image
              style="width: 100px; height: 100px"
              :src="scope.row.image"
              :preview-src-list="srcList"
            >></el-image>
          </template>
        </el-table-column>
        <el-table-column property="user.name" label="Nhận diện" width="150" class-name="text-center">
          <template slot-scope="scope">
            <router-link :to="'/users/'+scope.row.user.id" v-if="scope.row.user" >
              <span>{{scope.row.user.name}}</span>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column label="Kết quả giả mạo" class-name="text-center">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.spoofing_success" type="warning">Thành công</el-tag>
            <el-tag v-else type="info">Thất bại</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" class-name="text-center">
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="danger"
              @click.native.prevent="deletePermisson(scope.$index, scope.row)"
            >Xóa</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      dataTable: [],
      error: {
        message: ""
      },
      noti: "",
      search: "",
      srcList: [],
      filter: {
        range_date: "",
        branch_id: ""
      }
    };
  },
  created() {
    this.getReports();
  },
  computed: {
    ...mapState(["infoCompany"])
  },
  methods: {
    getReports: async function() {
      await axios
        .get("/api/fake_face_report")
        .then(response => {
          console.log(response.data.reports);
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            this.dataTable = response.data.reports;
          }
        })
        .catch(error => {
          console.log(error);
        });

      let srcList = [];
      this.dataTable.map(function(report) {
        srcList.push(report.image);
      });
      this.srcList = srcList;
    },
    deletePermisson(index, report) {
      this.$confirm("Bạn có chắc chắn muốn xóa report này?", "Cảnh báo", {
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Hủy",
        type: "warning"
      })
        .then(() => {
          axios.delete("/api/fake_face_report/" + report.id).then(response => {
            if (response.data.status === false) {
              this.$notify.error({
                title: "Thất bại",
                message: response.data.message,
                position: "bottom-right"
              });
            } else {
              this.dataTable.splice(index, 1);
              this.srcList.splice(index, 1);
              this.$notify({
                title: "Hoàn thành",
                message: "Xóa report thành công",
                type: "success",
                position: "bottom-right"
              });
            }
          });
        })
        .catch(() => {
          this.error.message = "Xóa thất bại!";
          setTimeout(() => {
            this.error.message = "";
          }, 3000);
        });
    },
    filterReport: async function() {
      await axios
        .get("/api/fake_face_report", {
          params: {
            date_begin: this.filter.range_date ? this.filter.range_date[0] : "",
            date_end: this.filter.range_date ? this.filter.range_date[1] : "",
            branch_id: this.filter.branch_id
          }
        })
        .then(response => {
          this.dataTable = response.data.reports;
        });
      let srcList = [];
      this.dataTable.map(function(report) {
        srcList.push(report.image);
      });
      this.srcList = srcList;
    }
  }
};
</script>

<style lang="scss">
.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}
.el-col {
  border-radius: 4px;
}
</style>