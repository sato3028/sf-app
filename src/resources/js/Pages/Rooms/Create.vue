<template>
  <div class="outer-container">
    <Navigation :currentRoute="'create'" />
    <div class="content-panel">

        <button class="back-button" @click="goBack">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="back-icon">
          <path fill="#6b4ef5" d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
        </svg>
        <span>戻る</span>
      </button>
        <div class="form-container">
          <h2 class="section-title">ユーザー情報</h2>
          <div class="divider"></div>

          <form @submit.prevent="confirmRoom" class="room-form">
            <!-- ゲーム内の名前 -->
            <div class="input-group">
  <label for="host_username">ゲーム内の名前</label>
  <input v-model="form.host_username" type="text" id="host_username" placeholder="ユーザー名" />
  <p v-if="formErrors.host_username" class="error-message">{{ formErrors.host_username }}</p>
</div>

            <!-- ランク帯入力 -->
            <div class="input-group">
  <label for="host_rank">
    ランク帯:
    <span :style="{ color: getRankColor(form.host_rank) }" class="rank-display">
      {{ getRankText(form.host_rank) }}
    </span>
  </label>
  <v-slider
    v-model="form.host_rank"
    :max="25000"
    :min="1"
    :step="1"
    hide-details
    track-color="#d6d0da"
    thumb-color="#6b4ef5"
    class="input-field"
  ></v-slider>
  <p v-if="form.host_rank === 25000" class="mr-display">
    <span class="text-black">MR:</span><span style="color: #6b4ef5">{{ form.host_mr }}</span><span class="text-black">MR</span>
  </p>
  <v-slider
    v-if="form.host_rank === 25000"
    v-model="form.host_mr"
    :max="2500"
    :min="1000"
    :step="1"
    hide-details
    track-color="#d6d0da"
    thumb-color="#6b4ef5"
    class="input-field"
  ></v-slider>
</div>

            <!-- 使用キャラクターの選択 -->
            <div class="input-group">
                <div class="character-label">
    <label for="host_characters">使用キャラクター</label>
  </div>
  <div class="add-character-container">
    <button class="add-character-button" type="button" @click="showCharacterModal = true">
      キャラクターを追加
    </button>
  </div>
            <div v-if="selectedCharacters.length" class="selected-characters-list">
              <div v-for="(char, index) in selectedCharacters" :key="index" class="character-card">
                <img :src="char.image" alt="" class="character-image" />
                <button class="delete-button" @click="removeCharacter(index)">×</button>
                <p>{{ char.type }}</p>
              </div>
            </div>
</div>

<CharacterModal
            v-if="showCharacterModal"
            :show="showCharacterModal"
            :characters="characters"
            @close="closeCharacterModal"
            @add="addCharacter"
          />

            <h2 class="section-title">募集内容</h2>
            <div class="divider"></div>

            <!-- 募集タイトル -->
            <div class="input-group">
  <label for="title">募集タイトル</label>
  <input v-model="form.title" type="text" id="title" placeholder="募集タイトル" />
  <p v-if="formErrors.title" class="error-message">{{ formErrors.title }}</p>
