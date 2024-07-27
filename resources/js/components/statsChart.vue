<template>
    <div>
        <label for="from">From:</label>
        <input type="date" id="from" v-model="fromDate">

        <label for="to">To:</label>
        <input type="date" id="to" v-model="toDate">

        <button @click="fetchData">Fetch Data</button>
        <highcharts :options="chartOptions"></highcharts>
    </div>
</template>

<script>
import Highcharts from 'highcharts';
import HighchartsVue from 'highcharts-vue';
import axios from 'axios';

export default {
    name: 'StatsChart',
    components: {
        highcharts: HighchartsVue.component
    },
    data() {
        return {
            chartOptions: {
                title: {
                    text: 'Stats Data'
                },
                series: []
            }
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            const from = this.fromDate ? this.fromDate : '';
            const to = this.toDate ? this.toDate : '';

            axios.get(`/stats/${from}/${to}`)
                .then(response => {
                    const data = response.data.map(point => ({
                        name: point.created_at,
                        y: point.online_users
                    }));
                    this.chartOptions.series = [{
                        type: 'column',
                        data: data
                    }];
                });
        }
    }
};
</script>
