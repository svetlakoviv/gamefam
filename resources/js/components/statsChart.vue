<template>
    <div>
        <div class="input-container">
            <div class="button-container">
                <button @click="fetchData">Fetch Data</button>
                <button @click="exportToCSV">Export to CSV</button>
            </div>
            <label for="from">From:</label>
            <input type="date" id="from" v-model="fromDate">

            <label for="to">To:</label>
            <input type="date" id="to" v-model="toDate">
        </div>
        <highcharts :options="chartOptions"></highcharts>
    </div>
</template>

<script>
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
                    text: 'Online user stats'
                },
                series: [],
                xAxis: {
                    type: 'datetime',
                    tickInterval: 3600 * 1000,
                    dateTimeLabelFormats: {
                        hour: '%H:%M'
                    }
                },
                accessibility: {
                    enabled: false,
                }
            },
            fromDate: '',
            toDate: '',
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
                    const data = response.data.map(point => ([
                        Date.parse(point.created_at),
                        point.online_users
                    ]));
                    this.chartOptions.series = [{
                        type: 'column',
                        name: 'Online Users',
                        data: data
                    }];
                });
        },
        exportToCSV() {
            const from = this.fromDate ? this.fromDate : '';
            const to = this.toDate ? this.toDate : '';

            fetch(`/export/${from}/${to}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // Get the Content-Disposition header to extract the filename
                    const disposition = response.headers.get('Content-Disposition');
                    let filename = 'data.csv'; // Default filename

                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|([^;\n]*))/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    return response.blob().then(blob => ({filename, blob}));
                })
                .then(({filename, blob}) => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Error exporting data:', error);
                });
        }
    }
};
</script>

<style scoped>
button {
    padding: 5px 10px;
    font-size: 16px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
}

button:hover {
    background-color: #0056b3;
}

input {
    margin-right: 10px;
}

.button-container {
    display: flex;
    gap: 10px;
    margin-right: 10px;
}

.input-container {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}
</style>
