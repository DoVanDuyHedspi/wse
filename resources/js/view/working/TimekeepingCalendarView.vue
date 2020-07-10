<template>
  <div id="timesheets">
    <div class="p-3 bg-white">
      <el-row :gutter="20">
        <el-col :span="8">
          <el-button size="mini" type="primary">
            <i class="el-icon-date"></i>
          </el-button>
          <router-link to="/timekeeping/table_view">
            <el-button size="mini" type="default">
              <i class="el-icon-s-order"></i>
            </el-button>
          </router-link>
        </el-col>
        <el-col :span="8" class="text-center">
          <h2>BẢNG CHẤM CÔNG</h2>
        </el-col>
        <!-- <el-col :span="8" class="text-right">
          <span style="cursor: pointer;" @click="handleNote" id="note">
            <i class="el-icon-info"></i> Chú thích
          </span>
        </el-col> -->
      </el-row>
      <!-- <el-row v-if="note" :gutter="20" class="container mx-auto my-4 note">
        <el-col :span="8">
          <span class="block penalty mr-2"></span> Đi muộn, về sớm (sáng/chiều) ngoài quota
        </el-col>
        <el-col :span="8">
          <span class="block solved mr-2"></span> Đi muộn, về sớm (sáng/chiều) đã làm bù
        </el-col>
        <el-col :span="8">
          <span class="block leave mr-2"></span>Không đủ thời gian làm việc tối thiểu
        </el-col>
        <el-col :span="8">
          <span class="block today mr-2"></span> Hôm nay
        </el-col>
      </el-row> -->
      <el-row :gutter="20">
        <el-col :span="8" class="text-right">Tổng số lần bị phạt: {{penalty.number_of_fines}} (lần)</el-col>
        <el-col :span="8" class="text-center">Tổng ngày đi làm: {{number_working_days}} (ngày)</el-col>
        <el-col :span="8" class="text-left">Tổng thời gian làm thêm: {{total_overtime}} (giờ)</el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col
          :span="12"
          class="text-right"
        >Tổng thời gian phạt theo block: {{penalty.block_penalty_time}} (phút)</el-col>
        <el-col
          :span="12"
          class="text-left"
        >Tổng thời gian phạt thực tế: {{penalty.actual_penalty_time}} (phút)</el-col>
      </el-row>
    </div>
    <div class="mt-3 pl-2" v-if="checkRoleManager">
      <el-row class="m-0" :gutter="20">
        <el-col :span="12">
          <b>Nhân viên:</b>
          <el-autocomplete
            class="inline-input search-user"
            prefix-icon="el-icon-search"
            v-model="employee"
            :fetch-suggestions="querySearch"
            placeholder="Tìm kiếm"
            @select="handleSelect"
            size="medium"
          ></el-autocomplete>
        </el-col>
        <el-col :span="12" class="text-right">
          <el-dropdown size="medium" split-button type="primary">
            <i class="el-icon-download"></i>Xuất
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>
                <span @click="downloadCsv('xlsx')">
                  <i class="el-icon-download"></i> Xuất xlsx
                </span>
              </el-dropdown-item>
              <el-dropdown-item>
                <span @click="downloadCsv('csv')">
                  <i class="el-icon-download"></i> Xuất csv
                </span>
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </el-col>
      </el-row>
      <!-- <b>Cập nhật lúc:</b>
      <span>{{timeUpdateTimekeepingData}}</span>-->
    </div>
    <div class="bh-white p-2">
      <calendar-view
        :show-date="showDate"
        :events="events"
        @click-event="onClickEvent"
        @click-date="onClickDay"
        class="theme-default"
      >
        <calendar-view-header
          slot="header"
          slot-scope="t"
          :header-props="t.headerProps"
          @input="setShowDate"
        />
      </calendar-view>
      <el-dialog
        :title="'Ngày ' + formForDate"
        :visible.sync="dialogFormRequest"
        :before-close="handleCloseDialog"
        width="55%"
      >
        <div class="mb-3">
          <el-row :gutter="15">
            <el-divider class="m-0 mb-3"></el-divider>
            <div class="text-center mb-3">
              <h5>Kết quả chấm công</h5>
            </div>
            <el-col :span="6">
              <div>
                <b>Chấm công lần đầu</b> 
                <div>
                  Thời gian :
                  <span
                    v-if="event"
                    style="font-weight: bold;"
                  >{{event.originalEvent.time_in}}</span>
                </div>
              </div>
              <div>
                Chi nhánh :
                <b v-if="event && event.originalEvent.time_in">Hà Nội</b>
              </div>
            </el-col>
            <el-col :span="6">
              <el-image
                v-if="event"
                style="width: 130px; height: 130px"
                :src="event.originalEvent.checkin_capture"
                fit="cover"
              ></el-image>
            </el-col>
            <el-col :span="6">
              <div>
                <b>Chấm công lần cuối</b> 
                <div>
                  Thời gian :
                  <span v-if="event" style="font-weight: bold;">{{event.originalEvent.time_out}}</span>
                </div>
              </div>
              <div>
                Chi nhánh :
                <b v-if="event && event.originalEvent.time_out">Hà Nội</b>
              </div>
            </el-col>
            <el-col :span="6">
              <el-image
                v-if="event"
                style="width: 130px; height: 130px"
                :src="event.originalEvent.checkout_capture"
                fit="cover"
              ></el-image>
            </el-col>
          </el-row>
        </div>
        <div class="text-center">
          <el-row :gutter="15">
            <el-col :span="24">
              <el-divider class="m-0 mb-3"></el-divider>
              <div>
                <h5>Làm đơn thư mới</h5>
              </div>
              <div>
                <el-radio v-model="formType" label="leave">Xin nghỉ phép</el-radio>
                <el-radio v-model="formType" label="OT">Xin làm thêm</el-radio>
                <el-radio v-model="formType" label="RM">Xin làm remote</el-radio>
                <el-radio v-model="formType" label="complain">Khiếu nại</el-radio>
                <el-radio v-model="formType" label="other">Khác</el-radio>
              </div>
            </el-col>
            <el-col :span="24" v-if="formType == 'other'" class="text-left">
              <!-- <div class="text-center"><b>Loại</b></div> -->
              <el-divider class="m-0 mt-3 mb-3"></el-divider>
              <el-row :gutter="15">
                <el-col :span="8" :offset="4">
                  <div>
                    <el-radio v-model="other_request" label="ILM">Đến muộn sáng</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="ILA">Đến muộn chiều</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="LEM">Về sớm sáng</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="LEA">Về sớm chiều</el-radio>
                  </div>
                </el-col>
                <el-col :span="10" :offset="2">
                  <div>
                    <el-radio v-model="other_request" label="LO">Rời khỏi vị trí</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="QQD">Quên chấm đến</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="QQV">Quên chấm về</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="other_request" label="QQF">Quên chấm cả ngày</el-radio>
                  </div>
                </el-col>
              </el-row>
              <el-divider class="m-0"></el-divider>
            </el-col>
            <el-col :span="24" class="text-center mt-3">
              <el-button type="primary" @click="makeForm" size="medium">
                Làm đơn
                <i class="el-icon-s-promotion"></i>
              </el-button>
            </el-col>
          </el-row>
        </div>
      </el-dialog>
    </div>
    <div id="footer-calendar" class="py-2 px-3">
      <span class="mr-3">
        <span class="dot ktc"></span> Không được tính công
      </span>
      <span class="mr-3">
        <span class="dot dmvs"></span>Vào trễ, ra sớm
      </span>
      <span class="mr-3">
        <span class="dot dlb"></span>Đã làm bù
      </span>
      <span class="mr-3">
        <span class="dot dg"></span>Đúng giờ
      </span>
      <span class="mr-3">
        <span class="dot weeken"></span>Cuối tuần
      </span>
      <span class="mr-3">
        <span class="dot ncl"></span>Nghỉ phép có lương/ Nghỉ lễ
      </span>
      <span class="mr-3">
        <span class="dot nkl"></span>Nghỉ phép không lương
      </span>
    </div>
  </div>
