<template>
    <div class="outer-container">
      <Navigation :currentRoute="'confirm'" />
      <div class="content-panel">
        <h2 class="section-title">確認</h2>
        <div class="divider"></div>

        <div class="form-container">
          <!-- ユーザー情報セクション -->
          <h2 class="section-title">ユーザー情報</h2>
          <div class="divider"></div>

          <div class="input-group">
            <label>ゲーム内の名前</label>
            <p>{{ formData.host_username }}</p>
          </div>

          <div class="input-group">
  <label>ランク帯</label>
  <p :style="{ color: getRankText(formData.host_rank).color }">{{ getRankText(formData.host_rank).rank }}</p>
  <p v-if="formData.host_rank === 25000">
    <span class="text-black">MR:</span> {{ formData.host_mr }} <span class="text-black">MR</span>
  </p>
</div>

          <div class="input-group">
            <label>使用キャラクター</label>
            <p>{{ formData.host_characters.join(', ') }}</p>
          </div>

          <!-- 募集内容セクション -->
          <h2 class="section-title">募集内容</h2>
          <div class="divider"></div>

          <div class="input-group">
            <label>募集タイトル</label>
            <p>{{ formData.title }}</p>
          </div>

          <div class="input-group">
  <label>募集ランク帯</label>
  <p>
    <span :style="{ color: getRankText(formData.rank_range[0]).color }">
      {{ getRankText(formData.rank_range[0]).rank }}
    </span>
    -
    <span :style="{ color: getRankText(formData.rank_range[1]).color }">
      {{ getRankText(formData.rank_range[1]).rank }}
    </span>
  </p>
  <p v-if="formData.rank_range[1] === 25000">
    <span class="text-black">MR範囲:</span> {{ formData.mr_range[0] }}MR - {{ formData.mr_range[1] }}MR
  </p>
</div>

          <div class="input-group">
            <label>募集キャラクター</label>
            <p>{{ formData.requested_characters.join(', ') }}</p>
          </div>

          <!-- カテゴリーセクション -->
          <h2 class="section-title">カテゴリー</h2>
        <div class="divider"></div>

        <div class="input-group">
  <label>カテゴリー</label>
  <p>{{ formData.attributeNames ? formData.attributeNames.join(', ') : 'なし' }}</p>
