<template>
  <div class>
    <el-container>
      <el-aside width="200px">
        <!-- <el-radio-group v-model="isCollapse" style="margin-bottom: 20px;">
          <el-radio-button :label="false">expand</el-radio-button>
          <el-radio-button :label="true">collapse</el-radio-button>
        </el-radio-group>-->
        <el-menu
          class="el-menu-vertical-demo"
          :collapse="isCollapse"
          background-color="#07313a"
          text-color="#fff"
          active-text-color="#ffd04b"
        >
          <el-menu-item index="0" class="text-center">
            <span slot="title">WSE</span>
          </el-menu-item>
          <el-submenu index="1">
            <template slot="title">
              <i class="el-icon-office-building"></i>
              <span slot="title">Quản lý tổ chức</span>
            </template>
            <el-menu-item-group>
              <span slot="title">Nhân sự</span>
              <router-link to="/user">
                <el-menu-item index="1-1">
                  <i class="el-icon-user"></i>
                  <span slot="title">Nhân viên</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
            <el-menu-item-group title="Phân quyền hệ thống">
              <router-link to="/permission">
                <el-menu-item index="1-2">
                  <i class="el-icon-user-solid"></i>
                  <span slot="title">Danh sách quyền</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
            <el-submenu index="1-4">
              <span slot="title">item four</span>
              <el-menu-item index="1-4-1">item one</el-menu-item>
            </el-submenu>
          </el-submenu>
          <el-menu-item index="2">
            <i class="el-icon-menu"></i>
            <span slot="title">Navigator Two</span>
          </el-menu-item>
          <el-menu-item index="3" disabled>
            <i class="el-icon-document"></i>
            <span slot="title">Navigator Three</span>
          </el-menu-item>
          <el-menu-item index="4">
            <i class="el-icon-setting"></i>
            <span slot="title">Navigator Four</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-container>
        <el-header style="background-color:#fff; border-bottom: 1px solid rgba(128,128,128, 0.3)">
          <div class="user-login">
            <div class="username">User: {{ $root.user.name }}</div>
            <div class="logout">
              <a href="#" @click.prevent="logout">Logout</a>
            </div>
          </div>
        </el-header>
        <el-main class="p-0">
          <div class="main-content">
            <router-view></router-view>
          </div>
        </el-main>
        <el-footer></el-footer>
      </el-container>
    </el-container>
  </div>
</template>



<script>
export default {
  data() {
    return {
      isCollapse: false
    };
  },
  methods: {
    logout: function() {
      axios
        .post("logout")
        .then(response => {})
        .catch(error => {
          if (error.status === 302 || 401) {
            document.location.href = "/login";
          } else {
            // throw error and go to catch block
          }
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.user-login {
  text-align: right;
}
.el-menu-vertical-demo:not(.el-menu--collapse) {
  width: 200px;
  min-height: 400px;
  height: 100vh;
  a {
    color: white;
  }
}
</style>