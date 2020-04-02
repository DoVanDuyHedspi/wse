<template>
  <div id="timesheets">
    <div class="p-3 bg-white">
      <el-row :gutter="20">
        <el-col :span="24" class="text-center">
          <h3>Bảng thời gian</h3>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="8" class="text-right">Tổng số lần bị phạt: {{penalty.number_of_fines}} (lần)</el-col>
        <el-col :span="8" class="text-center">Tổng ngày đi làm: {{number_working_days}} (ngày)</el-col>
        <el-col :span="8" class="text-left">Tổng thời gian làm thêm: 0 (giờ)</el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="12" class="text-right">Tổng thời gian phạt theo block: {{penalty.block_penalty_time}} (phút)</el-col>
        <el-col :span="12" class="text-left">Tổng thời gian phạt thực tế: {{penalty.actual_penalty_time}} (phút)</el-col>
      </el-row>
    </div>
    <div class="bh-white mt-3 p-1">
      <calendar-view :show-date="showDate" :events="events" class="theme-default">
        <calendar-view-header
          slot="header"
          slot-scope="t"
          :header-props="t.headerProps"
          @input="setShowDate"
        />
      </calendar-view>
    </div>
  </div>
</template>
<script>
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar";
import { mapState } from "vuex";
require("vue-simple-calendar/static/css/default.css");
require("vue-simple-calendar/static/css/holidays-us.css");
export default {
  data() {
    return {
      showDate: new Date(),
      penalty: {
        actual_penalty_time: 0,
        block_penalty_time: 0,
        number_of_fines: 0
      },
      number_working_days: 0,
    };
  },
  components: {
    CalendarView,
    CalendarViewHeader
  },
  created() {
    let now = new Date().toJSON().slice(0, 10);
    this.fetchData(now);
  },
  computed: {
    ...mapState(["events"])
  },
  methods: {
    setShowDate(d) {
      this.showDate = d;
      let fullDate = new Date(d);
      let month = fullDate.getMonth()+1;
      if(month < 10) {
        month = "0" + month;
      }
      var currentDate = fullDate.getFullYear() + "-" + month;
      this.fetchData(currentDate);
    },
    fetchData(date) {
      let params = [];
      params["id"] = this.$root.user.employee_code;
      params["date"] = date;
      this.$store.dispatch("fetchEvents", params).then(
        response => {
          let penalty = this.$store.getters.getInfoPenalty;
          this.penalty.actual_penalty_time = penalty['actual_penalty_time'];
          this.penalty.block_penalty_time = penalty['block_penalty_time'];
          this.penalty.number_of_fines = penalty['number_of_fines'];
          this.number_working_days = penalty['number_working_days']
          if (response.data.status === false) {
            this.error.message = response.data.message;
          }
        },
        error => {
          console.log(error);
        }
      );
    }
  }
};
</script>

<style lang="scss">

// .theme-default .cv-day.past {
//   background-color: white;
// }

.theme-default .cv-event {
  text-align: center;
}

.cv-day {
  background: lightyellow;
}

.theme-default .cv-day.past {
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
  background-color: #7fff0075;
}

.theme-default .cv-event.red {
  background-color: #fa0404b9;
}

.theme-default .cv-header {
  background: skyblue;
  border-radius: 10px 10px 0 0;
}

.theme-default .cv-day.today {
  background: yellow;
}

.cv-day,
.cv-event,
.cv-header-day,
.cv-header-days,
.cv-week,
.cv-weeks {
  border-color: skyblue !important;
  border-style: solid;
}

.theme-default .cv-header button {
  color: black;
  background: floralwhite;
}
#timesheets {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  color: #2c3e50;
  margin-left: auto;
  margin-right: auto;
}
</style>