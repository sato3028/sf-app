<template>
    <teleport to="body">
      <div v-if="show" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <span class="modal-title">ログイン</span>
            <button class="close-button" @click="closeModal">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="20" height="20">
                <path fill="#6b4ef5" d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
              </svg>
            </button>
          </div>

          <a :href="route('login.google')" class="google-login-btn">
            <img src="/images/web_neutral_sq_SI@2x.png" alt="Google Sign-In" class="google-btn-img" />
          </a>
        </div>
      </div>
    </teleport>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
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
  width: 400px;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  position: relative;
  text-align: center;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #e0e0e0;
  padding: 10px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.modal-title {
  font-size: 1.2em;
  font-weight: bold;
  color: #6b4ef5;
  margin-left: 10px;
}

.close-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.close-button svg {
  width: 25px;
  height: 25px;
}

.google-login-btn {
  display: inline-block;
  margin-top: 50px;
  margin-bottom: 50px;
}

.google-btn-img {
  width: 240px;
  cursor: pointer;
}
</style>
