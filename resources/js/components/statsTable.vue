<template>
    <div>
        <h1>Data Table</h1>
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
            fetch('/table') // Replace with your actual API endpoint
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
    background-color: #f2f2f2;
}
</style>
