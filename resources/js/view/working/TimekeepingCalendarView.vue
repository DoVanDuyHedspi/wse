<template>
  <div id="timesheets">
    <div class="p-3 bg-white">
      <el-row :gutter="20">
        <el-col :span="8" :offset="8" class="text-center">
          <h2>BẢNG THỜI GIAN</h2>
        </el-col>
        <el-col :span="8" class="text-right">
          <span style="cursor: pointer;" @click="handleNote" id="note">
            <i class="el-icon-info"></i> Chú thích
          </span>
        </el-col>
      </el-row>
      <el-row v-if="note" :gutter="20" class="container mx-auto my-4 note">
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
      </el-row>
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
    <div class="mt-3 pl-2">
      <div class="mb-3">
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
      </div>
      <b>Cập nhật lúc:</b>
      <span>{{timeUpdateTimekeepingData}}</span>
    </div>
    <div class="bh-white p-2">
      <calendar-view
        :show-date="showDate"
        :events="events"
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
        :title="'Làm đơn ngày ' + formForDate"
        :visible.sync="dialogFormRequest"
        :center="true"
      >
        <div class="text-center">
          <el-row :gutter="15">
            <el-col :span="24">
              <div>
                <b>Kiểu đơn:</b>
                <el-radio v-model="formType" label="OT">Làm thêm</el-radio>
                <el-radio v-model="formType" label="RM">Làm remote</el-radio>
                <el-radio v-model="formType" label="leaves">Làm bù, quên chấm</el-radio>
                <el-radio v-model="formType" label="complain">Khiếu nại</el-radio>
              </div>
              <el-divider class="mb-0"></el-divider>
            </el-col>
            <el-col :span="24" v-if="formType == 'leaves'" class="text-left mt-3">
              <!-- <div class="text-center"><b>Loại</b></div> -->
              <el-row :gutter="15">
                <el-col :span="8" :offset="4">
                  <div>
                    <el-radio v-model="leaveType" label="ILM">Đến muộn sáng</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="ILA">Đến muộn chiều</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="LEM">Về sớm sáng</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="LEA">Về sớm chiều</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="LO">Rời khỏi vị trí</el-radio>
                  </div>
                </el-col>
                <el-col :span="10" :offset="2">
                  <div>
                    <el-radio v-model="leaveType" label="QQD">Quên chấm đến</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="QQV">Quên chấm về</el-radio>
                  </div>
                  <div>
                    <el-radio v-model="leaveType" label="QQF">Quên chấm cả ngày</el-radio>
                  </div>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
        </div>

        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click="makeForm">Tiến hành</el-button>
        </span>
      </el-dialog>
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
      formType: "leaves",
      leaveType: "ILM"
    };
  },
  components: {
    CalendarView,
    CalendarViewHeader
  },
  created() {
    let now = new Date().toJSON().slice(0, 10);
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
      return state.user.employee_code + ' ' + state.user.name;
    }
  }),
  methods: {
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
      var employee_code = this.employee ? this.employee.split(" ")[0] : this.$root.user.employee_code;
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
    makeForm() {
      if (this.formType == "leaves") {
        this.$router.push(
          "/other_request/new?date=" +
            this.formForDate +
            "&type=" +
            this.leaveType
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
      }
    }
  }
};
</script>

<style lang="scss">
.search-user input {
  border-top: 0;
  border-right: 0;
  border-left: 0;
  width: 300px;
  color: #409EFF;
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

.theme-default .cv-day:hover {
  cursor: pointer;
}

.theme-default .cv-day.future {
  // background: lightyellow;
  background: white;
}

.theme-default .cv-day.outsideOfMonth {
  background-color: #f7f7f7 !important;
}

.theme-default .cv-event.default {
  background-color: white;
}

.theme-default .cv-event.green {
  background-color: #0f0 ;
}

.theme-default .cv-event.red {
  background-color: #fa0404b9;
}

.theme-default .cv-event.pink {
  background-color: pink;
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