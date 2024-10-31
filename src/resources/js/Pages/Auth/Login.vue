<script setup>
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="login-container">
        <Head title="Google Login" />

        <!-- 左半分（白い背景とGoogleログインボタン） -->
        <div class="login-left">
            <!-- 左上に適当なタイトル -->
            <div class="title-header">
                <h1 class="app-title">Welcome to Our App</h1>
            </div>

            <!-- コンテンツ部分 -->
            <div class="login-content">
                <h2 class="login-title">Log in with Google</h2>
                <a :href="route('login.google')" class="google-login-btn">
                    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Google Sign-In" class="google-btn-img">
                </a>
            </div>
        </div>

        <!-- 右半分（画像とグラスモーフィズムテキスト） -->
        <div class="login-right">
            <img src="https://res.cloudinary.com/dqaxgeag8/image/upload/v1726342379/web_1_atmnad.png" alt="Background Image" class="login-image">
            <div class="overlay-text">
                <h2>Join the Ultimate Experience</h2>
                <p>Connect and enjoy seamless gaming experiences</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-container {
    display: flex;
    height: 100vh; /* 高さを画面全体に合わせる */
}

.login-left {
    width: 50%;
    background-color: white; /* 左側を白い背景 */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
}

/* 左上にタイトルを配置 */
.title-header {
    position: absolute;
    top: 20px;
    left: 20px;
}

.app-title {
    font-size: 1.5em;
    color: #333;
}

/* コンテンツ部分のスタイルを調整 */
.login-content {
    text-align: center;
    margin-top: -50px; /* 上に配置 */
}

.login-title {
    font-size: 1.8em;
    margin-bottom: 20px;
    color: #333;
}

.google-login-btn {
    display: inline-block;
}

.google-btn-img {
    width: 240px; /* Googleボタンのサイズを調整 */
}

/* 右側部分 */
.login-right {
    position: relative;
    width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* 画像を縦横比を維持しつつ全体に表示 */
}

/* グラスモーフィズム効果を持つテキストボックス */
.overlay-text {
    position: absolute;
    padding: 20px;
    background: rgba(255, 255, 255, 0.2); /* 半透明の背景色 */
    border-radius: 16px;
    backdrop-filter: blur(10px); /* 背景のぼかし効果 */
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); /* わずかな影を追加 */
    color: white;
    text-align: center;
    top: 50%; /* 上下中央に配置 */
    left: 50%;
    transform: translate(-50%, -50%); /* 中央に揃えるためにtransformを使用 */
}

.overlay-text h2 {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.overlay-text p {
    font-size: 1.2em;
}
</style>
