<template>
    <Head title="チャットルーム" />
    <div class="outer-container">
        <Navigation :currentRoute="'show'" />
        <div class="content-panel">
            <div class="main-content">
                <div class="chat-room">
                    <h1 class="room-title">{{ room.title }}</h1>
                    <div class="chat-messages">
                        <div v-for="chat in chats" :key="chat.id"
                            :class="{ 'chat-message': true, 'self': chat.user.name === '自分' }">
                            <div class="chat-bubble">
                                <span class="chat-sender">{{ chat.user.name }}:</span>
                                <span class="chat-text">{{ chat.message }}</span>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="sendMessage" class="chat-form">
                        <input v-model="message" type="text" placeholder="メッセージを入力" />
                        <button type="submit" class="send-button">送信</button>
                    </form>
                </div>
                <div class="right-info">
                    <div class="right-panel">
                        <h2 class="room-info-title">ルーム情報</h2>
                        <h3 class="section-title">ホスト情報</h3>
                        <p class="info"><span class="info-label">ユーザー名:</span> <span class="info-value">{{
                                room.host_username }}</span></p>
                        <p class="info"><span class="info-label">ランク:</span> <span class="info-value">{{
                            getRankText(room.host_rank, room.host_mr) }}</span></p>
                        <p class="info"><span class="info-label">使用キャラクター:</span> <span class="info-value">{{
                            parseCharacters(room.host_characters).join(', ') }}</span></p>
                        <h3 class="section-title">募集情報</h3>
                        <p class="info"><span class="info-label">募集キャラクター:</span> <span class="info-value">{{
                            parseCharacters(room.requested_characters).join(', ') }}</span></p>
                        <p class="info"><span class="info-label">募集ランク範囲:</span> <span class="info-value">{{
                            getRankRangeText(room.min_rank, room.max_rank, room.min_mr, room.max_mr) }}</span></p>
                        <p class="info"><span class="info-label">募集カテゴリー:</span></p>
                        <ul class="info-value">
                            <li v-for="attribute in room.attributes" :key="attribute.id">{{ attribute.name }}</li>
                        </ul>
                        <div class="button-container">
                            <button v-if="isHost" @click="showHostModal = true" class="modal-button">
                                ルームを作成する方法はこちら
                            </button>
                            <button v-else @click="showParticipantModal = true" class="modal-button">
                                ルームに参加する方法はこちら
                            </button>
                        </div>
                    </div>
                    <HostModal v-if="isHost" :show="showHostModal" @close="showHostModal = false" />
                    <ParticipantModal v-else :show="showParticipantModal" @close="showParticipantModal = false" />
                    <div>
                        <button v-if="isHost" @click="deleteRoom"
                            class="action-button full-width danger">ルームを解散</button>
                        <button v-else @click="leaveRoom" class="action-button full-width danger">ルームを退出</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, usePage, Head } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import HostModal from '@/Components/HostModal.vue';
import ParticipantModal from '@/Components/ParticipantModal.vue';

const { room } = usePage().props;
const chats = ref(room.chats || []);
const message = ref('');
const isHost = ref(room.host_id === usePage().props.auth.user.id);
const showHostModal = ref(false);
const showParticipantModal = ref(false);
const alertMessage = usePage().props.alert || null;

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

const getRankText = (lp, mr = null) => {
    const rank = lpRanges.find(r => lp >= r.min && (!r.max || lp <= r.max));
    if (rank && rank.rank === 'MASTER' && mr !== null) {
        return `MR: ${mr}MR`;
    }
    return rank ? rank.rank : 'Unranked';
};

const getRankRangeText = (minRank, maxRank, minMR = null, maxMR = null) => {
    if (minRank === 25000 && maxRank === 25000 && minMR !== null && maxMR !== null) {
        return `MR: ${minMR}MR - MR: ${maxMR}MR`;
    }
    const minRankText = getRankText(minRank, minMR);
    const maxRankText = maxRank === 25000 ? `MR: ${maxMR}MR` : getRankText(maxRank);
    return `${minRankText} - ${maxRankText}`;
};

