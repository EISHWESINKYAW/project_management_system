import axios from "axios";
import { clearLocalStorage } from "./helper";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
    withCredentials: true,
    headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "*",
        Accept: "application/json",
    },
});

// Auth token interceptor
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => {
        return Promise.resolve(response);
    },
    (error) => {
        if (error.status == 401) {
            clearLocalStorage();
        }
        return Promise.reject(error);
    }
);

const get = async (url, config = {}) => {
    const response = await api.get(url, config);
    return response;
};

const post = async (url, data = {}, config = {}) => {
    const response = await api.post(url, data, config);
    return response;
};

const postWithFile = async (url, formData, config = {}) => {
    const response = await api.post(url, formData, {
        ...config,
        headers: {
            ...config.headers,
            "Content-Type": "multipart/form-data",
        },
    });
    return response;
};

const put = async (url, data = {}, config = {}) => {
    const response = await api.put(url, data, config);
    return response;
};

const remove = async (url, config = {}) => {
    const response = await api.delete(url, config);
    return response;
};

// Export all
export default {
    get,
    post,
    postWithFile,
    put,
    delete: remove,
};
