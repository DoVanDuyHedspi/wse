<template>
  <div class>
    <el-container>
      <el-aside v-bind:style="{ width: aside_width}">
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
              <router-link to="/users">
                <el-menu-item index="1-1">
                  <!-- <i class="el-icon-user"></i> -->
                  <span slot="title">Thành viên</span>
                </el-menu-item>
              </router-link>
              <router-link to="/manage_timesheets">
                <el-menu-item index="1-2">
                  <!-- <i class="el-icon-user"></i> -->
                  <span slot="title">Quản lý bảng thời gian</span>
                </el-menu-item>
              </router-link>
              <el-submenu index="1-3">
                <template slot="title">
                  <span slot="title">Yêu cầu của nhân viên</span>
                </template>
                <router-link to="/users_requests/ot_remote">
                  <el-menu-item index="1-3-1">
                    <span slot="title">Làm ngoài giờ, remote</span>
                  </el-menu-item>
                </router-link>
                <router-link to="/users_requests/leaves">
                  <el-menu-item index="1-3-2">
                    <span slot="title">Làm bù</span>
                  </el-menu-item>
                </router-link>
              </el-submenu>
            </el-menu-item-group>
            <el-menu-item-group title="Tổ chức">
              <router-link to="/organization">
                <el-menu-item index="1-3">
                  <!-- <i class="el-icon-connection"></i> -->
                  <span slot="title">Sơ đồ tổ chức</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
            <el-menu-item-group title="Phân quyền hệ thống">
              <router-link to="/permission">
                <el-menu-item index="1-4">
                  <!-- <i class="el-icon-s-claim"></i> -->
                  <span slot="title">Danh sách quyền</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
          </el-submenu>
          <router-link to="/user_timesheets">
            <el-menu-item index="2">
              <i class="el-icon-date"></i>
              <span slot="title">Lịch làm việc</span>
            </el-menu-item>
          </router-link>
          <el-submenu index="4">
            <template slot="title">
              <i class="el-icon-s-order"></i>
              <span slot="title">Yêu cầu cá nhân</span>
            </template>
            <router-link to="/request_ot">
              <el-menu-item index="4-1">
                <span slot="title">Làm ngoài giờ, remote</span>
              </el-menu-item>
            </router-link>
            <router-link to="/request_leaves">
              <el-menu-item index="4-2">
                <span slot="title">Làm bù</span>
              </el-menu-item>
            </router-link>
          </el-submenu>
          <router-link to="/branches">
            <el-menu-item index="5">
              <i class="el-icon-map-location"></i>
              <span slot="title">Dữ liệu workspace</span>
            </el-menu-item>
          </router-link>
          <el-submenu index="6">
            <template slot="title">
              <i class="el-icon-s-flag"></i>
              <span slot="title">Báo cáo</span>
            </template>
            <router-link to="/report/fake_face_report">
              <el-menu-item index="6-1">
                <span slot="title">Mặt giả</span>
              </el-menu-item>
            </router-link>
          </el-submenu>
        </el-menu>
      </el-aside>
      <el-container>
        <el-header style="background-color:#fff; border-bottom: 1px solid rgba(128,128,128, 0.3)">
          <el-row :gutter="20">
            <el-col :span="6" style="line-height: 60px">
              <i
                v-bind:class="{'el-icon-s-fold': !isCollapse, 'el-icon-s-unfold': isCollapse}"
                style="font-size: 25px; cursor: pointer;"
                @click="handleAsideMenu"
              ></i>
            </el-col>
            <el-col :span="18">
              <div class="user-login">
                <div class="username">User: {{ $root.user.name }}</div>
                <div class="logout">
                  <a href="#" @click.prevent="logout">Logout</a>
                </div>
              </div>
            </el-col>
          </el-row>
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
      isCollapse: false,
      aside_width: ""
    };
  },
  created() {
    // this.goToUserInfo();
    this.$store.dispatch("fetch");
    this.$store.dispatch("fetchCompanyInfo");
  },
  methods: {
    goToUserInfo() {
      // this.$router.push("/users/" + this.$root.user.id);
    },
    handleAsideMenu() {
      this.isCollapse = !this.isCollapse;
      if (this.isCollapse == true) {
        this.aside_width = "auto";
      }
    },
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
.el-aside {
  max-width: 215px;
  ul {
    height: 100%;
    min-height: 100vh;
  }
}
.el-menu-vertical-demo:not(.el-menu--collapse) {
  width: 215px;
  min-height: 400px;
  height: 100%;
  min-height: 100vh;
  a {
    color: white;
    text-decoration: blink;
  }
}
</style>