const sendMessage = () => {
    router.post(`/rooms/${room.id}/chats`, { message: message.value }, {
        preserveScroll: true,
        onSuccess: () => {
            chats.value.push({
                user: { name: '自分' },
                message: message.value,
            });
            message.value = '';
        },
    });
};

const deleteRoom = () => {
    if (confirm('本当にルームを解散しますか？')) {
        router.delete(`/rooms/${room.id}`, {
            onSuccess: () => {
                router.visit('/rooms');
            },
        });
    }
};

const leaveRoom = () => {
    if (confirm('ルームを退出しますか？')) {
        router.post(`/rooms/${room.id}/leave`, {}, {
            onSuccess: () => {
                router.visit('/rooms');
            },
        });
    }
};

onMounted(() => {
    window.Echo.channel(`room.${room.id}`)
        .listen('MessageSent', (event) => {
            chats.value.push({
                user: { name: event.user },
                message: event.message,
            });
        });
    if (alertMessage) {
        alert(alertMessage);
    }
});

function parseCharacters(characters) {
    if (!characters) return [];

    try {
        const parsed = typeof characters === 'string' ? JSON.parse(characters) : characters;

        if (Array.isArray(parsed)) {
            return parsed.map((char) => (typeof char === 'object' && char.name ? char.name : char));
        }

        return [];
    } catch (e) {
        console.error("Error parsing characters:", e);
        return [];
    }
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
    width: 100%;
    height: 100%;
    overflow-y: auto;
    overflow: visible;
    margin-left: 390px;
}

.main-content {
    display: flex;
    height: 100%;
    gap: 100px;
}

.chat-room {
    flex: 3;
    display: flex;
    flex-direction: column;
    padding: 20px;
    background-color: #FFFFFF;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    min-height: 80%;
    overflow-y: auto;
    margin: 50px 0;
}

.room-title {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 20px;
}

.chat-message {
    display: flex;
    margin-bottom: 10px;
}

.chat-message.self {
    justify-content: flex-end;
}

.chat-bubble {
    max-width: 60%;
    padding: 10px;
    border-radius: 20px;
    background-color: #f3f3f3;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chat-message.self .chat-bubble {
    background-color: #FFFFFF;
}

.chat-sender {
    font-weight: bold;
    margin-right: 10px;
}

.chat-text {
    word-wrap: break-word;
}

.chat-form {
    display: flex;
    align-items: center;
    border-top: 1px solid #ddd;
    padding: 10px 0;
}

.chat-form input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    margin-right: 10px;
    outline: none;
}

.chat-form .send-button {
    padding: 10px 20px;
    background-color: #6B4EF5;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}

.chat-form .send-button:hover {
    background-color: #937cff;
}

.right-info {
    flex: 2;
    display: flex;
    flex-direction: column;
    margin: 50px 100px 50px 0;
}

.right-panel {
    padding: 20px;
    background-color: #6B4EF5;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    height: 100%;
    overflow-y: auto;
}

.room-info-title {
    margin-bottom: 20px;
    font-size: 26px;
    color: #fff;
    font-weight: bold;
}

.section-title {
    margin-bottom: 10px;
    font-size: 20px;
    color: #fff;
    font-weight: bold;
}

.info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.info-label {
    color: #ddd;
    min-width: 150px;
}

.info-value {
    color: #fff;
    flex-grow: 1;
    text-align: right;
}

ul.info-value {
    padding: 0;
    list-style-type: none;
}

ul.info-value li {
    text-align: left;
    color: #fff;
}

.action-button {
    display: block;
    width: 100%;
    padding: 10px 20px;
    margin-top: 20px;
    background-color: #FF6B6B;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-align: center;
}

.action-button:hover {
    opacity: 0.9;
}

.full-width {
    width: 100%;
}

.danger {
    background-color: #FF6B6B;
}

.modal-button {
    background-color: white;
    color: #6B4EF5;
    padding: 10px 15px;
    border: 2px solid #6B4EF5;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 20px;
    display: block;
    text-align: center;
    width: auto;
}

.modal-button:hover {
    opacity: 0.9;
}

.button-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}
</style>
