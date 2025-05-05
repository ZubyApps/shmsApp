import http from "./http";

// Utility function for GET requests
async function fetchData(url, options = {}, errorMessage = 'Request failed') {
    try {
        const response = await http.get(url, options);
        if (response.status < 200 || response.status > 300) {
            throw new Error(`${errorMessage}: Status ${response.status}`);
        }
        return response.data;
    } catch (error) {
        console.error(error);
        throw error; // Let the caller handle the error
    }
}

// Export the function
export { fetchData };