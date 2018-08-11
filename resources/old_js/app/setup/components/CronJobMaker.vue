<template>
  <div class="grid-5">
    <div class="flyform--group">
      <label>Run Every</label>
      <div class="flyform--group-select">
        <select name="cronjob-maker" v-model="form.everySelection">
          <template v-for="(option, key) in stepOneOptions">
            <option :value="option">{{ key }}</option>
          </template>
        </select>
      </div>
    </div>

    <div class="flyform--group" v-if="showWeekDaysSelector">
      <label>on Day</label>
      <div class="flyform--group-select">
        <select name="week-day-selector" v-model="form.weekDay">
          <template v-for="(value, weekday) in weekDays">
            <option :value="value">{{ weekday }}</option>
          </template>
        </select>
      </div>
    </div>

    <div class="flyform--group" v-if="showDaysSelector">
      <label>on Date</label>
      <div class="flyform--group-select" v-if="showDaysSelector">
        <select name="day-selector" v-model="form.day">
          <template v-for="day in 31">
            <option :value="day">{{ getDayText(day) }}</option>
          </template>
        </select>
      </div>
    </div>

    <div class="flyform--group" v-if="showMonthsSelector">
      <label>of Month</label>
      <div class="flyform--group-select" v-if="showMonthsSelector">
        <select name="month-selector" v-model="form.month">
          <template v-for="(value, month) in months">
            <option :value="value">{{ month }}</option>
          </template>
        </select>
      </div>
    </div>

    <div class="flyform--group" v-if="showHourSelector">
      <label>at Hour</label>
      <div class="flyform--group-select">
        <select name="hour-selector" v-model="form.hour">
          <template v-for="hour in 24">
            <option :value="hour - 1">{{ hour - 1 }}</option>
          </template>
        </select>
      </div>
    </div>

    <div class="flyform--group" v-if="showMinuteSelector">
      <label><span v-if="!showHourSelector">at</span> <span v-if="showHourSelector">and</span> Minute</label>
      <div class="flyform--group-select">
        <select name="minute-selector" v-model="form.minute">
          <template v-for="minute in 60">
            <option :value="minute - 1">{{ minute - 1 }}</option>
          </template>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    cronTiming: {
      default: "* * * * *",
    },
  },
  data() {
    return {
      form: {
        day: 1,
        hour: 0,
        month: 1,
        minute: 0,
        weekDay: 0,
        everySelection: "minute",
      },
      stepOneOptions: {
        Minute: "minute",
        Hour: "hour",
        Day: "day",
        Week: "week",
        Month: "month",
        Year: "year",
      },
      weekDays: {
        Sunday: 0,
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6,
      },
      months: {
        January: 1,
        February: 2,
        March: 3,
        April: 4,
        May: 5,
        June: 6,
        July: 7,
        August: 8,
        September: 9,
        October: 10,
        November: 11,
        December: 12,
      },
    };
  },
  watch: {
    cronJobFormat: function(cronTiming) {
      this.$emit("update:cronTiming", cronTiming);
    },
  },
  methods: {
    getDayText(day) {
      return _.ordinalize(day);
    },
  },
  computed: {
    cronJobFormat() {
      switch (this.form.everySelection) {
        case "minute":
          return "* * * * *";
        case "hour":
          return `${this.form.minute} * * * *`;
        case "day":
          return `${this.form.minute} ${this.form.hour} * * *`;
        case "week":
          return `${this.form.minute} ${this.form.hour} * * ${
            this.form.weekDay
          }`;
        case "month":
          return `${this.form.minute} ${this.form.hour} ${this.form.day} * *`;
        case "year":
          return `${this.form.minute} ${this.form.hour} ${this.form.day} ${
            this.form.month
          } *`;
      }
    },
    showDaysSelector() {
      switch (this.form.everySelection) {
        case "month":
        case "year":
          return true;
        default:
          return false;
      }
    },
    showMonthsSelector() {
      switch (this.form.everySelection) {
        case "year":
          return true;
        default:
          return false;
      }
    },
    showMinuteSelector() {
      switch (this.form.everySelection) {
        case "day":
        case "hour":
        case "week":
        case "month":
        case "year":
          return true;
        default:
          return false;
      }
    },
    showHourSelector() {
      switch (this.form.everySelection) {
        case "day":
        case "week":
        case "month":
        case "year":
          return true;
        default:
          return false;
      }
    },
    showWeekDaysSelector() {
      switch (this.form.everySelection) {
        case "week":
          return true;
        default:
          return false;
      }
    },
  },
};
</script>
