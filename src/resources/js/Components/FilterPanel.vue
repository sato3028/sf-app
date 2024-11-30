<template>
    <div class="filter-panel">
      <h2 class="section-title">フィルター</h2>
      <div class="divider"></div>

      <!-- ホストランク帯 -->
      <div class="input-group">
        <label for="host_rank">
          ルーム作成者のランク帯:
          <span :style="{ color: getRankColor(filters.host_rank[0]) }">{{ getRankText(filters.host_rank[0]) }}</span>
          -
          <span :style="{ color: getRankColor(filters.host_rank[1]) }">{{ getRankText(filters.host_rank[1]) }}</span>
        </label>
        <v-range-slider
          v-model="filters.host_rank"
          :max="25000"
          :min="1"
          :step="1"
          hide-details
          track-color="#d6d0da"
          thumb-color="#6b4ef5"
          class="input-field"
        ></v-range-slider>

        <div v-if="filters.host_rank[1] === 25000" class="mr-slider">
        <p class="mr-display">
          <span class="text-black">ルーム作成者のMR:</span>
          {{ filters.host_mr_range[0] }} - {{ filters.host_mr_range[1] }} MR
        </p>
        <v-range-slider
          v-model="filters.host_mr_range"
          :max="2500"
          :min="1000"
          :step="1"
          hide-details
          track-color="#d6d0da"
          thumb-color="#6b4ef5"
          class="input-field"
        ></v-range-slider>
      </div>
      </div>

      <!-- ホスト使用キャラクター -->
      <div class="input-group">
        <label for="host_characters">ルーム作成者の使用キャラクター</label>
        <button class="add-character-button" @click="showHostCharacterModal = true">キャラクターを追加</button>
        <CharacterModal
          v-if="showHostCharacterModal"
          :show="showHostCharacterModal"
          :characters="characters"
          @close="closeHostCharacterModal"
          @add="addHostCharacter"
        />
        <div class="selected-characters-list">
          <div v-for="(char, index) in selectedHostCharacters" :key="index" class="character-card">
            <img :src="char.image" alt="" class="character-image" />
            <button @click="removeHostCharacter(index)" class="delete-button">×</button>
          </div>
        </div>
      </div>

      <!-- 募集ランク帯 -->
      <div class="input-group">
        <label for="rank_range">
          募集ランク帯:
          <span :style="{ color: getRankColor(filters.rank_range[0]) }">{{ getRankText(filters.rank_range[0]) }}</span>
          -
          <span :style="{ color: getRankColor(filters.rank_range[1]) }">{{ getRankText(filters.rank_range[1]) }}</span>
        </label>
        <v-range-slider
          v-model="filters.rank_range"
          :max="25000"
          :min="1"
          :step="1"
          hide-details
          track-color="#d6d0da"
          thumb-color="#6b4ef5"
          class="input-field"
        ></v-range-slider>

        <div v-if="filters.rank_range[1] === 25000" class="mr-slider">
        <p class="mr-display">
          <span class="text-black">募集 MR 範囲:</span>
          {{ filters.requested_mr_range[0] }} - {{ filters.requested_mr_range[1] }} MR
        </p>
        <v-range-slider
          v-model="filters.requested_mr_range"
          :max="2500"
          :min="1000"
          :step="1"
          hide-details
          track-color="#d6d0da"
          thumb-color="#6b4ef5"
          class="input-field"
        ></v-range-slider>
      </div>
      </div>

      <!-- 募集キャラクター -->
      <div class="input-group">
        <label for="requested_characters">募集キャラクター</label>
        <button class="add-character-button" @click="showRequestedCharacterModal = true">キャラクターを追加</button>
        <CharacterModal
          v-if="showRequestedCharacterModal"
          :show="showRequestedCharacterModal"
          :characters="characters"
          @close="closeRequestedCharacterModal"
          @add="addRequestedCharacter"
        />
        <div class="selected-characters-list">
          <div v-for="(char, index) in selectedRequestedCharacters" :key="index" class="character-card">
            <img :src="char.image" alt="" class="character-image" />
            <button @click="removeRequestedCharacter(index)" class="delete-button">×</button>
          </div>
        </div>
      </div>

      <!-- カテゴリー -->
      <div class="input-group">
        <label for="attributes">カテゴリー</label>
        <div v-if="attributes && attributes.length > 0" class="checkbox-list">
          <div
            v-for="attribute in attributes"
            :key="attribute.id"
            :class="['checkbox-item', { selected: filters.attributes.includes(attribute.id) }]"
          >
            <input type="checkbox" :id="'attribute' + attribute.id" v-model="filters.attributes" :value="attribute.id" />
            <label :for="'attribute' + attribute.id">{{ attribute.name }}</label>
          </div>
        </div>
      </div>

      <!-- フィルター操作 -->
      <div class="input-group">
        <button @click="applyFilters" class="confirm-button">フィルターを適用</button>
        <button @click="resetFilters" class="reset-button">リセット</button>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, reactive } from 'vue';
  import CharacterModal from '@/Components/CharacterModal.vue';

  const props = defineProps({
    characters: Array,
    attributes: Array,
    filters: Object,
  onApplyFilters: Function,
  onResetFilters: Function,
  });

  const filters = reactive({
  host_rank: [1, 25000],
  host_mr_range: [1000, 2500], // ホスト用 MR 範囲
  rank_range: [1, 25000],
  requested_mr_range: [1000, 2500], // 募集用 MR 範囲
  host_characters: [],
  requested_characters: [],
  attributes: [],
});

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
  { rank: 'ROOKIE1', min: 0, max: 200, color: '#00001C' },
];

