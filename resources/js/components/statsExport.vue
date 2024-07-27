<script>
export default {
    methods: {
        exportToCSV() {
            fetch('/export') // Replace with your actual API endpoint for exporting CSV
                .then(response => {
                    // Ensure the response is valid
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
                            filename = matches[1].replace(/['"]/g, ''); // Clean the filename
                        }
                    }

                    return response.blob().then(blob => ({ filename, blob }));
                })
                .then(({ filename, blob }) => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename; // Use the filename extracted from the response
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

<template>
    <div>
        <button @click="exportToCSV">Export to CSV</button>
    </div>
</template>

<style scoped>
button {
    padding: 10px 20px;
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
</style>
