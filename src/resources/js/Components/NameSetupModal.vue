<template>
    <teleport to="body">
        <div v-if="show" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title">名前の設定</span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="updateName" class="form-horizontal">
                        <input v-model="name" type="text" placeholder="ゲーム内の名前を入力してください" required class="name-input" />
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

const emit = defineEmits(['close']);

const name = ref('');

const updateName = () => {
    router.post(route('user.updateName'), { name: name.value }, {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            router.reload();
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
    width: 500px;
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
    text-align: left;
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
    align-items: center;
    justify-content: space-between;
}

.name-input {
    flex: 1;
    padding: 15px;
    margin-right: 15px;
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
