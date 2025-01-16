<template>
    <teleport to="body">
        <div v-if="show" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title">キャラクターを選択</span>
                    <button class="close-button" @click="closeModal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="20" height="20">
                            <path fill="#6b4ef5"
                                d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                        </svg>
                    </button>
                </div>

                <div class="character-grid">
                    <div v-for="char in characters" :key="char.name" class="character-card"
                        :class="{ selected: isSelected(char.name) }" @click="toggleCharacterSelection(char.name)">
                        <div class="character-image" :style="{ backgroundImage: `url(${char.image})` }"></div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button @click="applySelection" class="action-button">追加</button>
                    <button @click="closeModal" class="action-button secondary">閉じる</button>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    show: Boolean,
    characters: Array,
    selectedCharacters: Array,
});

const emit = defineEmits(['close', 'apply']);

const localSelected = ref([...props.selectedCharacters]);

const toggleCharacterSelection = (characterName) => {
    const index = localSelected.value.indexOf(characterName);
    if (index === -1) {
        localSelected.value.push(characterName);
    } else {
        localSelected.value.splice(index, 1);
    }
};

const isSelected = (characterName) => localSelected.value.includes(characterName);

const applySelection = () => {
    const characterSelections = localSelected.value.map((characterName) => {
        const character = props.characters.find((char) => char.name === characterName);
        return {
            name: characterName,
            image: character ? character.image : '/images/default_icon.jpg',
        };
    });
    console.log("Selected characters to apply:", characterSelections);
    emit("apply", characterSelections);
    closeModal();
};

onMounted(() => {
    console.log("Characters in FilterCharacterModal:", props.characters);
    console.log("Initial selectedCharacters in Modal:", localSelected.value);
});

const closeModal = () => {
    emit("close");
};
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
    color: #333;
}

.modal-content {
    background: white;
    width: 60%;
    max-height: 80vh;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow-y: auto;
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
}

.close-button {
    background: none;
    border: none;
    cursor: pointer;
}

.close-button svg {
    width: 25px;
    height: 25px;
}

.character-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin: 20px;
}

.character-card {
    cursor: pointer;
    padding: 5px;
    border: 2px solid transparent;
    border-radius: 8px;
    transition: border-color 0.2s;
}

.character-card:hover {
    border-color: #937cff;
}

.character-card.selected {
    border-color: #6b4ef5;
}

.character-image {
    width: 100%;
    padding-top: 40%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 8px;
}

.type-selection {
    margin-top: 10px;
    margin-left: 40px;
    display: flex;
    justify-content: left;
    gap: 10px;
}

.type-selection h3 {
    font-size: 18px;
}

.type-label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 18px;
    cursor: pointer;
    color: #333;
    font-weight: normal;
}

.type-label input[type="radio"] {
    appearance: none;
    width: 16px;
    height: 16px;
    border: 2px solid #ccc;
    border-radius: 50%;
    outline: none;
    cursor: pointer;
    background-color: transparent;
    position: relative;
}

.type-label input[type="radio"]:focus {
    outline: none;
    box-shadow: none;
}

.type-label input[type="radio"]:checked {
    background-color: #6b4ef5;
    border-color: #6b4ef5;
}

.type-label input[type="radio"]:checked~.radio-text {
    color: #6b4ef5;
    font-weight: bold;
}

.radio-text {
    transition: color 0.2s, font-weight 0.2s;
}

.modal-actions {
    margin: 20px;
    display: flex;
    justify-content: space-between;
}

.action-button {
    padding: 10px 40px;
    border: none;
    border-radius: 8px;
    background-color: #6b4ef5;
    color: white;
    cursor: pointer;
}

.action-button.secondary {
    background-color: #e0e0e0;
    color: #333;
}

.action-button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
</style>
