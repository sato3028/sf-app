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
            <p>{{ formData.host_username || '未入力' }}</p>
          </div>

          <div class="input-group">
  <label>ランク帯</label>
  <p :style="{ color: getRankText(formData.host_rank)?.color || '#000' }">
    {{ getRankText(formData.host_rank)?.rank || '不明' }}
  </p>
  <p v-if="formData.host_rank === 25000">
    <span class="text-black">MR:</span> {{ formData.host_mr || '未設定' }} <span class="text-black">MR</span>
  </p>
</div>

          <div class="input-group">
  <label>使用キャラクター</label>
  <div class="character-list">
    <div
      v-for="(character, index) in unwrapProxy(formData.host_characters) || []"
      :key="index"
      class="character-card"
    >
      <img :src="getCharacterImage(character) || '/default-image.jpg'" alt="キャラクター画像" class="character-image" />
    </div>
  </div>
</div>

          <!-- 募集内容セクション -->
          <h2 class="section-title">募集内容</h2>
          <div class="divider"></div>

          <div class="input-group">
            <label>募集タイトル</label>
            <p>{{ formData.title || '未設定' }}</p>
          </div>

          <div class="input-group">
  <label>募集ランク帯</label>
  <p>
    <span :style="{ color: getRankText(formData.rank_range?.[0])?.color || '#000' }">
      {{ getRankText(formData.rank_range?.[0])?.rank || '不明' }}
    </span>
    -
    <span :style="{ color: getRankText(formData.rank_range?.[1])?.color || '#000' }">
      {{ getRankText(formData.rank_range?.[1])?.rank || '不明' }}
    </span>
  </p>
  <p v-if="formData.rank_range?.[1] === 25000">
    <span class="text-black">MR範囲:</span>
    {{ formData.mr_range?.[0] || '未設定' }}MR - {{ formData.mr_range?.[1] || '未設定' }}MR
  </p>
</div>

          <div class="input-group">
            <label>募集キャラクター</label>
            <div class="character-list">
              <div
                v-for="(character, index) in formData.requested_characters || []"
                :key="index"
                class="character-card"
              >
                <img :src="getCharacterImage(character) || '/default-image.jpg'" alt="キャラクター画像" class="character-image" />
              </div>
            </div>
          </div>

          <!-- カテゴリーセクション -->
          <h2 class="section-title">カテゴリー</h2>
          <div class="divider"></div>

          <div class="input-group">
            <label>カテゴリー</label>
            <p>{{ selectedCategories.length ? selectedCategories.join(', ') : 'なし' }}</p>
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
  import { defineProps, computed, onMounted } from 'vue';
  import Navigation from '@/Components/Navigation.vue';

  const props = defineProps({
    formData: Object,
    attributes: Array,
  });

  onMounted(() => {
  const savedForm = sessionStorage.getItem('createForm');
  if (savedForm) {
    try {
      Object.assign(form, JSON.parse(savedForm));
      console.log('セッションデータを復元しました:', form);
    } catch (e) {
      console.error('セッションデータの読み込みに失敗しました:', e);
      sessionStorage.removeItem('createForm');
    }
  } else {
    console.log('セッションデータが存在しません');
  }
});



  const characters = [
  { name: 'RYU', image: '/images/ryu_icon.jpg' },
  { name: 'LUKE', image: '/images/luke_icon.jpg' },
  { name: 'JAMIE', image: '/images/jamie_icon.jpg' },
  { name: 'CHUNLI', image: '/images/chunli_icon.jpg' },
  { name: 'GUILE', image: '/images/guile_icon.jpg' },
  { name: 'KIMBERLY', image: '/images/kimberly_icon.jpg' },
  { name: 'JURI', image: '/images/juri_icon.jpg' },
  { name: 'KEN', image: '/images/ken_icon.jpg' },
  { name: 'BLANKA', image: '/images/blanka_icon.jpg' },
  { name: 'DHALSIM', image: '/images/dhalsim_icon.jpg' },
  { name: 'HONDA', image: '/images/honda_icon.jpg' },
  { name: 'DEEJAY', image: '/images/deejay_icon.jpg' },
  { name: 'MANON', image: '/images/manon_icon.jpg' },
  { name: 'MARISA', image: '/images/marisa_icon.jpg' },
  { name: 'JP', image: '/images/jp_icon.jpg' },
  { name: 'ZANGIEF', image: '/images/zangief_icon.jpg' },
  { name: 'LILY', image: '/images/lily_icon.jpg' },
  { name: 'CAMMY', image: '/images/cammy_icon.jpg' },
  { name: 'RASHID', image: '/images/rashid_icon.jpg' },
  { name: 'AKI', image: '/images/aki_icon.jpg' },
  { name: 'ED', image: '/images/ed_icon.jpg' },
  { name: 'GOUKI', image: '/images/gouki_icon.jpg' },
  { name: 'VEGA', image: '/images/vega_icon.jpg' },
  { name: 'TERRY', image: '/images/terry_icon.jpg' },
  { name: 'ALL', image: '/images/all_icon.jpg' },
];

function getCharacterImage(character) {
  // キャラクター名を取得
  const characterName = typeof character === 'object' && character.name ? character.name : character;

  // 一致するキャラクターを検索
  const matchedCharacter = characters.find((char) => char.name === characterName);
  return matchedCharacter ? matchedCharacter.image : '/default-image.jpg';
}



function submitRoom() {
  router.post('/rooms', props.formData, {
    onSuccess: () => {
      // セッションストレージを削除
      sessionStorage.removeItem('createForm');

      // ログを localStorage に記録
      localStorage.setItem('log', 'セッションストレージを削除完了');

      // 確認用ログ
      console.log('セッションストレージを削除しました:', sessionStorage.getItem('createForm'));

      // サーバーのリダイレクト先へ遷移
    },
    onError: (errors) => {
      console.error('エラー:', errors);
    },
  });
}


function goBack() {
  // 現在のフォームデータを保存
  sessionStorage.setItem('createForm', JSON.stringify(props.formData));

  // 作成ページに戻る
  router.get('/rooms/create', {}, {
    onSuccess: () => {
      console.log('セッションデータを保存しました:', sessionStorage.getItem('createForm'));
    },
    onError: (error) => {
      console.error('戻る操作でエラーが発生しました:', error);
    },
  });
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

    const range = lpRanges.find((r) => lp >= r.min && (r.max === undefined || lp <= r.max));
  console.debug('ランク帯取得 - 結果:', range);
  return range ? { rank: range.rank, color: range.color } : { rank: '不明', color: '#000' };
  }

  const selectedCategories = computed(() => {
  if (!props.attributes || !Array.isArray(props.attributes)) {
    return [];
  }

  return props.formData.attributes
    ?.map((id) => props.attributes.find((attr) => attr.id === id)?.name)
    .filter((name) => name) || [];
});

function unwrapProxy(proxyData) {
  // Proxyデータを確認して解除
  if (proxyData && proxyData.__v_isReactive) {
    return JSON.parse(JSON.stringify(proxyData));
  }
  return proxyData;
}

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

  .character-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.character-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.character-image {
  width: 100px;
  height: 50px;
  border-radius: 8px;
  object-fit: cover;
}
  </style>
