<template>
    <teleport to="body">
      <div v-if="show" class="modal-overlay">
        <div class="modal-content">
          <!-- Headerの左揃え -->
          <div class="modal-header">
            <span class="modal-title">名前の設定</span>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateName" class="form-horizontal">
              <!-- 入力フィールド -->
              <input
                v-model="name"
                type="text"
                placeholder="ゲーム内の名前を入力してください"
                required
                class="name-input"
              />
              <!-- 保存ボタン -->
              <button type="submit" class="save-button">保存</button>
            </form>
          </div>
        </div>
      </div>
    </teleport>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    show: Boolean,
  });

  const name = ref('');

  const updateName = () => {
    router.post(route('user.updateName'), { name: name.value }, {
      preserveScroll: true,
      onSuccess: () => {
        location.reload();
      },
    });
  };
  </script>

  <style scoped>
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
  }

  .modal-content {
    background: white;
    width: 500px; /* 横幅を広く調整 */
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    position: relative;
    text-align: center;
  }

  .modal-header {
    background-color: #e0e0e0;
    padding: 10px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    text-align: left; /* 左揃え */
  }

  .modal-title {
    font-size: 1.2em;
    font-weight: bold;
    color: #6b4ef5;
  }

  .modal-body {
    padding: 30px 20px;
  }

  .form-horizontal {
    display: flex;
    align-items: center; /* 入力フィールドとボタンを中央揃え */
    justify-content: space-between; /* フィールドとボタンの間に空間を作成 */
  }

  .name-input {
    flex: 1; /* フィールドを横に広げる */
    padding: 15px;
    margin-right: 15px; /* ボタンとの間に空間を追加 */
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
  }

  .save-button {
    padding: 12px 20px;
    background-color: #6b4ef5;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
  }

  .save-button:hover {
    background-color: #937cff;
  }
  </style>
