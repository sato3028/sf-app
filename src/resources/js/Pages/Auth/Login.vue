<script setup>
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="login-container">
        <Head title="Google Login" />

        <div class="login-left">
            <div class="title-header">
                <h1 class="app-title">StreetMatch</h1>
            </div>

            <div class="login-content">
                <a :href="route('login.google')" class="google-login-btn">
                    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" alt="Google Sign-In" class="google-btn-img">
                </a>
            </div>
        </div>

        <div class="login-right">
            <img src="https://res.cloudinary.com/dqaxgeag8/image/upload/v1726342379/web_1_atmnad.png" alt="Background Image" class="login-image">
            <div class="overlay-text">
                <h2>サンプルテキスト</h2>
                <p>サンプルテキスト</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-container {
    display: flex;
    height: 100vh;
}

.login-left {
    width: 50%;
    background-color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
}

.title-header {
    position: absolute;
    top: 20px;
    left: 20px;
}

.app-title {
    font-size: 1.5em;
    color: #333;
}

.login-content {
    text-align: center;
    margin-top: -50px;
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
    width: 240px;
}

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
    object-fit: cover;
}

.overlay-text {
    position: absolute;
    padding: 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    color: white;
    text-align: center;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.overlay-text h2 {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.overlay-text p {
    font-size: 1.2em;
}
</style>
