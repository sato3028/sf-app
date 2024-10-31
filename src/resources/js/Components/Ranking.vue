<template>
    <div class="ranking-container">
      <div class="ranking-header">
        <button class="arrow-button">&lt;</button>
        <h2 class="ranking-title">週間優良ユーザー</h2>
        <button class="arrow-button">&gt;</button>
      </div>
      <div class="ranking-list">
        <div class="ranking-item" v-for="rank in 5" :key="rank">
          <span class="ranking-number">{{ rank }}.</span>
          <img src="/path/to/avatar.png" alt="avatar" class="ranking-avatar" />
          <div class="ranking-line"></div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  </script>

  <style scoped>
  .ranking-container {
    background-color: #6B4EF5;
    width: 300px;
    height: 400px; /* コンテンツの高さを指定 */
    border-radius: 16px;
    padding: 20px;
    margin-right: 50px;
    margin-top: 50px; /* 上に余白を追加 */
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto; /* コンテンツが溢れた時にスクロールする */
  }

  .ranking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 20px;
  }

  .ranking-title {
    color: white;
    font-size: 18px;
    font-weight: bold;
  }

  .arrow-button {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
  }

  .ranking-list {
    width: 100%;
  }

  .ranking-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }

  .ranking-number {
    color: white;
    font-size: 24px;
    font-weight: bold;
    margin-right: 10px;
  }

  .ranking-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
  }

  .ranking-line {
    flex-grow: 1;
    height: 2px;
    background-color: white;
  }
  </style>
