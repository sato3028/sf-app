<template>
    <Head title="ルーム一覧" />
    <div class="outer-container">
        <Navigation :currentRoute="'index'" />

        <div class="content-container">
            <div class="content-panel">
                <div class="title-and-filter-button">
                    <h1 class="title">ルーム一覧</h1>
                    <button @click="toggleFilter" class="filter-toggle-button">
                        {{ isFilterVisible ? 'フィルターを隠す' : 'フィルターを表示' }}
                    </button>
                </div>

                <FilterPanel v-if="isFilterVisible" :filters="filters" :attributes="attributes" :characters="characters"
                    @apply-filters="applyFilters" @reset-filters="resetFilters" />

                <div class="room-list">
                    <div v-if="filteredRooms.length === 0">
                        <p>条件に合うルームが見つかりませんでした。</p>
                    </div>
                    <div v-else>
                        <div v-for="room in filteredRooms" :key="room.id" class="room">
                            <h2 class="room-title">{{ room.title }}</h2>

                            <hr class="separator" />

                            <div class="room-categories">
                                <span v-for="attribute in room.attributes" :key="attribute.id" class="category-chip">
                                    {{ attribute.name }}
                                </span>
                            </div>

                            <div class="room-recruit-info">
                                <p>
                                    募集ランク:
                                    <span v-if="room.max_rank === 25000">
                                        <span
                                            :style="{ color: getMasterRankRange(room.min_rank, room.min_mr, room.max_mr).minColor, fontWeight: 'bold' }">
                                            {{ getMasterRankRange(room.min_rank, room.min_mr, room.max_mr).text.split('- ')[0] }}
                                        </span>
                                        -
                                        <span
                                            :style="{ color: getMasterRankRange(room.min_rank, room.min_mr, room.max_mr).maxColor, fontWeight: 'bold' }">
                                            {{ getMasterRankRange(room.min_rank, room.min_mr, room.max_mr).text.split('- ')[1] }}
                                        </span>
                                    </span>
                                    <span v-else>
                                        <span
                                            :style="{ color: getRankRange(room.min_rank, room.max_rank).minColor, fontWeight: 'bold' }">
                                            {{ getRankRange(room.min_rank, room.max_rank).text.split(' - ')[0] }}
                                        </span>
                                        -
                                        <span
                                            :style="{ color: getRankRange(room.min_rank, room.max_rank).maxColor, fontWeight: 'bold' }">
                                            {{ getRankRange(room.min_rank, room.max_rank).text.split(' - ')[1] }}
                                        </span>
                                    </span>
                                </p>
                                <div class="character-images">
                                    <img v-for="character in parseRequestedCharacters(room.requested_characters)"
                                        :key="character.name" :src="getCharacterImage(character.name)"
                                        :alt="character.name" class="character-image" :class="{
                                            'border-gray': character.type === 'EITHER',
                                            'border-purple': character.type === 'CLASSIC',
                                            'border-orange': character.type === 'MODERN',
                                        }" />
                                </div>
                            </div>

                            <hr class="separator" />

                            <div class="room-user-info">
                                <p class="username">{{ room.host_username }}</p>
                                <span class="rank-badge" :style="getRankBadgeStyle(room.host_rank)">
                                    {{ getHostRank(room.host_rank, room.host_mr) }}
                                </span>
                                <div class="character-images">
                                    <img v-for="character in parseRequestedCharacters(room.host_characters)"
                                        :key="character.name" :src="getCharacterImage(character.name)"
                                        :alt="character.name" class="character-image" :class="{
                                            'border-gray': character.type === 'EITHER',
                                            'border-purple': character.type === 'CLASSIC',
                                            'border-orange': character.type === 'MODERN',
                                        }" />
                                </div>
                                <button @click="joinRoom(room.id)" class="join-room-button">
                                    入室
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Ranking />
        <ModalLogin :show="isModalOpen" @close="isModalOpen = false" />
        <NameSetupModal v-if="isLoggedIn && needsNameSetup" :show="needsNameSetup" @close="needsNameSetup = false" />
    </div>
</template>


<script setup>
import { reactive, ref, computed, defineProps, onMounted } from 'vue';
import { router, usePage, Head } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Ranking from '@/Components/Ranking.vue';
import ModalLogin from '@/Pages/Auth/ModalLogin.vue';
import NameSetupModal from '@/Components/NameSetupModal.vue';
import FilterPanel from '@/Components/FilterPanel.vue';