const getRankColor = (lp) => {
  if (lp === 25000) return '#1BF4B9'; // MASTER color
  const range = lpRanges.find((r) => lp >= r.min && (!r.max || lp <= r.max));
  return range ? range.color : '#645e69'; // Default color if no match
};

const getRankText = (lp) => {
  if (lp === 25000) return 'MASTER';
  const range = lpRanges.find((r) => lp >= r.min && (!r.max || lp <= r.max));
  return range ? range.rank : 'UNKNOWN';
};

  const showHostCharacterModal = ref(false);
  const selectedHostCharacters = reactive([]);
  const addHostCharacter = (character) => selectedHostCharacters.push(character);
  const removeHostCharacter = (index) => selectedHostCharacters.splice(index, 1);
  const closeHostCharacterModal = () => (showHostCharacterModal.value = false);

  const showRequestedCharacterModal = ref(false);
  const selectedRequestedCharacters = reactive([]);
  const addRequestedCharacter = (character) => selectedRequestedCharacters.push(character);
  const removeRequestedCharacter = (index) => selectedRequestedCharacters.splice(index, 1);
  const closeRequestedCharacterModal = () => (showRequestedCharacterModal.value = false);

  const applyFilters = () => {
  props.onApplyFilters(filters);
};

const resetFilters = () => {
  props.onResetFilters();
};
  </script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

.filter-panel {
  background: white;
  padding: 20px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  font-family: "Noto Sans JP", sans-serif;
}

.section-title {
  font-size: 20px;
  font-weight: bold;
  color: #6b4ef5;
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
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 10px;
  display: block;
}

.checkbox-list label {
    margin-bottom: 0px;
}

.input-field {
  width: 95%;
  margin-top: 10px;
  margin-left: auto; /* 左右中央揃え */
  margin-right: auto;
}

.confirm-button, .reset-button {
  padding: 10px;
  background-color: #6b4ef5;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  margin-right: 10px;
}

.confirm-button:hover, .reset-button:hover {
  background-color: #937cff;
}

.selected-characters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.character-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 3px solid transparent;
  border-radius: 8px;
}

.border-gray {
  border-color: gray;
}

.border-orange {
  border-color: orange;
}

.border-purple {
  border-color: purple;
}

.character-image {
  width: 80px;
  aspect-ratio: 2 / 1;
  object-fit: cover;
  border-radius: 4px;
}

.delete-button {
  position: absolute;
  top: 1px;
  right: 1px;
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

.checkbox-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  background-color: transparent;
  transition: background-color 0.2s ease;
}

.checkbox-item.selected {
  background-color: #f4f0fe;
}

.checkbox-item input[type="checkbox"] {
  appearance: none;
  width: 18px;
  height: 18px;
  margin-right: 8px;
  border: 2px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  background-color: transparent;
}

.checkbox-item label {
  font-size: 16px;
  font-weight: bold;
}

.checkbox-item input[type="checkbox"]:checked {
  background-color: #6b4ef5;
  border: 1px solid #ccc;
}

.rank-display {
  font-weight: bold;
  color: #6b4ef5;
}

.mr-display {
  font-weight: bold;
  color: #6b4ef5;
}

.error-message {
  color: red;
  font-size: 14px;
  margin-top: 5px;
}
</style>
