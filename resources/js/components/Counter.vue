<template>
    <div>
        <p v-if="userStat">The user count is: {{ userStat.online_users }}</p>
        <p v-else>Loading...</p>
    </div>
</template>

<script>
export default {
    name: 'LatestUserStat',
    data() {
        return {
            userStat: null,
            interval: null
        };
    },
    mounted() {
        this.fetchData(); // Fetch data immediately when the component is mounted
        this.startFetching(); // Start fetching data at intervals
    },
    beforeDestroy() {
        clearInterval(this.interval); // Clear the interval when the component is destroyed
    },
    methods: {
        async fetchData() {
            try {
                let response = await fetch('/latest');
                let data = await response.json();
                this.userStat = data;
            } catch (error) {
                console.error('Error fetching latest user stat:', error);
            }
        },
        startFetching() {
            this.interval = setInterval(() => {
                this.fetchData(); // Fetch data every 5 seconds
            }, 5000); // 5000 milliseconds = 5 seconds
        }
    }
};
</script>
