<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Thành viên</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container mt-3">
      <div class="mb-2">
        <el-row>
          <el-col :span="24">
            <div class="grid-content">
              <h3>
                Danh sách thành viên
                <el-tooltip effect="dark" :content="info" placement="right-start">
                  <i class="el-icon-question" style="font-size: 20px"></i>
                </el-tooltip>
              </h3>
            </div>
          </el-col>
        </el-row>
        <el-row :gutter="20">
          <el-col :span="6"></el-col>
          <el-col :span="12" :offset="12">
            <div class="grid-content float-right">
              <el-dropdown split-button type="default">
                Chi nhánh
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>Chi nhánh 1</el-dropdown-item>
                  <el-dropdown-item>Chi nhánh 2</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
              <router-link to="/users/create">
                <el-button icon="el-icon-plus" type="success">Thêm mới</el-button>
              </router-link>
            </div>
          </el-col>
        </el-row>
        <el-row>
          <el-table
            :data="users"
            style="width: 100%;  box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04)"
            stripe
          >
            <el-table-column fixed prop="employee_code" label="Mã nhân viên" width="150"></el-table-column>
            <el-table-column prop="name" label="Tên" width="200"></el-table-column>
            <el-table-column prop="email" label="Email" width="250"></el-table-column>
            <el-table-column prop="position.name" label="Vị trí" width="200"></el-table-column>
            <el-table-column prop="gender" label="Giới tính" width="100"></el-table-column>
            <el-table-column prop="birthday" label="Ngày sinh" width="150"></el-table-column>
            <el-table-column prop="employee_type.name" label="Loại nhân viên" width="200"></el-table-column>
            <el-table-column prop="group.name" label="Phòng ban" width="200"></el-table-column>
            <el-table-column prop="branch.name" label="Chi nhánh" width="200"></el-table-column>
            <el-table-column prop="phone_number" label="SĐT" width="200"></el-table-column>
            <el-table-column prop="current_address" label="Địa chỉ hiện tại" width="250"></el-table-column>
            <el-table-column prop="pernentment_address" label="Địa chỉ thường chú" width="250"></el-table-column>
            <el-table-column prop="identity_card_passport.code" label="CMND/Hộ chiếu" width="150"></el-table-column>
            <el-table-column fixed="right" label="Thao tác" width="200">
              <template slot-scope="scope">
                <router-link :to="'/users/edit/' + scope.row.id">
                  <el-button size="mini"><i class="el-icon-edit"></i></el-button>
                </router-link>
                <router-link :to="'/users/' + scope.row.id">
                  <el-button
                    size="mini"
                    type="primary"
                  ><i class="el-icon-view"></i></el-button>
                </router-link>
              </template>
            </el-table-column>
          </el-table>
        </el-row>
      </div>

      <!-- <div class="mt-3">
        <pagination :data="users" :align="'center'" @pagination-change-page="getPermissions"></pagination>
      </div>-->
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      error: {
        message: ""
      },
      info:
        "Danh sách thành viên có thể xem thông tin trong quyền của người dùng này"
    };
  },
  created() {
    this.getUsers();
  },
  methods: {
    getUsers() {
      axios
        .get("/users")
        .then(response => {
          if (response.data.status === false) {
            this.error.message = response.data.message;
          } else {
            this.users = response.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    }
  }
};
</script>

<style>
</style>