</div>

            <!-- 募集ランク帯 -->
            <div class="input-group">
  <label for="rank_range">
    募集ランク帯:
    <span :style="{ color: getRankColor(form.rank_range[0]) }" class="rank-display">
      {{ getRankText(form.rank_range[0]) }}
    </span>
    -
    <span :style="{ color: getRankColor(form.rank_range[1]) }" class="rank-display">
      {{ getRankText(form.rank_range[1]) }}
    </span>
  </label>
  <v-range-slider
    v-model="form.rank_range"
    :max="25000"
    :min="1"
    :step="getStep(form.rank_range[0])"
    hide-details
    track-color="#d6d0da"
    thumb-color="#6b4ef5"
    class="input-field"
  ></v-range-slider>
            <p v-if="form.rank_range[1] === 25000" class="mr-display">
                <span class="text-black">MR範囲:</span>{{ form.mr_range[0] }}<span class="text-black">MR</span> - {{ form.mr_range[1] }}<span class="text-black">MR</span>
            </p>
            <v-range-slider
              v-if="form.rank_range[1] === 25000"
              v-model="form.mr_range"
              :max="2500"
              :min="1000"
              :step="1"
              hide-details
              track-color="#d6d0da"
              thumb-color="#6b4ef5"
              class="input-field"
            ></v-range-slider>
          </div>

            <!-- 募集キャラクターの選択 -->
            <div class="input-group">
    <div class="character-label">
      <label for="requested_characters">募集キャラクター</label>
    </div>
    <div class="add-character-container">
      <button class="add-character-button" type="button" @click="showRequestedCharacterModal = true">
        募集キャラクターを追加
      </button>
    </div>
    <div v-if="requestedCharacters.length" class="selected-characters-list">
      <div v-for="(char, index) in requestedCharacters" :key="index" class="character-card">
        <img :src="char.image" alt="" class="character-image" />
        <button class="delete-button" @click="removeRequestedCharacter(index)">×</button>
        <p>{{ char.type }}</p>
      </div>
    </div>
  </div>

  <CharacterModal
      v-if="showRequestedCharacterModal"
      :show="showRequestedCharacterModal"
      :characters="characters"
      @close="closeRequestedCharacterModal"
      @add="addRequestedCharacter"
    />

            <!-- カテゴリーの選択 -->
            <h2 class="section-title">カテゴリー</h2>
            <div class="divider"></div>
          <div class="dropdown" @click="toggleMenu">
            <span>カテゴリーを選択</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="arrow-icon">
              <path v-if="!menuOpen" d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/>
              <path v-else d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z"/>
            </svg>
          </div>
  <div v-if="menuOpen" class="checkbox-list">
    <div v-for="attribute in attributes" :key="attribute.id" :class="['checkbox-item', { selected: form.attributes.includes(attribute.id) }]">
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
  import { reactive, computed, defineProps, ref, nextTick, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  import Navigation from '@/Components/Navigation.vue';
  import CharacterModal from "@/Components/CharacterModal.vue";

  const props = defineProps({
    attributes: Array
  });

  const form = reactive({
    host_username: '',
    title: '',
    host_rank: 1,
    host_mr: 1500,
    rank_range: [1, 10000],
    mr_range: [1000, 2500],
    host_characters: [],
    requested_characters: [],
    attributes: []
  });

  onMounted(() => {
  const savedForm = sessionStorage.getItem('createForm');
  if (savedForm) {
    Object.assign(form, JSON.parse(savedForm));
  }
});

const characters = [
  { name: 'リュウ', image: '/images/ryu_icon.jpg' },
  { name: 'ルーク', image: '/images/luke_icon.jpg' },
    { name: 'ジェイミー', image: '/images/jamie_icon.jpg' },
    { name: '春麗', image: '/images/chunli_icon.jpg' },
    { name: 'ガイル', image: '/images/guile_icon.jpg' },
    { name: 'キンバリー', image: '/images/kimberly_icon.jpg' },
    { name: 'ジュリ', image: '/images/juri_icon.jpg' },
    { name: 'ケン', image: '/images/ken_icon.jpg' },
    { name: 'ブランカ', image: '/images/blanka_icon.jpg' },
    { name: 'ダルシム', image: '/images/dhalsim_icon.jpg' },
    { name: 'エドモンド本田', image: '/images/honda_icon.jpg' },
    { name: 'DJ', image: '/images/deejay_icon.jpg' },
    { name: 'マノン', image: '/images/manon_icon.jpg' },
    { name: 'マリーザ', image: '/images/marisa_icon.jpg' },
    { name: 'JP', image: '/images/jp_icon.jpg' },
    { name: 'ザンギエフ', image: '/images/zangief_icon.jpg' },
    { name: 'リリィ', image: '/images/lily_icon.jpg' },
    { name: 'キャミィ', image: '/images/cammy_icon.jpg' },
    { name: 'ラシード', image: '/images/rashid_icon.jpg' },
    { name: 'アキ', image: '/images/aki_icon.jpg' },
    { name: 'エド', image: '/images/ed_icon.jpg' },
    { name: '豪鬼', image: '/images/gouki_icon.jpg' },
    { name: 'ベガ', image: '/images/vega_icon.jpg' },
    { name: 'テリー', image: '/images/terry_icon.jpg' },
    { name: 'なんでも', image: '/images/all_icon.jpg' },
];

const selectedCharacters = reactive([]); // 選択済みのキャラクターリスト
const showCharacterModal = ref(false); // モーダルの表示状態

const addCharacter = (character) => {
  selectedCharacters.push(character);
};

const confirmRoom = () => {
  if (selectedCharacters.length === 0) {
    console.error("キャラクターが選択されていません。");
    return;
  }

  const formData = {
    ...form,
    host_characters: selectedCharacters,
  };

  router.post("/rooms/confirm", formData);
};

const closeCharacterModal = () => {
  showCharacterModal.value = false;
};

const removeCharacter = (index) => {
  selectedCharacters.splice(index, 1);
};

const requestedCharacters = reactive([]); // 募集キャラクターリスト
const showRequestedCharacterModal = ref(false); // 募集キャラクターモーダルの表示状態

// 募集キャラクターを追加
const addRequestedCharacter = (character) => {
  requestedCharacters.push(character);
};

// 募集キャラクターモーダルを閉じる
const closeRequestedCharacterModal = () => {
  showRequestedCharacterModal.value = false;
};

// 募集キャラクターを削除
const removeRequestedCharacter = (index) => {
  requestedCharacters.splice(index, 1);
};


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

  // ランク表示の処理
  const displayHostRank = computed(() => {
    if (form.host_rank === 25000) {
      return `LP: MASTER, MR: ${form.host_mr || 1000}MR`;
    }
    return `LP: ${form.host_rank}`;
  });

  const displayRankRange = computed(() => {
  if (form.rank_range[1] === 25000) {
    return `LP: ${form.rank_range[0]} - MASTER, MR: ${form.mr_range[0] || 1000} - ${form.mr_range[1] || 2500}`;
  }
  return `LP: ${form.rank_range[0]} - ${form.rank_range[1]}`;
});

  function getRankText(lp) {
    if (lp === 25000) return 'MASTER';  // 25000の場合に必ずMASTERを返す
    const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp <= r.max));
    return range ? range.rank : '';
}



