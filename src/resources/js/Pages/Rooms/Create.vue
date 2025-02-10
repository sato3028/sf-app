<template>
    <Head title="ルーム作成" />
    <div class="outer-container">
        <Navigation :currentRoute="'create'" />
        <div class="content-panel">
            <button class="back-button" @click="goBack">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="back-icon">
                    <path fill="#6b4ef5"
                        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                </svg>
                <span>戻る</span>
            </button>
            <div class="form-container">
                <h2 class="section-title">ユーザー情報</h2>
                <div class="divider"></div>

                <form @submit.prevent="confirmRoom" class="room-form">
                    <div class="input-group">
                        <label for="host_rank">
                            あなたのランク帯:
                            <span :style="{ color: getRankColor(form.host_rank) }" class="rank-display">
                                {{ getRankText(form.host_rank) }}
                            </span>
                        </label>
                        <v-slider v-model="form.host_rank" :max="25000" :min="1" :step="1" hide-details
                            track-color="#d6d0da" thumb-color="#6b4ef5" class="input-field"></v-slider>
                        <p v-if="form.host_rank === 25000" class="mr-display">
                            <span class="text-black">MR:</span><span style="color: #6b4ef5">{{ form.host_mr
                                }}</span><span class="text-black">MR</span>
                        </p>
                        <v-slider v-if="form.host_rank === 25000" v-model="form.host_mr" :max="2500" :min="1000"
                            :step="1" hide-details track-color="#d6d0da" thumb-color="#6b4ef5"
                            class="input-field"></v-slider>
                    </div>

                    <div class="input-group">
                        <div class="character-label">
                            <label for="host_characters">あなたの使用キャラクター</label>
                        </div>
                        <div class="add-character-container">
                            <button class="add-character-button" type="button" @click="showCharacterModal = true">
                                キャラクターを追加
                            </button>
                        </div>
                        <div v-if="selectedCharacters.length" class="selected-characters-list">
                            <div v-for="(char, index) in selectedCharacters" :key="index" class="character-card" :class="{
                                'border-gray': char.type === 'EITHER',
                                'border-orange': char.type === 'MODERN',
                                'border-purple': char.type === 'CLASSIC',
                            }">
                                <img :src="char.image" alt="" class="character-image" />
                                <button class="delete-button" @click="removeCharacter(index)">×</button>
                            </div>
                        </div>
                        <p v-if="formErrors.host_characters" class="error-message">{{ formErrors.host_characters }}</p>
                    </div>

                    <CharacterModal v-if="showCharacterModal" :show="showCharacterModal" :characters="characters"
                        @close="closeCharacterModal" @add="addCharacter" />

                    <h2 class="section-title">募集内容</h2>
                    <div class="divider"></div>

                    <div class="input-group">
                        <label for="title">募集タイトル</label>
                        <input v-model="form.title" type="text" id="title" placeholder="募集タイトル" />
                        <p v-if="formErrors.title" class="error-message">{{ formErrors.title }}</p>
                    </div>

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
                        <v-range-slider v-model="form.rank_range" :max="25000" :min="1"
                            :step="getStep(form.rank_range[0])" hide-details track-color="#d6d0da" thumb-color="#6b4ef5"
                            class="input-field"></v-range-slider>
                        <p v-if="form.rank_range[1] === 25000" class="mr-display">
                            <span class="text-black">MR範囲:</span>{{ form.mr_range[0] }}<span
                                class="text-black">MR</span> - {{ form.mr_range[1] }}<span class="text-black">MR</span>
                        </p>
                        <v-range-slider v-if="form.rank_range[1] === 25000" v-model="form.mr_range" :max="2500"
                            :min="1000" :step="1" hide-details track-color="#d6d0da" thumb-color="#6b4ef5"
                            class="input-field"></v-range-slider>
                            <p v-if="formErrors.rank_range" class="error-message">{{ formErrors.rank_range }}</p>
                    </div>

                    <div class="input-group">
                        <div class="character-label">
                            <label for="requested_characters">募集キャラクター</label>
                        </div>
                        <div class="add-character-container">
                            <button class="add-character-button" type="button"
                                @click="showRequestedCharacterModal = true">
                                募集キャラクターを追加
                            </button>
                        </div>
                        <div v-if="requestedCharacters.length" class="selected-characters-list">
                            <div v-for="(char, index) in requestedCharacters" :key="index" class="character-card"
                                :class="{
                                    'border-gray': char.type === 'EITHER',
                                    'border-orange': char.type === 'MODERN',
                                    'border-purple': char.type === 'CLASSIC',
                                }">
                                <img :src="char.image" alt="" class="character-image" />
                                <button class="delete-button" @click="removeRequestedCharacter(index)">×</button>
                            </div>
                        </div>
                        <p v-if="formErrors.requested_characters" class="error-message">{{ formErrors.requested_characters }}</p>
                    </div>

                    <CharacterModal v-if="showRequestedCharacterModal" :show="showRequestedCharacterModal"
                        :characters="characters" @close="closeRequestedCharacterModal" @add="addRequestedCharacter" />

                    <h2 class="section-title">カテゴリー</h2>
                    <div class="divider"></div>
                    <div class="dropdown" @click="toggleMenu">
                        <span>カテゴリーを選択</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="arrow-icon">
                            <path v-if="!menuOpen"
                                d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                            <path v-else
                                d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z" />
                        </svg>
                    </div>
                    <div v-if="menuOpen" class="checkbox-list">
                        <div v-for="attribute in attributes" :key="attribute.id"
                            :class="['checkbox-item', { selected: form.attributes.includes(attribute.id) }]">
                            <input type="checkbox" :id="'attribute' + attribute.id" v-model="form.attributes"
                                :value="attribute.id">
                            <label :for="'attribute' + attribute.id">{{ attribute.name }}</label>
                        </div>
                    </div>

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
import { router, usePage, Head } from '@inertiajs/vue3';
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

        selectedCharacters.splice(0, selectedCharacters.length, ...(form.host_characters || []));
        selectedCharacters.forEach((char) => {
            char.image = getCharacterImage(char);
        });

        requestedCharacters.splice(0, requestedCharacters.length, ...(form.requested_characters || []));
        requestedCharacters.forEach((char) => {
            char.image = getCharacterImage(char);
        });
    }

    const user = usePage().props.auth.user;
    if (user && user.name) {
        form.host_username = user.name;
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

const selectedCharacters = reactive([]);
const showCharacterModal = ref(false);

const addCharacter = (character) => {
    selectedCharacters.push(character);
    console.log("使用キャラクター:", selectedCharacters);
};

const confirmRoom = () => {
    try {
        form.host_characters = selectedCharacters.map((char) => ({
            name: char.name,
            image: char.image,
            type: char.type,
        }));

        form.requested_characters = requestedCharacters.map((char) => ({
            name: char.name,
            image: char.image,
            type: char.type,
        }));

        if (!validateForm()) {
            console.error("フォームにエラーがあります:", formErrors);
            return;
        }

        sessionStorage.setItem('createForm', JSON.stringify(form));

        router.post(route("rooms.confirm"), form, {
            onSuccess: () => {
                console.log("Confirmページに遷移しました");
            },
            onError: (errors) => {
                console.error("エラーが発生しました:", errors);
            },
        });
    } catch (error) {
        console.error("例外が発生しました:", error);
    }
};

const closeCharacterModal = () => {
    showCharacterModal.value = false;
};

const removeCharacter = (index) => {
    selectedCharacters.splice(index, 1);
};

const requestedCharacters = reactive([]);
const showRequestedCharacterModal = ref(false);

const addRequestedCharacter = (character) => {
    requestedCharacters.push(character);
    console.log("募集キャラクター:", requestedCharacters);
};

const closeRequestedCharacterModal = () => {
    showRequestedCharacterModal.value = false;
};

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
    if (lp === 25000) return 'MASTER';
    const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp <= r.max));
    return range ? range.rank : '';
}

