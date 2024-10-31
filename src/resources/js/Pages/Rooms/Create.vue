<template>
    <div class="outer-container">
      <Navigation :currentRoute="'create'" />
      <div class="content-panel">
        <button class="back-button">&lt; 戻る</button>
        <div class="form-container">
          <h2 class="section-title">ユーザー情報</h2>

          <form @submit.prevent="createRoom" class="room-form">
            <!-- ゲーム内の名前 -->
            <div class="input-group">
              <label for="host_username">ゲーム内の名前</label>
              <input v-model="form.host_username" type="text" id="host_username" placeholder="ユーザー名" required />
            </div>

            <!-- ランク帯入力 -->
            <div class="input-group">
              <label for="host_rank">ランク帯: <span class="rank-display">{{ displayHostRank }}</span></label>
              <v-slider v-model="form.host_rank" :max="25000" :min="1" :step="1" hide-details class="input-field"></v-slider>
              <p v-if="form.host_rank === 25000" class="mr-display">MR: {{ form.host_mr }}</p>
              <v-slider v-if="form.host_rank === 25000" v-model="form.host_mr" :max="2500" :min="1000" :step="1" hide-details class="input-field"></v-slider>
            </div>

            <!-- 使用キャラクターの選択 -->
            <div class="input-group">
              <label for="host_characters">使用キャラクター</label>
              <v-select v-model="form.host_characters" :items="characters" label="キャラクター" chips multiple dense outlined></v-select>
            </div>

            <h2 class="section-title">募集内容</h2>

            <!-- 募集タイトル -->
            <div class="input-group">
              <label for="title">募集タイトル</label>
              <input v-model="form.title" type="text" id="title" placeholder="募集タイトル" required />
            </div>

            <!-- 募集ランク帯 -->
            <div class="input-group">
              <label for="rank_range">募集ランク帯</label>
              <v-range-slider v-model="form.rank_range" :max="25000" :min="1" :step="1" hide-details></v-range-slider>
              <p>ランク範囲: {{ form.rank_range[0] }} - {{ form.rank_range[1] }}</p>
              <v-range-slider v-if="form.rank_range[1] === 25000" v-model="form.mr_range" :max="2500" :min="1000" :step="1" hide-details></v-range-slider>
              <p v-if="form.rank_range[1] === 25000">MR範囲: {{ form.mr_range[0] }} - {{ form.mr_range[1] }}</p>
            </div>

            <!-- 募集キャラクターの選択 -->
            <div class="input-group">
              <label for="requested_characters">募集キャラクター</label>
              <v-select v-model="form.requested_characters" :items="characters" label="キャラクター" chips multiple dense outlined></v-select>
            </div>

            <!-- カテゴリーの選択 -->
            <h2 class="section-title">カテゴリー</h2>
            <div class="attributes-container input-field">
              <div v-for="attribute in attributes" :key="attribute.id" class="checkbox-group">
                <input type="checkbox" :id="'attribute' + attribute.id" v-model="form.attributes" :value="attribute.id">
                <label :for="'attribute' + attribute.id">{{ attribute.name }}</label>
              </div>
            </div>

            <!-- 確認ボタン -->
            <div class="input-group">
              <button type="submit" class="confirm-button">確認</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { reactive, computed, defineProps } from 'vue';
  import { router } from '@inertiajs/vue3';
  import Navigation from '@/Components/Navigation.vue';

  const props = defineProps({
    attributes: Array
  });

  const characters = ['リュウ', 'ルーク', 'ジェイミー', '春麗', 'ガイル', 'キンバリー', 'ジュリ', 'ケン', 'ブランカ', 'ダルシム', 'エドモンド本田', 'DJ', 'マノン', 'マリーザ', 'JP', 'ザンギエフ', 'リリィ', 'キャミィ', 'ラシード', 'アキ', 'エド', '豪鬼'];

  const form = reactive({
    host_username: '',
    title: '',
    host_rank: 1,
    host_mr: null,
    rank_range: [1, 25000],
    mr_range: [1000, 2500],
    host_characters: [],
    requested_characters: [],
    attributes: []
  });

  // ランク表示の処理
  const displayHostRank = computed(() => {
    if (form.host_rank === 25000) {
      return `LP: MASTER, MR: ${form.host_mr || 1000}MR`;
    }
    return `LP: ${form.host_rank}`;
  });

  // 募集ランク範囲の表示
  const displayStartRank = computed(() => form.rank_range[0] || '未設定');
  const displayEndRank = computed(() => form.rank_range[1] ? `${form.rank_range[1]} LP` : '未設定');

  // ホストのランクのバリデーション処理
  const validateHostRank = () => {
    if (form.host_rank > 25000) form.host_rank = 25000;
    if (form.host_rank < 1) form.host_rank = 1;
  };

  // MR範囲のバリデーション処理
  const validateMRRange = () => {
    if (form.mr_range[0] > form.mr_range[1]) form.mr_range[0] = form.mr_range[1];
    if (form.mr_range[1] < form.mr_range[0]) form.mr_range[1] = form.mr_range[0];
  };

  // ルーム作成処理
  const createRoom = () => {
    const formData = {
      title: form.title,
      host_username: form.host_username,
      host_characters: form.host_characters,
      rank_range: form.rank_range,
      mr_range: form.mr_range,
      host_rank: form.host_rank,
      requested_characters: form.requested_characters,
      attributes: form.attributes,
    };

    router.post('/rooms', formData, {
      onSuccess: () => {
        router.visit('/');
      },
      onError: (errors) => {
        console.error('エラー:', errors);
      },
    });
  };
  </script>

  <style scoped>
  .outer-container {
    display: flex;
    height: 100vh;
  }

  .content-panel {
    flex: 1;
    padding: 40px;
    margin-left: 290px;
    overflow-y: auto;
  }

  .back-button {
    color: #5531FF;
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 20px;
  }

  .form-container {
    background: white;
    padding: 30px; /* 横幅を少し狭く調整 */
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    max-width: 900px; /* 最大幅を設定 */
    margin: 0 auto;
    margin-bottom: 40px; /* フォームの下に空間を追加 */
}


  .section-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
  }

  .input-group {
    margin-bottom: 20px;
  }

  input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
  }

  .rank-display {
    font-weight: bold;
    color: #6B4EF5;
  }

  .mr-display {
    font-weight: bold;
    color: #6B4EF5;
  }

  .confirm-button {
    width: 100%;
    padding: 10px;
    background-color: #6B4EF5;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }

  .confirm-button:hover {
    background-color: #937cff;
  }

  .attributes-container {
    display: flex;
    flex-direction: column;
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }

  .checkbox-group input[type="checkbox"] {
    margin-right: 10px;
  }
  </style>
