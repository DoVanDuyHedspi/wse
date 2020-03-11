<template>
  <div>
    <div class="bg-white p-3" style="border-bottom: 1px solid rgba(128,128,128, 0.3)">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item :to="{ path: '/' }">homepage</el-breadcrumb-item>
        <el-breadcrumb-item>Quản lý tổ chức</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân sự</el-breadcrumb-item>
        <el-breadcrumb-item>Nhân viên</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="error" v-if="error.message.length">
      <div class="alert alert-danger" role="alert">{{ error.message }}</div>
    </div>
    <div class="p-4">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">User name</th>
            <th scope="col">Email</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in users" :key="'user-' + index">
            <th scope="row">{{ user.id }}</th>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
          </tr>
        </tbody>
      </table>
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
      }
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
            console.log(response.data);
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