const isModalOpen = ref(false);

const { props } = usePage();
const rooms = ref(props.rooms || []);
const attributes = props.attributes || [];
const isLoggedIn = ref(props.auth?.user !== null);
const needsNameSetup = ref(props.needsNameSetup);

onMounted(() => {
    console.log("Index.vueで取得したroomsデータ:", rooms.value);
});

function getCharacterImage(characterName) {
    const character = characters.find((char) => char.name === characterName);
    return character ? character.image : '/images/default_icon.jpg';
}

function parseRequestedCharacters(requestedCharacters) {
    try {
        const parsedData = JSON.parse(requestedCharacters);
        return Array.isArray(parsedData) ? parsedData : [];
    } catch (e) {
        console.error('Failed to parse requested characters:', e);
        return [];
    }
}

const updateFilter = (newFilters) => {
    Object.assign(filters, newFilters);
    isFilterApplied.value = true;
};

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

const filters = reactive({
    host_rank: [1, 25000],
    host_mr_range: [1000, 2500],
    rank_range: [1, 25000],
    requested_mr_range: [1000, 2500],
    host_characters: [],
    requested_characters: [],
    attributes: [],
});

const isFilterApplied = ref(false);

const filteredRooms = computed(() => {
    if (!isFilterApplied.value) {
        return rooms.value;
    }
    return rooms.value.filter((room) => {
        const isWithinRank =
            room.host_rank >= filters.host_rank[0] &&
            room.host_rank <= filters.host_rank[1];
        const isWithinMR =
            filters.host_rank[1] !== 25000 ||
            (room.host_mr >= filters.host_mr_range[0] &&
                room.host_mr <= filters.host_mr_range[1]);
        const isWithinCharacters =
            filters.host_characters.length === 0 ||
            filters.host_characters.some((char) => room.host_characters.includes(char));
        const isWithinAttributes =
            filters.attributes.length === 0 ||
            filters.attributes.every((attr) => room.attributes.includes(attr));
        return isWithinRank && isWithinMR && isWithinCharacters && isWithinAttributes;
    });
});