function getRankColor(lp) {
    if (lp === 25000) {
        return '#1BF4B9';
    }
    const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp < r.max));
    return range ? range.color : '#645e69';
}

function getStep(lp) {
    if (lp === 0) return 100;
    const range = lpRanges.find(r => lp >= r.min && (r.max === undefined || lp <= r.max));
    return range ? 1 : 100;
}

const formErrors = reactive({
    host_username: '',
    host_characters: '',
    title: '',
    requested_characters: '',
});

const validateForm = () => {
    formErrors.title = form.title.trim() ? '' : 'この項目を入力してください';
    formErrors.host_characters = form.host_characters.length ? '' : 'この項目を入力してください';
    formErrors.requested_characters = form.requested_characters.length ? '' : 'この項目を入力してください';
    formErrors.rank_range = form.rank_range.length === 2 ? '' : 'この項目を入力してください';

    return !Object.values(formErrors).some(error => error);
};

const menuOpen = ref(false);

const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

const goBack = () => {
    router.get('/rooms');
};

function getCharacterImage(character) {
    const characterName = typeof character === 'object' && character.name ? character.name : character;

    const matchedCharacter = characters.find((char) => char.name === characterName);
    return matchedCharacter ? matchedCharacter.image : '/default-image.jpg';
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
    margin: 20px 0;
}

.input-group label {
    font-size: 17px;
    margin-bottom: 10px;
    display: block;
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
    background-color: transparent;
    transition: background-color 0.2s ease;
}

.checkbox-item.selected {
    background-color: white;
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
    outline: none;
    box-shadow: none;
}

.checkbox-item label {
    font-size: 16px;
    font-weight: bold;
}

.checkbox-item input[type="checkbox"]:checked+label {
    color: #6b4ef5;
}

.checkbox-item input[type="checkbox"]:checked {
    background-color: #6b4ef5;
    border: 1px solid #ccc;
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
    margin-bottom: 5px;
}

.add-character-container {
    margin-bottom: 10px;
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
    width: 100px;
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

.error-message {
    color: red;
    font-size: 14px;
    margin-top: 5px;
}

</style>
