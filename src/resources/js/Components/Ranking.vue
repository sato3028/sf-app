<template>
    <div class="ranking-container">
      <div class="ranking-header">
        <button class="arrow-button" @click="switchRanking('previous')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="arrow-icon">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
          </svg>
        </button>
        <h2 class="ranking-title">{{ rankingTitle }}</h2>
        <button class="arrow-button" @click="switchRanking('next')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="arrow-icon">
            <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
          </svg>
        </button>
      </div>
      <div class="ranking-list">
        <p class="no-data">データが不足しています</p>
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';

  const rankingTitle = ref('週間優良ユーザー');

  const switchRanking = (direction) => {
    const titles = ['日間優良ユーザー', '週間優良ユーザー', '月間優良ユーザー'];
    const currentIndex = titles.indexOf(rankingTitle.value);

    if (direction === 'previous') {
      rankingTitle.value = titles[(currentIndex - 1 + titles.length) % titles.length];
    } else if (direction === 'next') {
      rankingTitle.value = titles[(currentIndex + 1) % titles.length];
    }
  };
  </script>

  <style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

    .ranking-container {
      background-color: #6B4EF5;
      width: 300px;
      height: 400px;
      border-radius: 16px;
      padding: 20px;
      margin-right: 50px;
      margin-top: 50px;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-y: auto;
      font-family: "Noto Sans JP", sans-serif;
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
      cursor: pointer;
    }

    .arrow-icon {
      width: 20px;
      height: 20px;
      fill: white;
    }

    .ranking-list {
      width: 100%;
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 100px;
      height: 100%;
    }

    .no-data {
      color: white;
      font-size: 17px;
      font-weight: bold;
    }
  </style>