const joinRoom = (roomId) => {
    if (!isLoggedIn.value) {
        console.log('User is not logged in, showing modal.');
        isModalOpen.value = true;
        return;
    }

    router.post(`/rooms/${roomId}/join`, {}, {
        onSuccess: () => {
            router.visit(`/rooms/${roomId}`);
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};

const applyFilters = (newFilters) => {
    console.log("applyFiltersが呼び出されました");

    Object.assign(filters, newFilters);
    console.log("現在のfilters:", filters);

    const sanitizedFilters = {
        host_rank: [...filters.host_rank],
        host_mr_range: [...filters.host_mr_range],
        rank_range: [...filters.rank_range],
        requested_mr_range: [...filters.requested_mr_range],
        host_characters: filters.host_characters.map((char) => char.name),
        requested_characters: filters.requested_characters.map((char) => char.name),
        attributes: [...filters.attributes],
    };
    console.log("Proxy解除後のフィルター:", sanitizedFilters);

    router.get('/rooms', sanitizedFilters, {
        preserveState: true,
        replace: true,
        onSuccess: (response) => {
            console.log("フィルタ結果:", response.props.rooms);
            rooms.value = response.props.rooms;
        },
        onError: (errors) => {
            console.error("フィルタ適用エラー:", errors);
        },
    });
};

const isFilterVisible = ref(false);
const toggleFilter = () => {
    isFilterVisible.value = !isFilterVisible.value;
};

const resetFilters = () => {
    console.log('リセットボタンが押されました');

    Object.assign(filters, {
        host_rank: [1, 25000],
        host_mr_range: [1000, 2500],
        rank_range: [1, 25000],
        requested_mr_range: [1000, 2500],
        host_characters: [],
        requested_characters: [],
        attributes: [],
    });

    isFilterApplied.value = false;

    router.get('/rooms', {}, {
        preserveState: true,
        replace: true,
        onSuccess: (response) => {
            console.log('フィルターリセット後のルーム一覧:', response.props.rooms);
            rooms.value = response.props.rooms;
        },
        onError: (errors) => {
            console.error('フィルターリセットエラー:', errors);
        },
    });
};

const lpRanges = [
    { rank: 'MASTER', min: 25000, color: '#17C89D', bgColor: '#E6FCF6', textColor: '#17C89D' },
    { rank: 'DIAMOND5', min: 23800, max: 25000, color: '#D85899', bgColor: '#FCE7EF', textColor: '#D85899' },
    { rank: 'DIAMOND4', min: 22600, max: 23800, color: '#D85899', bgColor: '#FCE7EF', textColor: '#D85899' },
    { rank: 'DIAMOND3', min: 21400, max: 22600, color: '#D85899', bgColor: '#FCE7EF', textColor: '#D85899' },
    { rank: 'DIAMOND2', min: 20200, max: 21400, color: '#D85899', bgColor: '#FCE7EF', textColor: '#D85899' },
    { rank: 'DIAMOND1', min: 19000, max: 20200, color: '#D85899', bgColor: '#FCE7EF', textColor: '#D85899' },
    { rank: 'PLATINUM5', min: 17800, max: 19000, color: '#16B4E7', bgColor: '#E6F7FC', textColor: '#16B4E7' },
    { rank: 'PLATINUM4', min: 16600, max: 17800, color: '#16B4E7', bgColor: '#E6F7FC', textColor: '#16B4E7' },
    { rank: 'PLATINUM3', min: 15400, max: 16600, color: '#16B4E7', bgColor: '#E6F7FC', textColor: '#16B4E7' },
    { rank: 'PLATINUM2', min: 14200, max: 15400, color: '#16B4E7', bgColor: '#E6F7FC', textColor: '#16B4E7' },
    { rank: 'PLATINUM1', min: 13000, max: 14200, color: '#16B4E7', bgColor: '#E6F7FC', textColor: '#16B4E7' },
    { rank: 'GOLD5', min: 12200, max: 13000, color: '#D7AA4E', bgColor: '#FFF4E0', textColor: '#D7AA4E' },
    { rank: 'GOLD4', min: 11800, max: 12200, color: '#D7AA4E', bgColor: '#FFF4E0', textColor: '#D7AA4E' },
    { rank: 'GOLD3', min: 11400, max: 11800, color: '#D7AA4E', bgColor: '#FFF4E0', textColor: '#D7AA4E' },
    { rank: 'GOLD2', min: 9800, max: 11400, color: '#D7AA4E', bgColor: '#FFF4E0', textColor: '#D7AA4E' },
    { rank: 'GOLD1', min: 9000, max: 9800, color: '#D7AA4E', bgColor: '#FFF4E0', textColor: '#D7AA4E' },
    { rank: 'SILVER5', min: 8200, max: 9000, color: '#333132', bgColor: '#E6E6E6', textColor: '#333132' },
    { rank: 'SILVER4', min: 7400, max: 8200, color: '#333132', bgColor: '#E6E6E6', textColor: '#333132' },
    { rank: 'SILVER3', min: 6600, max: 7400, color: '#333132', bgColor: '#E6E6E6', textColor: '#333132' },
    { rank: 'SILVER2', min: 5800, max: 6600, color: '#333132', bgColor: '#E6E6E6', textColor: '#333132' },
    { rank: 'SILVER1', min: 5000, max: 5800, color: '#333132', bgColor: '#E6E6E6', textColor: '#333132' },
    { rank: 'BRONZE5', min: 4600, max: 5000, color: '#9B754F', bgColor: '#F5EBE1', textColor: '#9B754F' },
    { rank: 'BRONZE4', min: 4200, max: 4600, color: '#9B754F', bgColor: '#F5EBE1', textColor: '#9B754F' },
    { rank: 'BRONZE3', min: 3800, max: 4200, color: '#9B754F', bgColor: '#F5EBE1', textColor: '#9B754F' },
    { rank: 'BRONZE2', min: 3400, max: 3800, color: '#9B754F', bgColor: '#F5EBE1', textColor: '#9B754F' },
    { rank: 'BRONZE1', min: 3000, max: 3400, color: '#9B754F', bgColor: '#F5EBE1', textColor: '#9B754F' },
    { rank: 'IRON5', min: 2600, max: 3000, color: '#2A2A2A', bgColor: '#E6E6E6', textColor: '#2A2A2A' },
    { rank: 'IRON4', min: 2200, max: 2600, color: '#2A2A2A', bgColor: '#E6E6E6', textColor: '#2A2A2A' },
    { rank: 'IRON3', min: 1800, max: 2200, color: '#2A2A2A', bgColor: '#E6E6E6', textColor: '#2A2A2A' },
    { rank: 'IRON2', min: 1400, max: 1800, color: '#2A2A2A', bgColor: '#E6E6E6', textColor: '#2A2A2A' },
    { rank: 'IRON1', min: 1000, max: 1400, color: '#2A2A2A', bgColor: '#E6E6E6', textColor: '#2A2A2A' },
    { rank: 'ROOKIE5', min: 800, max: 1000, color: '#00001C', bgColor: '#D9D9E6', textColor: '#00001C' },
    { rank: 'ROOKIE4', min: 600, max: 800, color: '#00001C', bgColor: '#D9D9E6', textColor: '#00001C' },
    { rank: 'ROOKIE3', min: 400, max: 600, color: '#00001C', bgColor: '#D9D9E6', textColor: '#00001C' },
    { rank: 'ROOKIE2', min: 200, max: 400, color: '#00001C', bgColor: '#D9D9E6', textColor: '#00001C' },
    { rank: 'ROOKIE1', min: 0, max: 200, color: '#00001C', bgColor: '#D9D9E6', textColor: '#00001C' }
];

function getRankRange(minRank, maxRank) {
    const startRank = lpRanges.find(range => minRank >= range.min && (!range.max || minRank <= range.max));
    const endRank = maxRank === 25000
        ? lpRanges.find(range => range.rank === 'MASTER')
        : lpRanges.find(range => maxRank >= range.min && (!range.max || maxRank <= range.max));

    return {
        text: maxRank === 25000 ? `${startRank.rank} - MASTER` : `${startRank.rank} - ${endRank.rank}`,
        minColor: startRank ? startRank.color : '#333132',
        maxColor: endRank ? endRank.color : '#333132'
    };
}

function getMasterRankRange(minRank, minMR, maxMR) {
    const startRank = lpRanges.find(range => minRank >= range.min && (!range.max || minRank <= range.max));
    const masterColor = lpRanges.find(range => range.rank === 'MASTER').color;

    return {
        text: minRank === 25000
            ? `MASTER ${minMR}MR - MASTER ${maxMR}MR`
            : `${startRank.rank} - MASTER MR: ${maxMR}MR`,
        minColor: startRank ? startRank.color : '#333132',
        maxColor: masterColor
    };
}

function getHostRank(hostRank, hostMR) {
    const rank = lpRanges.find(range => hostRank >= range.min && (!range.max || hostRank <= range.max));

    if (hostRank === 25000) {
        return `MASTER MR: ${hostMR}MR`;
    }
    return `${rank.rank}`;
}

function getRankBadgeStyle(rank) {
    const range = lpRanges.find(r => rank >= r.min && (!r.max || rank <= r.max));
    return range ? { backgroundColor: range.bgColor, color: range.textColor } : {};
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

.outer-container {
    display: flex;
    height: 100vh;
    overflow: hidden;

}

.content-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    margin-left: 340px;
    margin-right: 50px;
}

@media screen and (min-width: 1440px) {
    .content-container {
        margin-right: 100px;
    }
}

.content-panel {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
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

.filter-button,
.reset-button {
    margin-right: 10px;
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-button:hover,
.reset-button:hover {
    background-color: #555;
}

.room-list {
    margin-top: 30px;
    flex: 1;
}

.room {
    background-color: #ffffff;
    border-radius: 10px;
    padding: 30px;
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
    padding: 5px 10px;
    border-radius: 10px;
    display: inline-block;
}

.room-recruit-info {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 10px;
}

.room-recruit-info p {
    margin: 0;
    display: flex;
    align-items: center;
    font-size: 18px;
}

.room-user-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.username {
    margin-right: 10px;
    font-weight: bold;
    font-size: 1.1em;
}

.rank-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
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
    margin-left: auto;
}

.join-room-button:hover {
    background-color: #937cff;
}

.separator {
    border: none;
    border-top: 1px solid #ddd;
    margin: 10px 0;
}

.category-chip {
    background-color: #F4F0FE;
    color: #6B4EF5;
    padding: 5px 10px;
    border-radius: 10px;
    display: inline-block;
    margin-right: 5px;
}

.character-images {
    display: flex;
    gap: 10px;
}

.character-image {
    width: 60px;
    height: 30px;
    object-fit: cover;
    border-radius: 5px;
    border: 3px solid transparent;
}

.character-image.border-gray {
    border-color: gray;
}

.character-image.border-purple {
    border-color: purple;
}

.character-image.border-orange {
    border-color: orange;
}
</style>
