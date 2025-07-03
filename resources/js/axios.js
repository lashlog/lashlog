import axios from "axios";

// Laravel の API エンドポイント（例: localhost）
// axios.defaults.baseURL = "http://localhost";

// または Laravel Sail の場合は
axios.defaults.baseURL = "http://localhost:8000";
// クッキーやセッション情報を送信する場合は true にする
axios.defaults.withCredentials = true;
axios.defaults.headers.common["Accept"] = "application/json";
export default axios;
