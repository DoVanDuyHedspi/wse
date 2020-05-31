<template>
  <div id="timesheets">
    <div class="p-3 bg-white">
      <el-row :gutter="20">
        <el-col :span="24" class="text-center">
          <h2>BẢNG THỜI GIAN</h2>
        </el-col>
      </el-row>
    </div>
    <div class="mt-3 p-2">
      <div class="mb-2">
        <el-row class="m-0">
          <el-col :span="8">
            <b>Nhân viên:</b>
            <el-autocomplete
              class="inline-input search-user mb-3"
              prefix-icon="el-icon-search"
              v-model="employee"
              :fetch-suggestions="querySearch"
              placeholder="Tìm kiếm nhân viên"
              @select="handleSelect"
              size="medium"
            ></el-autocomplete>
          </el-col>
          <el-col :span="8" class="text-center">
            <el-date-picker
              v-model="month"
              type="month"
              size="medium"
              placeholder="Pick a month"
              format="MM-yyyy"
              value-format="DD-MM-yyyy"
              @change="filterMonth"
            ></el-date-picker>
          </el-col>
          <el-col :span="8" class="text-right">
            <el-dropdown size="medium" split-button type="primary">
              <i class="el-icon-download"></i>Xuất
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>
                  <i class="el-icon-download"></i> Xuất xlsx
                </el-dropdown-item>
                <el-dropdown-item>
                  <i class="el-icon-download"></i> Xuất csv
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
            <el-button size="medium" type="primary" v-print="'#table'">
              <i class="el-icon-printer"></i> In
            </el-button>
          </el-col>
        </el-row>
        <el-table :data="events" style="width: 100%" id="table">
          <el-table-column prop="date" label="Ngày"></el-table-column>
          <el-table-column label="Vào đầu tiên">
            <template slot-scope="scope">
              <div v-if="scope.row.time_in">{{scope.row.time_in}}</div>
              <div v-else>-</div>
            </template>
          </el-table-column>
          <el-table-column label="Ra cuối cùng">
            <template slot-scope="scope">
              <div v-if="scope.row.time_out">{{scope.row.time_out}}</div>
              <div v-else>-</div>
            </template>
          </el-table-column>
          <el-table-column prop="fined_time" label="Thời gian đi muộn/về sớm (phút)"></el-table-column>
          <el-table-column prop="overtime" label="Thời gian làm thêm (phút)"></el-table-column>
          <el-table-column label="Trạng thái">
            <template slot-scope="scope">
              <div v-if="isSunday(scope.row.date)">
                <span style="background: #E6A23C" class="dot"></span>Cuối tuần
              </div>
              <div v-else-if="scope.row.type == null">
                <span style="background: #67C23A" class="dot"></span>Vắng mặt
              </div>
              <div v-else-if="scope.row.number_of_fines == 0">
                <span style="background: #FFFFFF; border: 1px black solid" class="dot"></span>Có mặt
              </div>
              <div v-else-if="scope.row.number_of_fines != 0">
                <span style="background: #F56C6C" class="dot"></span>Có mặt
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <!-- <b>Cập nhật lúc:</b>
      <span>{{timeUpdateTimekeepingData}}</span>-->
    </div>
    <div class="bh-white p-2">
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
    <div id="footer" class="p-3">
      <span class="mr-3">
        <span style="background: #E6A23C" class="dot"></span> Không tính công
      </span>
      <span class="mr-3">
        <span style="background: #F56C6C" class="dot"></span>Vào trễ, ra sớm
      </span>
      <span class="mr-3">
        <span style="background: #FFFFFF; border: 1px black solid" class="dot"></span>Chấm công đúng giờ
      </span>
      <span class="mr-3">
        <span style="background: #67C23A" class="dot"></span>Không chấm công/Không đủ thời gian làm tối thiểu
      </span>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";
import moment from "moment";
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
      leaveType: "ILM",
      month: moment().format("DD-MM-YYYY")
    };
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
      return state.user.employee_code + " " + state.user.name;
    }
  }),
  methods: {
    filterMonth() {
      var employee_code = this.employee.split(" ")[0];
      this.fetchData(this.month, employee_code);
    },
    isSunday(date) {
      var myDate = moment(date, "DD-MM-YYYY").format("MM-DD-YYYYY");
      myDate = new Date(myDate);
      if (myDate.getDay() == 0) {
        return true;
      } else {
        return false;
      }
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
          "/request_leaves/new?date=" +
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

<style lang="scss" scope>
.search-user input {
  border-top: 0;
  border-right: 0;
  border-left: 0;
  width: 250px;
  color: #409eff;
}

.el-table th {
  font-size: inherit !important;
  color: black;
  font-weight: bold;
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

#footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  background: white;
  border-top: 1px solid rgba(128, 128, 128, 0.3);
  padding-left: 10% !important;
}

.dot {
  height: 10px;
  width: 10px;
  margin-right: 10px;
  border-radius: 100%;
  display: inline-block;
}
</style>