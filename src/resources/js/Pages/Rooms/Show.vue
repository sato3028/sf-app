<template>
    <div class="outer-container">
      <Navigation :currentRoute="'show'" />
      <div class="content-panel">
        <div class="main-content">
          <div class="chat-room">
            <h1 class="room-title">チャット</h1>
            <!-- チャットメッセージの一覧表示 -->
            <div class="chat-messages">
              <div v-for="chat in chats" :key="chat.id" :class="{'chat-message': true, 'self': chat.user.name === '自分'}">
                <div class="chat-bubble">
                  <span class="chat-sender">{{ chat.user.name }}:</span>
                  <span class="chat-text">{{ chat.message }}</span>
                </div>
              </div>
            </div>
            <!-- メッセージ送信フォーム -->
            <form @submit.prevent="sendMessage" class="chat-form">
              <input v-model="message" type="text" placeholder="メッセージを入力" />
              <button type="submit" class="send-button">送信</button>
            </form>
          </div>
          <div class="right-panel">
            <h2 class="room-info-title">ルーム情報</h2>
            <!-- ルーム情報表示 -->
            <h3 class="section-title">ホスト情報</h3>
            <p class="info"><span class="info-label">ユーザー名:</span> <span class="info-value">{{ room.host_username }}</span></p>
            <p class="info"><span class="info-label">ランク:</span> <span class="info-value">{{ getHostRank(room.host_rank, room.host_mr) }}</span></p>
            <p class="info"><span class="info-label">使用キャラクター:</span> <span class="info-value">{{ parseCharacters(room.host_characters).join(', ') }}</span></p>
            <h3 class="section-title">募集情報</h3>
            <p class="info"><span class="info-label">募集キャラクター:</span> <span class="info-value">{{ parseCharacters(room.requested_characters).join(', ') }}</span></p>
            <p class="info"><span class="info-label">募集ランク範囲:</span> <span class="info-value">{{ getRankRange(room.min_rank, room.max_rank) }}</span></p>
            <p class="info"><span class="info-label">募集カテゴリー:</span></p>
            <ul class="info-value">
              <li v-for="attribute in room.attributes" :key="attribute.id">{{ attribute.name }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  import { usePage } from '@inertiajs/vue3';
  import Navigation from '@/Components/Navigation.vue';

  const { room } = usePage().props;
  const chats = ref(room.chats || []);
  const message = ref('');

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

  onMounted(() => {
    window.Echo.channel(`room.${room.id}`)
      .listen('MessageSent', (event) => {
        chats.value.push({
          user: { name: event.user },
          message: event.message,
        });
      });
  });

  function parseCharacters(characters) {
    try {
      return JSON.parse(characters);
    } catch (e) {
      return characters;
    }
  }

  function getHostRank(hostRank, hostMR) {
    if (hostRank === 25000) {
      return `MASTER, MR: ${hostMR}MR`;
    }
    return `${hostRank} LP`;
  }

  function getRankRange(minRank, maxRank) {
    if (maxRank === 25000) {
      return `${minRank} - MASTER`;
    }
    return `${minRank} - ${maxRank} LP`;
  }
  </script>

  <style scoped>
  .outer-container {
    display: flex;
    height: 100vh;
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
    background-color: #f3f3f3; /* 相手のメッセージの背景をグレーがかった白に変更 */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .chat-message.self .chat-bubble {
    background-color: #FFFFFF; /* 自分のメッセージの背景を白に設定 */
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

  .right-panel {
    flex: 2;
    display: flex;
    flex-direction: column;
    padding: 20px;
    background-color: #6B4EF5;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    height: auto;
    margin: 50px 0;
    overflow-y: auto;
    margin-right: 20px;
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
  </style>