</template>
<script>
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar";
import { mapState } from "vuex";
import moment from "moment";
require("vue-simple-calendar/static/css/default.css");
require("vue-simple-calendar/static/css/holidays-us.css");
export default {
  data() {
    return {
      employee: this.$root.user.employee_code + " " + this.$root.user.name,
      showDate: new Date(),
      penalty: {
        actual_penalty_time: 0,
        block_penalty_time: 0,
        number_of_fines: 0
      },
      number_working_days: 0,
      total_overtime: 0,
      note: false,
      timeUpdateTimekeepingData: "",
      dialogFormRequest: false,
      formForDate: "",
      formType: "leave",
      other_request: "ILM",
      event: ""
    };
  },
  components: {
    CalendarView,
    CalendarViewHeader
  },
  created() {
    let now = new Date().toJSON().slice(0, 10);
    console.log(now);
    this.fetchData(now, this.$root.user.employee_code);
    this.getTimeUpdate();
    // this.employee = user.user_code + ' ' + user.name;
  },
  computed: mapState({
    user: state => state.user,
    events: state => state.events,
    listSuggestions() {
      return this.$store.getters.getListSuggestions;
    },
    currentUser(state) {
      return state.user.employee_code + " " + state.user.name;
    },
    checkRoleManager() {
      return this.$store.getters.checkRoleManage;
    },
  }),
  methods: {
    forceFileDownload(response, type) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      if (type == "csv") {
        link.setAttribute("download", "BangChamCongNhanVien.csv");
      } else if (type == "xlsx") {
        link.setAttribute("download", "BangChamCongNhanVien.xlsx");
      }

      document.body.appendChild(link);
      link.click();
    },
    downloadCsv(type) {
      let fullDate = new Date(this.showDate);
      let month = fullDate.getMonth() + 1;
      if (month < 10) {
        month = "0" + month;
      }
      var currentMonth = fullDate.getFullYear() + "-" + month;
      axios({
        method: "post",
        url: "/api/users/export/timesheetsEmployee/",
        responseType: "arraybuffer",
        data: {
          employee_code: this.employee.split(" ")[0],
          type: type,
          month: currentMonth
        }
      })
        .then(response => {
          this.forceFileDownload(response, type);
        })
        .catch(() => console.log("error occured"));
    },
    querySearch(queryString, cb) {
      var suggestions = this.listSuggestions;
      var results = queryString
        ? suggestions.filter(this.createFilter(queryString))
        : suggestions;
      cb(results);
    },
    createFilter(queryString) {
      return suggestion => {
        return (
          suggestion.value.toLowerCase().indexOf(queryString.toLowerCase()) !==
          -1
        );
      };
    },
    handleSelect(item) {
      var employee_code = this.employee.split(" ")[0];
      let now = new Date().toJSON().slice(0, 10);
      this.fetchData(now, employee_code);
    },
    setShowDate(d) {
      this.showDate = d;
      let fullDate = new Date(d);
      let month = fullDate.getMonth() + 1;
      if (month < 10) {
        month = "0" + month;
      }
      var currentDate = fullDate.getFullYear() + "-" + month;
      var employee_code = this.employee
        ? this.employee.split(" ")[0]
        : this.$root.user.employee_code;
      this.fetchData(currentDate, employee_code);
    },
    fetchData(date, id) {
      let params = [];
      params["id"] = id;
      params["date"] = date;
      this.$store.dispatch("fetchEvents", params).then(
        response => {
          let info = this.$store.getters.getTimeSheetInfo;
          this.penalty.actual_penalty_time = info["actual_penalty_time"];
          this.penalty.block_penalty_time = info["block_penalty_time"];
          this.penalty.number_of_fines = info["number_of_fines"];
          this.number_working_days = info["number_working_days"];
          this.total_overtime = info["total_overtime"];
          if (response.data.status === false) {
            this.error.message = response.data.message;
          }
        },
        error => {
          console.log(error);
        }
      );
    },
    getTimeUpdate() {
      axios
        .get("/api/company/timeUpdateTimekeepingData")
        .then(response => {
          this.timeUpdateTimekeepingData = response.data.fetch_at;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    handleNote() {
      console.log("tets");
      this.note = !this.note;
    },
    onClickDay(d) {
      this.dialogFormRequest = true;
      let date = moment(d.toLocaleDateString(), "MM-DD-YYYY").format(
        "DD-MM-YYYY"
      );
      this.formForDate = date;
    },

    onClickEvent(e) {
      this.dialogFormRequest = true;
      this.event = e;
      let date = moment(e.id, "YYYY-MM-DD").format("DD-MM-YYYY");
      this.formForDate = date;
      console.log(e);
    },
    handleCloseDialog(done) {
      this.event = "";
      done();
    },
    makeForm() {
      if (this.formType == "other") {
        this.$router.push(
          "/other_request/new?date=" +
            this.formForDate +
            "&type=" +
            this.other_request
        );
      } else if (this.formType == "complain") {
        this.$router.push("/request_check_camera/new?date=" + this.formForDate);
      } else if (this.formType == "RM") {
        this.$router.push(
          "/request_ot/new?date=" + this.formForDate + "&type=" + this.formType
        );
      } else if (this.formType == "OT") {
        this.$router.push(
          "/request_ot/new?date=" + this.formForDate + "&type=" + this.formType
        );
      } else if (this.formType == "leave") {
        this.$router.push("/request_leave/new?date=" + this.formForDate);
      }
    }
  }
};
</script>

<style lang="scss" scope>
.search-user input {
  border-top: 0;
  border-right: 0;
  border-left: 0;
  width: 300px;
  color: #409eff;
}

.theme-default .cv-event {
  text-align: center;
}

// .cv-day {
//   background: lightyellow;
// }

.theme-default .cv-day.past {
  // background: lightyellow;
  background: white;
}

.theme-default .cv-event:hover {
  cursor: pointer;
}

.theme-default .cv-day.future {
  // background: lightyellow;
  background: white;
}

.theme-default .cv-day.outsideOfMonth {
  background-color: #f7f7f7 !important;
}

.theme-default .cv-event.ktc {
  border: 1.3px solid #909399;
  background-color: rgb(244, 244, 245);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: #909399;
  }
}

.ktc {
  border: 1.3px solid #909399;
  background-color: rgb(244, 244, 245);
  &:hover {
    background-color: #909399;
  }
}

.theme-default .cv-event.weeken {
  border: 1.3px solid #909399;
  background-color: white;
  color: rgb(81, 77, 106);
  &:hover {
    background-color: white;
  }
}

.weeken {
  border: 1.3px solid #909399;
  background-color: white;
  &:hover {
    background-color: white;
  }
}

.theme-default .cv-event.dg {
  border: 1.3px solid #67c23a;
  background-color: rgb(225, 243, 216);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: #67c23a;
  }
}

