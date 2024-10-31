<template>
    <div class="navigation-outer-container">
    <nav class="navigation-container">
      <!-- 左の列：ロゴを配置 -->
      <div class="left-column">
        <div class="grid-container">
          <!-- 空白のカラム -->
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>

          <!-- ルーム一覧のロゴ -->
          <div class="grid-item logo" :class="{ active: currentRoute === 'index' }">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="30" height="30">
              <path fill="#ffffff" d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/>
            </svg>
          </div>

          <!-- ルーム作成のロゴ -->
          <div class="grid-item logo" :class="{ active: currentRoute === 'create' }">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="30" height="30">
              <path fill="#ffffff" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7-14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
            </svg>
          </div>

          <!-- 残りの空白のカラム -->
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
        </div>
      </div>

      <!-- 右の列：テキストを配置 -->
      <div class="right-column">
        <div class="grid-container">
          <!-- タイトル -->
          <div class="grid-item title">
            <h1 class="title-text">タイトル</h1>
          </div>

          <!-- 空白のカラム -->
          <div class="grid-item"></div>
          <div class="grid-item"></div>

          <!-- ルーム一覧 -->
          <div class="grid-item text-left">
            <button @click="goToIndex" class="nav-button">ルーム一覧</button>
          </div>

          <!-- ルーム作成 -->
          <div class="grid-item text-left">
            <button @click="goToCreateRoom" class="nav-button">ルーム作成</button>
          </div>

          <!-- 残りの空白のカラム -->
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>
          <div class="grid-item"></div>

          <!-- ログインテキスト (17番目のカラムに配置) -->
          <div class="grid-item text-left">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20" style="margin-right: 20px;">
              <path fill="#ffffff" d="M217.9 105.9L340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1L32 320c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM352 416l64 0c17.7 0 32-14.3 32-32l0-256c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c53 0 96 43 96 96l0 256c0 53-43 96-96 96l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
            </svg>
            <button @click="goToProfile" class="nav-button">ログイン</button>
          </div>
        </div>
      </div>
    </nav>
    </div>
  </template>

  <script setup>
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    currentRoute: String
  });

  const goToIndex = () => {
    router.get('/rooms');
  };

  const goToCreateRoom = () => {
    router.get('/rooms/create');
  };

  const goToProfile = () => {
    router.get('/profile');
  };
  </script>

  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

  nav {
    font-family: "Noto Sans JP", sans-serif;
  }

  .navigation-outer-container{
    position: fixed;
    height: 100vh;
  }

  /* 左の列は200px固定、右の列は600px固定 */
  .navigation-container {
    display: grid;
    grid-template-columns: 70px 220px;
    height: 100vh; /* 縦方向全体に展開 */
  }

  .left-column {
    background-color: #937CFF;
    display: grid;
    grid-template-rows: repeat(17, 1fr);
    padding-top: 20px;
  }

  .right-column {
    background-color: #6B4EF5;
    display: grid;
    grid-template-rows: repeat(17, 1fr);
    padding-top: 20px;
  }

  .grid-container {
    display: grid;
    grid-template-rows: repeat(17, 1fr);
    gap: 10px;
    width: 100%;
    height: 100%;
  }

  .grid-item {
    display: flex;
    align-items: center;
    height: 100%;
    justify-content: center;
  }

  .title {
    justify-content: center; /* タイトルは中央揃え */
  }

  .text-left {
    justify-content: flex-start;
    padding-left: 20px;
  }

  /* ログインロゴとテキストの間隔を広げる */
  .grid-item.text-left svg {
    width: 30px;
    height: 30px;
    margin-right: 20px; /* ロゴとテキストの間に余白を追加 */
  }

  /* ルートにいる場合、ロゴの左に白い線を表示 */
  .logo.active {
    border-left: 4px solid white;
  }

  .nav-button {
    background-color: transparent;
    border: none;
    color: white;
    font-size: 1.3em;
    font-weight: 550;
    cursor: pointer;
  }

  .nav-button:hover {
    color: #ddd;
  }

  .active .nav-button {
    color: #ffcc00;
  }

  .title-text {
    color: white;
    font-size: 1.5em;
    text-align: center;
    line-height: 2em;
  }
  </style>
