import axios from "axios"
import { handleValidationErrors, clearValidationErrors } from "./helpers"

const http =  axios.create({
    baseURL: 'http://localhost:8000'
})


http.interceptors.response.use( (response) => {
    return response
}, (error) => {
    const domElement = error.config.html
        if (error.response.status === 422) {
            clearValidationErrors(domElement)
            handleValidationErrors(error.response.data.errors, domElement)  
        }
        if (error.response.status === 401) {
            location.href = '/'  
        }
    return Promise.reject(error)
})

export default http