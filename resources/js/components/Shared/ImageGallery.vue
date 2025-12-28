<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { ChevronLeft, ChevronRight, X } from 'lucide-vue-next';

const props = defineProps<{
  images: string[];
  initialIndex?: number;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const isOpen = ref(true);
const current = ref(props.initialIndex ?? 0);

const next = () => {
  if (current.value < props.images.length - 1) current.value++;
};
const prev = () => {
  if (current.value > 0) current.value--;
};

const close = () => {
  isOpen.value = false;
  emit('close');
};

const onKey = (e: KeyboardEvent) => {
  if (!isOpen.value) return;
  if (e.key === 'Escape') close();
  if (e.key === 'ArrowRight') next();
  if (e.key === 'ArrowLeft') prev();
};

onMounted(() => {
  window.addEventListener('keydown', onKey);
});
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKey);
});
</script>

<template>
  <div v-if="isOpen" class="gallery-overlay" @click.self="close">
    <div class="gallery-content">
      <button class="gallery-close" @click="close"><X class="h-6 w-6"/></button>
      <img :src="props.images[current]" class="gallery-image" />
      <button v-if="current > 0" class="gallery-nav left" @click.stop="prev"><ChevronLeft class="h-8 w-8"/></button>
      <button v-if="current < props.images.length - 1" class="gallery-nav right" @click.stop="next"><ChevronRight class="h-8 w-8"/></button>
      <div class="gallery-dots">
        <span v-for="(img, idx) in props.images" :key="idx"
          :class="['dot', { active: idx === current }]" @click.stop="current = idx"></span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.gallery-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.gallery-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 1rem;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.gallery-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
.gallery-close {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: transparent;
  border: none;
  cursor: pointer;
}
.gallery-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255,255,255,0.8);
  border-radius: 50%;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
}
.gallery-nav.left { left: 0.5rem; }
.gallery-nav.right { right: 0.5rem; }
.gallery-dots {
  position: absolute;
  bottom: 0.5rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 0.3rem;
}
.dot {
  width: 0.8rem;
  height: 0.2rem;
  background: rgba(0,0,0,0.4);
  border-radius: 0.1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.dot.active { background: rgba(0,0,0,0.9); }
</style>
