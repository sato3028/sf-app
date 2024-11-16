<template>
    <teleport to="body">
      <div v-if="show" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <span class="modal-title">ルームに参加する方法</span>
            <button class="close-button" @click="closeModal">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="20" height="20">
                <path fill="#6b4ef5" d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <ol class="instruction-list">
              <li><span class="highlight">FIGHTINGGROUND</span>を選択</li>
              <li><span class="highlight">ONLINE</span>の欄の<span class="highlight">CUSTOMROOM</span>を選択</li>
              <li><span class="highlight">ルームを検索する</span>を選択</li>
              <li>
                下に表示されている<span class="highlight">検索メニュー</span>に応じたボタンを選択し、
                <span class="highlight">プレイヤーネームからルームを探す</span>を選択
              </li>
              <li>
                <span class="highlight">StreetMatch側のルーム情報</span>に表示されている
                <span class="highlight">ユーザー名</span>を入力し、検索
              </li>
              <li>
                ルームを選択後、<span class="highlight">パスコード</span>に
                <span class="highlight">1111</span>を入力し、ルームに参加する
              </li>
            </ol>
          </div>
        </div>
      </div>
    </teleport>
  </template>

  <script setup>
  const props = defineProps({
    show: Boolean,
  });
  const emit = defineEmits(['close']);
  const closeModal = () => emit('close');
  </script>

  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap');

  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    font-family: "Noto Sans JP", sans-serif;
  }

  .modal-content {
    background: white;
    width: 500px; /* ウィンドウを大きく */
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    position: relative;
    text-align: left;
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #e0e0e0;
    padding: 15px 25px; /* パディングを拡大 */
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
  }

  .modal-title {
    font-size: 1.5em; /* タイトルを大きく */
    font-weight: bold;
    color: #6b4ef5;
  }

  .modal-body {
    padding-left: 30px; /* モーダル内の余白を広げる */
  }

  .instruction-list {
    list-style-type: decimal;
    margin: 30px; /* リストの余白を調整 */
    padding: 0;
    color: #333;
    font-size: 1.3em; /* リストの文字を大きく */
  }

  .instruction-list li {
    position: relative;
    margin-bottom: 15px; /* 各リスト項目の間隔を広げる */
  }

  .instruction-list li::marker {
    font-weight: bold;
  }

  .highlight {
    color: #6b4ef5;
    font-weight: bold;
  }

  .close-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
  }

  .close-button svg {
    width: 35px;
    height: 35px;
  }
  </style>