function getRankColor(lp) {
  if (lp === 25000) {
    return '#1BF4B9'; // MASTERの色
  }
  const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp < r.max));
  return range ? range.color : '#645e69'; // マッチがない場合のデフォルトカラー
}

function getStep(lp) {
  if (lp === 0) return 100; // ROOKIE1のときにステップを設定
  const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp <= r.max));
  return range ? 1 : 100; // すべてのランクでステップを1に設定
}


const formErrors = reactive({
  host_username: '',
  host_characters: '',
  title: '',
  requested_characters: '',
});

const validateForm = () => {
  formErrors.host_username = form.host_username ? '' : 'この項目を入力してください';
  formErrors.host_characters = form.host_characters.length ? '' : 'この項目を入力してください';
  formErrors.title = form.title ? '' : 'この項目を入力してください';
  formErrors.requested_characters = form.requested_characters.length ? '' : 'この項目を入力してください';

  // エラーが一つもない場合にtrueを返す
  return !Object.values(formErrors).some(error => error);
};

  const menuOpen = ref(false);

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};


  const goBack = () => {
  router.get('/rooms');
};


  </script>

  <style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

    label {
        font-size: 17px;
    }

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

  .back-button {
  display: flex;
  align-items: center;
  color: #5531FF;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  margin-bottom: 20px;
}

.back-icon {
  width: 20px;
  height: 20px;
  margin-right: 8px;
  fill: #6b4ef5;
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
  font-size: 24px; /* フォントサイズを大きく */
  font-weight: bold;
  color: #6B4EF5; /* 紫色 */
  margin-bottom: 10px; /* 下の線との間隔 */
}

.divider {
  height: 2px;
  background-color: #ddd; /* グレー色 */
  margin-bottom: 20px; /* 下の内容との間隔 */
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


  .dropdown {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px 8px 0 0;
  cursor: pointer;
  background-color: #F9FAFC;
  font-weight: bold;
}

.dropdown:hover {
  background-color: #f0f0f0;
}

.arrow-icon {
  width: 20px;
  height: 20px;
  fill: #6b4ef5;
}

.checkbox-list {
  display: flex;
  flex-wrap: wrap;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 8px 8px;
  padding: 10px;
  background-color: #F9FAFC;
  margin-top: -1px;
  gap: 10px;
  transition: all 0.3s ease;
}

.checkbox-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  background-color: transparent; /* 初期状態は透明 */
  transition: background-color 0.2s ease;
}

.checkbox-item.selected {
  background-color: white; /* 選択時に白背景 */
}

.checkbox-item input[type="checkbox"] {
  appearance: none;
  width: 18px;
  height: 18px;
  margin-right: 8px;
  border: 2px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  background-color: transparent;
  outline: none; /* 青い枠を消す */
  box-shadow: none;
}

.checkbox-item label {
  font-size: 16px;
  font-weight: bold; /* 太字に設定 */
}

.checkbox-item input[type="checkbox"]:checked + label {
  color: #6b4ef5;
}

.checkbox-item input[type="checkbox"]:checked {
  background-color: #6b4ef5;
  border: 1px solid #ccc;
}

.input-group {
  margin-top: 20px;
}

.text-black {
    color: black;
    font-weight: normal;
  }

  .error-message {
  color: red;
  font-size: 14px;
  margin-top: 5px;
}

.character-label {
  margin-bottom: 5px; /* テキストとボタンの間隔を設定 */
}

.add-character-container {
  margin-bottom: 15px; /* ボタンと選択キャラクターリストの間隔を設定 */
}


.add-character-button {
  padding: 8px;
  background-color: #6b4ef5;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.add-character-button:hover {
  background-color: #937cff;
}

.selected-characters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 20px;
}
.character-card {
    position: relative; /* 削除ボタンを配置するため */
  display: flex;
  flex-direction: column;
  align-items: center;
}
.character-image {
    width: 100px; /* 横幅を設定 */
  aspect-ratio: 2 / 1; /* 縦1横2のアスペクト比 */
  object-fit: cover;
  border-radius: 8px;
}

.delete-button {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: #ff6b6b;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 14px;
  cursor: pointer;
}

.delete-button:hover {
  background-color: #ff4c4c;
}
  </style>
