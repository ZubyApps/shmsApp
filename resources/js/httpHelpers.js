import http from "./http";

// // Utility function for GET requests
// async function fetchData(url, options = {}, errorMessage = 'Request failed') {
//     try {
//         const response = await http.get(url, options);
//         if (response.status < 200 || response.status > 300) {
//             throw new Error(`${errorMessage}: Status ${response.status}`);
//         }
//         return response.data;
//     } catch (error) {
//         console.error(error);
//         throw error; // Let the caller handle the error
//     }
// }

async function httpRequest(url, method = 'GET', options = {}, errorMessage = `${method} request failed`) {
    try {
        const httpMethod = method.toLowerCase();
        let response;

        switch (httpMethod) {
            case 'get':
                response = await http.get(url, options);
                break;
            case 'post':
                response = await http.post(url, options.data || {}, options);
                break;
            case 'put':
                response = await http.put(url, options.data || {}, options);
                break;
            case 'patch':
                response = await http.patch(url, options.data || {}, options);
                break;
            case 'delete':
                response = await http.delete(url, options);
                break;
            default:
                throw new Error(`Unsupported HTTP method: ${method}`);
        }

        if (response.status < 200 || response.status > 300) {
            throw new Error(`${errorMessage}: Status ${response.status}`);
        }
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

// Export the function
export { httpRequest };