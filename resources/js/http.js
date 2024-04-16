import axios from "axios"
import { handleValidationErrors, clearValidationErrors } from "./helpers"

const http =  axios.create({
    // baseURL: 'http://localhost:8000'
    baseURL: 'https://phpstack-1240730-4437357.cloudwaysapps.com/'
})


http.interceptors.response.use( (response) => {
    return response
}, (error) => {
    const domElement = error.config.html
        if (error.response.status === 422) {
            clearValidationErrors(domElement)
            handleValidationErrors(error.response.data.errors, domElement)  
        }
    return Promise.reject(error)
})

export default http