</div>


          <!-- ボタン -->
          <div class="button-group">
            <button @click="submitRoom" class="confirm-button">ルーム作成</button>
            <button @click="goBack" class="back-button">戻る</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { router } from '@inertiajs/vue3';
  import { defineProps, computed } from 'vue';
  import Navigation from '@/Components/Navigation.vue';

  const props = defineProps({
    formData: Object,
    attributes: Array,
  });

  function submitRoom() {
    router.post('/rooms', props.formData, {
      onSuccess: () => {
        router.visit('/');
      },
      onError: (errors) => {
        console.error('エラー:', errors);
      },
    });
  }

  function goBack() {
    router.get('/rooms/create');
  }

  function getRankText(lp) {
    const lpRanges = [
      { rank: 'MASTER', min: 25000, color: '#1BF4B9' },
      { rank: 'DIAMOND5', min: 23800, max: 24999, color: '#D85899' },
      { rank: 'DIAMOND4', min: 22600, max: 23800, color: '#D85899' },
      { rank: 'DIAMOND3', min: 21400, max: 22600, color: '#D85899' },
      { rank: 'DIAMOND2', min: 20200, max: 21400, color: '#D85899' },
      { rank: 'DIAMOND1', min: 19000, max: 20200, color: '#D85899' },
      { rank: 'PLATINUM5', min: 17800, max: 19000, color: '#16B4E7' },
      { rank: 'PLATINUM4', min: 16600, max: 17800, color: '#16B4E7' },
      { rank: 'PLATINUM3', min: 15400, max: 16600, color: '#16B4E7' },
      { rank: 'PLATINUM2', min: 14200, max: 15400, color: '#16B4E7' },
      { rank: 'PLATINUM1', min: 13000, max: 14200, color: '#16B4E7' },
      { rank: 'GOLD5', min: 12200, max: 13000, color: '#D7AA4E' },
      { rank: 'GOLD4', min: 11800, max: 12200, color: '#D7AA4E' },
      { rank: 'GOLD3', min: 11400, max: 11800, color: '#D7AA4E' },
      { rank: 'GOLD2', min: 9800, max: 11400, color: '#D7AA4E' },
      { rank: 'GOLD1', min: 9000, max: 9800, color: '#D7AA4E' },
      { rank: 'SILVER5', min: 8200, max: 9000, color: '#333132' },
      { rank: 'SILVER4', min: 7400, max: 8200, color: '#333132' },
      { rank: 'SILVER3', min: 6600, max: 7400, color: '#333132' },
      { rank: 'SILVER2', min: 5800, max: 6600, color: '#333132' },
      { rank: 'SILVER1', min: 5000, max: 5800, color: '#333132' },
      { rank: 'BRONZE5', min: 4600, max: 5000, color: '#9B754F' },
      { rank: 'BRONZE4', min: 4200, max: 4600, color: '#9B754F' },
      { rank: 'BRONZE3', min: 3800, max: 4200, color: '#9B754F' },
      { rank: 'BRONZE2', min: 3400, max: 3800, color: '#9B754F' },
      { rank: 'BRONZE1', min: 3000, max: 3400, color: '#9B754F' },
      { rank: 'IRON5', min: 2600, max: 3000, color: '#2A2A2A' },
      { rank: 'IRON4', min: 2200, max: 2600, color: '#2A2A2A' },
      { rank: 'IRON3', min: 1800, max: 2200, color: '#2A2A2A' },
      { rank: 'IRON2', min: 1400, max: 1800, color: '#2A2A2A' },
      { rank: 'IRON1', min: 1000, max: 1400, color: '#2A2A2A' },
      { rank: 'ROOKIE5', min: 800, max: 1000, color: '#00001C' },
      { rank: 'ROOKIE4', min: 600, max: 800, color: '#00001C' },
      { rank: 'ROOKIE3', min: 400, max: 600, color: '#00001C' },
      { rank: 'ROOKIE2', min: 200, max: 400, color: '#00001C' },
      { rank: 'ROOKIE1', min: 0, max: 200, color: '#00001C' }
    ];
    if (lp === 25000) return { rank: 'MASTER', color: '#1BF4B9' };

const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp <= r.max));
return range ? { rank: range.rank, color: range.color } : { rank: '', color: '#645e69' };
  }

  const selectedCategories = computed(() => {
  if (!props.attributes || !Array.isArray(props.attributes)) {
    return []; // attributesが存在しない場合、空の配列を返す
  }

  return props.formData.attributes
    .map(id => props.attributes.find(attr => attr.id === id)?.name)
    .filter(name => name); // undefined のフィルタリング
});
  </script>

  <style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

  .outer-container {
    display: flex;
    height: 100vh;
    font-family: "Noto Sans JP", sans-serif;
  }

  .content-panel {
    flex: 1;
    padding: 40px;
    margin-left: 290px;
    overflow-y: auto;
  }

  .form-container {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin: 0 auto;
    margin-bottom: 40px;
  }

  .section-title {
    font-size: 24px;
    font-weight: bold;
    color: #6B4EF5;
    margin-bottom: 10px;
  }

  .divider {
    height: 2px;
    background-color: #ddd;
    margin-bottom: 20px;
  }

  .input-group {
    margin-bottom: 20px;
  }

  .input-group label {
  font-size: 16px; /* 少し大きく */
  font-weight: bold; /* 太字 */
}


  .confirm-button {
    width: 100%;
    padding: 10px;
    background-color: #6B4EF5;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 20px;
  }

  .confirm-button:hover {
    background-color: #937cff;
  }

  .back-button {
    width: 100%;
    padding: 10px;
    background-color: #cccccc;
    color: black;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
  }

  .button-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .text-black {
    color: black;
    font-weight: normal;
  }
  </style>