.dg {
  border: 1.3px solid #67c23a;
  background-color: rgb(225, 243, 216);
  &:hover {
    background-color: #67c23a;
  }
}

.theme-default .cv-event.ncl {
  border: 1.3px solid yellow;
  background-color: rgba(255, 247, 3, 0.15);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: yellow;
  }
}

.ncl {
  border: 1.3px solid yellow;
  background-color: rgba(255, 247, 3, 0.15);
  &:hover {
    background-color: yellow;
  }
}

.theme-default .cv-event.nkl {
  border: 1.3px solid #e6a23c;
  background-color: rgb(250, 236, 216);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: #e6a23c;
  }
}

.nkl {
  border: 2px solid #e6a23c;
  background-color: rgb(250, 236, 216);
  &:hover {
    background-color: #e6a23c;
  }
}

.theme-default .cv-event.dmvs {
  border: 1.3px solid red;
  background-color: rgb(253, 226, 226);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: red;
  }
}

.dmvs {
  border: 1.3px solid red;
  background-color: rgb(253, 226, 226);
  &:hover {
    background-color: red;
  }
}

.theme-default .cv-event.dlb {
  border: 1.3px solid pink;
  background-color: rgb(254, 240, 240);
  color: rgb(81, 77, 106);
  &:hover {
    background-color: pink;
  }
}

