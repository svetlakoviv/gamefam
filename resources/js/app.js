import './bootstrap';

import { createApp } from 'vue'
import Counter from './components/Counter.vue'
import HighchartsVue from 'highcharts-vue'
import StatsChart from "./components/statsChart.vue";
import StatsTable from "./components/statsTable.vue"
import StatsExport from "./components/statsExport.vue"

const app = createApp({})

app.component('counter', Counter)
app.component('stats-chart', StatsChart)
app.component('stats-table', StatsTable)
app.component('stats-export', StatsExport)
app.use(HighchartsVue)

app.mount('#app')
