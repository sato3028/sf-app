<template>
    <div class="outer-container">
      <!-- 左側のNavigation -->
      <Navigation :currentRoute="'index'" />

      <!-- 中央のコンテンツ部分 -->
      <div class="content-container">
        <div class="content-panel">
          <div class="title-and-filter-button">
            <h1 class="title">ルーム一覧</h1>
            <!-- フィルタートグルボタン -->
            <button @click="toggleFilter" class="filter-toggle-button">
              {{ isFilterVisible ? 'フィルターを隠す' : 'フィルターを表示' }}
            </button>
          </div>

          <!-- フィルターセクション -->
          <div v-if="isFilterVisible" class="filter-container">
            <h2>フィルター</h2>
            <div class="input-group">
              <label for="host_characters">ホストの使用キャラクター:</label>
              <v-select
                v-model="filters.host_characters"
                :items="characters"
                label="キャラクター"
                chips
                multiple
                dense
                outlined
                class="input-field"
              ></v-select>
            </div>

            <div class="input-group">
              <label for="host_rank">ホストのランク範囲: {{ filters.host_rank[0] }} LP - {{ filters.host_rank[1] }} LP</label>
              <v-range-slider
                v-model="filters.host_rank"
                :max="25000"
                :min="1"
                :step="1"
                hide-details
                class="input-field"
              ></v-range-slider>
            </div>

            <div class="input-group">
              <label for="attributes">カテゴリー:</label>
              <div class="attributes-container input-field">
                <div v-if="attributes && attributes.length > 0" v-for="attribute in attributes" :key="attribute.id" class="checkbox-group">
                  <input type="checkbox" :id="'attribute' + attribute.id" v-model="filters.attributes" :value="attribute.id">
                  <label :for="'attribute' + attribute.id">{{ attribute.name }}</label>
                </div>
                <p v-else>カテゴリーが見つかりません。</p>
              </div>
            </div>

            <button @click="applyFilters" class="filter-button">フィルターを適用</button>
            <button @click="resetFilters" class="reset-button">リセット</button>
          </div>

          <!-- ルーム一覧表示 -->
          <div class="room-list">
            <div v-if="filteredRooms.length === 0">
              <p>条件に合うルームが見つかりませんでした。</p>
            </div>
            <div v-else>
              <div v-for="room in filteredRooms" :key="room.id" class="room">
                <h2 class="room-title">{{ room.title }}</h2>

                <!-- タイトルとカテゴリー間の線 -->
                <hr class="separator" />

                <!-- カテゴリー -->
                <div class="room-categories">
                  <span v-for="attribute in room.attributes" :key="attribute.id" class="category-chip">{{ attribute.name }}</span>
                </div>

                <!-- 募集ランクとキャラクター -->
                <div class="room-recruit-info">
                  <p>
                    募集ランク:
                    <span v-if="room.max_rank === 25000">
                      {{ getMasterRankRange(room.min_rank, room.min_mr, room.max_mr) }}
                    </span>
                    <span v-else>
                      {{ getRankRange(room.min_rank, room.max_rank) }}
                    </span>
                  </p>
                  <p class="spaced">募集キャラクター: {{ room.requested_characters || '未設定' }}</p>
                </div>

                <!-- 募集ランクとユーザー名間の線 -->
                <hr class="separator" />

                <!-- ユーザー名、ランク、キャラクター画像 -->
                <div class="room-user-info">
                  <p class="username">ユーザー名: {{ room.host_username }}</p>
                  <span class="rank-badge">{{ getHostRank(room.host_rank, room.host_mr) }}</span>
                  <span class="character-img">キャラクター画像</span>
                  <button @click="joinRoom(room.id)" class="join-room-button">入室</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 右側のランキング部分 -->
      <Ranking />
    </div>
  </template>

  <script setup>
  import { reactive, ref, computed, defineProps, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  import Navigation from '@/Components/Navigation.vue';
  import Ranking from '@/Components/Ranking.vue';

  const props = defineProps({
    rooms: Array,
    attributes: Array,
  });

  onMounted(() => {
    console.log('Attributes Props:', props.attributes);
  });

  // キャラクターのリスト
  const characters = [
    'リュウ', 'ルーク', 'ジェイミー', '春麗', 'ガイル', 'キンバリー', 'ジュリ', 'ケン',
    'ブランカ', 'ダルシム', 'エドモンド本田', 'DJ', 'マノン', 'マリーザ', 'JP', 'ザンギエフ',
    'リリィ', 'キャミィ', 'ラシード', 'アキ', 'エド', '豪鬼'
  ];

  // フィルターの設定
  const filters = reactive({
    host_rank: [1, 25000],
    host_characters: [],
    attributes: [],
  });

  // フィルタされたルーム一覧
  const filteredRooms = computed(() => {
    return props.rooms.filter(room => {
      const matchHostRank = room.host_rank >= filters.host_rank[0] && room.host_rank <= filters.host_rank[1];
      const hostCharacters = JSON.parse(room.host_characters || '[]');
      const matchCharacters = filters.host_characters.length === 0 || filters.host_characters.some(character => hostCharacters.includes(character));
      const matchAttributes = filters.attributes.length === 0 || filters.attributes.every(attr => room.attributes.some(attribute => attribute.id === attr));
      return matchHostRank && matchCharacters && matchAttributes;
    });
  });

  // ルームに参加する関数
  const joinRoom = (roomId) => {
    router.post(`/rooms/${roomId}/join`, {}, {
      onSuccess: () => {
        router.visit(`/rooms/${roomId}`);
      },
      onError: (errors) => {
        console.log(errors);
      },
    });
  };

  // フィルターを適用する関数
  const applyFilters = () => {
    router.get('/rooms', {
      host_rank: filters.host_rank,
      host_characters: filters.host_characters,
      categories: filters.attributes,
    });
  };

  // フィルターをリセットする関数
  const resetFilters = () => {
    filters.host_rank = [1, 25000];
    filters.host_characters = [];
    filters.attributes = [];
  };

  // JSONかどうかをチェックする関数
  const isJson = (str) => {
    try {
      JSON.parse(str);
      return true;
    } catch (e) {
      return false;
    }
  };

  // フィルターの表示/非表示の管理
  const isFilterVisible = ref(false);
  const toggleFilter = () => {
    isFilterVisible.value = !isFilterVisible.value;
  };

  // ランク範囲のリスト
  const lpRanges = [
    { rank: 'MASTER', min: 25000, color: '#1BF4B9' },
    { rank: 'DIAMOND5', min: 23800, max: 25000, color: '#D85899' },
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

  // 募集ランクの範囲を取得する関数
  function getRankRange(minRank, maxRank) {
    const startRank = lpRanges.find(range => minRank >= range.min && (!range.max || minRank <= range.max));
    const endRank = lpRanges.find(range => maxRank >= range.min && (!range.max || maxRank <= range.max));

    if (maxRank === 25000) {
      return `${startRank.rank} - MASTER`;
    }
    return `${startRank.rank} - ${endRank.rank}`;
  }

  // MASTERランクの範囲を取得する関数
  function getMasterRankRange(minRank, minMR, maxMR) {
    const startRank = lpRanges.find(range => minRank >= range.min && (!range.max || minRank <= range.max));
    if (minRank === 25000) {
      return `MASTER ${minMR}MR - MASTER ${maxMR}MR`;
    }
    return `${startRank.rank} - MASTER, MR: ${maxMR}MR`;
  }

  // ホストランクを取得する関数
  function getHostRank(hostRank, hostMR) {
    const rank = lpRanges.find(range => hostRank >= range.min && (!range.max || hostRank <= range.max));

    if (hostRank === 25000) {
      return `MASTER, MR: ${hostMR}MR`;
    }
    return `${rank.rank}`;
  }
  </script>

  <style scoped>
  .outer-container {
    display: flex;
    height: 100vh;
    overflow: hidden;
  }

  .content-container {
    flex: 1;
    overflow-y: hidden;
    margin-left: 340px;
    margin-right: 50px;
  }

  @media screen and (min-width: 1440px) {
    .content-container {
      margin-right: 100px;
    }
  }

  .title-and-filter-button {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .title {
    font-size: 2em;
    color: #333;
    font-weight: bold;
    margin: 20px;
  }

  .filter-toggle-button {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .filter-toggle-button:hover {
    background-color: #555;
  }

  .filter-container {
    margin: 20px;
    padding: 10px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  .input-group {
    margin-bottom: 20px;
  }

  .filter-button, .reset-button {
    margin-right: 10px;
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .filter-button:hover, .reset-button:hover {
    background-color: #555;
  }

  /* Room list styling */
  .room-list {
    padding: 20px;
    max-height: calc(100vh - 200px);
    overflow-y: scroll;
  }

  .room {
    background-color: #ffffff; /* 背景を白に設定 */
    border-radius: 10px;
    padding: 30px; /* 余白を広く設定 */
    margin-bottom: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    position: relative;
  }

  .room-title {
    font-size: 1.5em;
    margin-bottom: 10px;
  }

  .room-categories {
    margin-bottom: 10px;
  }

  .category-chip {
    background-color: #d1aef2;
    color: #000;
    padding: 5px 10px; /* 内部の余白（padding） */
    border-radius: 10px;
    display: inline-block;
  }

  .room-recruit-info {
    display: flex;
    justify-content: flex-start; /* 左揃えに変更 */
    gap: 20px; /* 募集ランクと募集キャラクター間に余白を追加 */
    margin-bottom: 10px;
  }

  .room-user-info {
    display: flex;
    justify-content: space-between; /* 要素同士の間隔を自動で調整 */
    align-items: center; /* 縦方向で要素を中央揃え */
  }

  .username {
    margin-right: 10px;
    font-weight: bold;
    font-size: 1.1em;
  }

  .rank-badge {
    background-color: #b3d9ff;
    padding: 5px 10px;
    border-radius: 20px;
    margin-right: 10px;
  }

  .character-img {
    background-color: #a6a6a6;
    padding: 5px 10px;
    border-radius: 10px;
  }

  .join-room-button {
    background-color: #6B4EF5;
    color: white;
    padding: 7px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: auto; /* ボタンを右端に寄せる */
  }

  .join-room-button:hover {
    background-color: #937cff;
  }

  .separator {
    border: none;
    border-top: 1px solid #ddd;
    margin: 10px 0;
  }
  </style>