.dlb {
  border: 1.3px solid pink;
  background-color: rgb(254, 240, 240);
  &:hover {
    background-color: pink;
  }
}

#footer-calendar {
  position: fixed;
  bottom: 0;
  width: 100%;
  background: white;
  border-top: 2px solid rgba(128, 128, 128, 0.3);
  .dot {
    height: 12px;
    width: 12px;
    margin-right: 10px;
    border-radius: 100%;
    display: inline-block;
  }
}

.theme-default .cv-header {
  // background: skyblue;
  border-radius: 10px 10px 0 0;
}

.theme-default .cv-header-day {
  background-color: white;
}

.theme-default .cv-day.today {
  background: #96c2f2;
}

// .cv-day,
// .cv-event,
// .cv-header-day,
// .cv-header-days,
// .cv-week,
// .cv-weeks {
//   border-color: skyblue !important;
//   border-style: solid;
// }
.cv-week {
  min-height: 3.8em;
}

.theme-default .cv-header button {
  color: black;
  // background: white;
}
#timesheets {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  color: #2c3e50;
  margin-left: auto;
  margin-right: auto;
  .el-dialog .el-dialog__body {
    padding-top: 0 !important;
  }
}

.block {
  width: 25px;
  height: 25px;
  display: inline-block;
}

.note {
  .penalty {
    background-color: #fa0404b9;
    border: 1px solid skyblue;
  }

  .solved {
    background-color: #ffcccc;
    border: 1px solid skyblue;
  }

  .leave {
    background-color: #7fff0075;
    border: 1px solid skyblue;
  }

  .today {
    background-color: yellow;
    border: 1px solid skyblue;
  }

  .past {
    background-color: white;
    border: 1px solid skyblue;
  }

  .future {
    background-color: lightyellow;
    border: 1px solid skyblue;
  }
}

#note:hover {
  color: skyblue;
}
</style>