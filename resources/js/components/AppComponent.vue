<template>
  <div>
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
            <span slot="title" id="logo">WSE</span>
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
                  <span slot="title">Chấm công</span>
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
                <router-link to="/users_requests/request_check_camera">
                  <el-menu-item index="1-3-3">
                    <span slot="title">Khiếu nại chấm công</span>
                  </el-menu-item>
                </router-link>
              </el-submenu>
            </el-menu-item-group>
            <el-menu-item-group title="Tổ chức">
              <router-link to="/organization">
                <el-menu-item index="1-3">
                  <!-- <i class="el-icon-connection"></i> -->
                  <span slot="title">Thiết lập tổ chức</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
            <el-menu-item-group title="Phân quyền hệ thống">
              <router-link to="/role">
                <el-menu-item index="1-4">
                  <span slot="title">Nhóm quyền</span>
                </el-menu-item>
              </router-link>
              <router-link to="/permission">
                <el-menu-item index="1-5">
                  <span slot="title">Quyền hạn</span>
                </el-menu-item>
              </router-link>
            </el-menu-item-group>
          </el-submenu>
          <el-submenu index="2">
            <template slot="title">
              <i class="el-icon-date"></i>
              <span slot="title">Lịch làm việc</span>
            </template>
            <router-link to="/timekeeping/calendar_view">
              <el-menu-item index="2-1">
                <span slot="title">Xem dạng lịch</span>
              </el-menu-item>
            </router-link>
            <router-link to="/timekeeping/table_view">
              <el-menu-item index="2-2">
                <span slot="title">Xem dạng bảng</span>
              </el-menu-item>
            </router-link>
          </el-submenu>
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
            <router-link to="/request_check_camera">
              <el-menu-item index="4-3">
                <span slot="title">Khiếu nại chấm công</span>
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
            <el-col :span="18" style="line-height: 60px">
              <div class="user-login">
                <el-badge
                  :value="number_unread_noti"
                  class="item"
                  type="primary"
                  style="line-height: normal;"
                >
                  <el-popover placement="bottom" width="400" trigger="click">
                    <el-row class="m-0">
                      <el-col :span="24" class="text-left">
                        <span style="font-size: 16px; color: #ff000091">Thông báo của bạn</span>
                      </el-col>
                    </el-row>
                    <el-divider class="my-0"></el-divider>
                    <el-row
                      :gutter="15"
                      v-for="(noti, index) in notifications"
                      :key="index"
                      :class="{unread: !noti.read_at}"
                      class="noti_row"
                    >
                      <el-col :span="4">
                        <i class="el-icon-message" style="font-size: 40px; color: #ff000091"></i>
                      </el-col>
                      <el-col :span="20">
                        <router-link to="/request_leaves" v-if="noti.type == 'form-request'">
                          <span @click="readNoti(noti.id, index)">{{noti.message}}</span>
                        </router-link>
                        <router-link
                          to="/request_check_camera"
                          v-else-if="noti.type == 'form-complain'"
                        >
                          <span @click="readNoti(noti.id, index)">{{noti.message}}</span>
                        </router-link>
                        <br />
                        <small>{{noti.date_time}}</small>
                      </el-col>
                    </el-row>
                    <el-divider class="my-0"></el-divider>
                    <i
                      slot="reference"
                      class="el-icon-message-solid"
                      style="font-size: 20px;color: #ff000091;cursor: pointer;"
                    ></i>
                  </el-popover>
                </el-badge>
                <el-popover placement="bottom" width="300" trigger="click" class="ml-4">
                  <el-row :gutter="15" class="m-0">
                    <el-col :span="6">
                      <el-avatar shape="square" :size="70" class="avatar" :src="$root.user.avatar"></el-avatar>
                    </el-col>
                    <el-col :span="18" class="pl-3">
                      <p class="m-0">
                        <b>{{$root.user.name}}</b>
                      </p>
                      <p>
                        <b>{{$root.user.email}}</b>
                      </p>
                    </el-col>
                  </el-row>
                  <el-divider class="my-2"></el-divider>
                  <el-row class="m-0">
                    <el-col :span="12" class="text-left">
                      <router-link :to="'/users/'+$root.user.id">
                        <el-button size="small">Tài khoản</el-button>
                      </router-link>
                    </el-col>
                    <el-col :span="12" class="text-right">
                      <el-button size="small" @click.prevent="logout">Đăng xuất</el-button>
                    </el-col>
                  </el-row>
                  <el-avatar slot="reference" class="avatar" :size="30" :src="$root.user.avatar"></el-avatar>
                </el-popover>

                <!-- <div class="logout">
                  <a href="#" @click.prevent="logout">Logout</a>
                </div>-->
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
import moment from "moment";
export default {
  data() {
    return {
      isCollapse: false,
      aside_width: "",
      notifications: "",
      number_unread_noti: 0
    };
  },
  created() {
    if (this.$route.path == "/") {
      this.goToUserInfo();
    }

    this.$store.dispatch("fetch");
    this.$store.dispatch("fetchCompanyInfo");
    this.$store.dispatch("fetchTimekeeping");
    this.fetchNotifications();
  },
  methods: {
    fetchNotifications: async function() {
      await axios
        .get("/api/users/" + this.$root.user.id + "/notifications")
        .then(response => {
          this.notifications = response.data.data;
        });
      let number_unread_noti = 0;
      this.notifications.map(function(noti) {
        if (!noti.read_at) {
          number_unread_noti += 1;
        }
      });
      this.number_unread_noti = number_unread_noti;
    },
    readNoti(noti_id, index) {
      axios
        .post("/api/users/notifications/" + noti_id + "/read")
        .then(response => {
          this.notifications[index].read_at = moment();
          this.number_unread_noti -= 1;
        });
    },
    goToUserInfo() {
      this.$router.push("/users/" + this.$root.user.id);
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

.avatar {
  vertical-align: middle;
  cursor: pointer;
}

#logo {
  color: red;
  font-weight: 900;
  font-size: 20px;
  font-family: "Courier New", Courier, monospace;
}

.unread {
  background: #e6f2ff;
}

.noti_row {
  margin: 0 !important;
  padding: 5px 0 5px 0;
}
</style>