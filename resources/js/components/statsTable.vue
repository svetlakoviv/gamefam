<template>
    <div>
        <h2 class="table-title">Average and Max values for 1 week</h2>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Average</th>
                <th>Max</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(value, key) in data" :key="key">
                <td>{{ key }}</td>
                <td>{{ value.avg }}</td>
                <td>{{ value.max }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            data: {}
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            fetch('/table')
                .then(response => response.json())
                .then(data => {
                    this.data = data;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }
    }
};
</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #c0c0c0;
}

tr:nth-child(odd) {
    background-color: #f2f2f2;
}

tr:nth-child(even) {
    background-color: #ffffff;
}

.table-title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin: 20px 0;
    color: #333;
    font-family: Arial, sans-serif;
}
</style>
