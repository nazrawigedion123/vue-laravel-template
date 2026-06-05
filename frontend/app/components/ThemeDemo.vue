<template>
  <div class="theme-demo-container">
    <header class="demo-header">
      <h1 class="demo-title">Semantic Design System Demo</h1>
      <p class="demo-subtitle">Using Material-style tokens with dynamic Pinia injection.</p>
    </header>

    <div class="demo-grid">
      <!-- Color Cards -->
      <div class="demo-card primary">
        <h3>Primary</h3>
        <p>Text on primary</p>
      </div>
      <div class="demo-card secondary">
        <h3>Secondary</h3>
        <p>Text on secondary</p>
      </div>
      <div class="demo-card surface">
        <h3>Surface</h3>
        <p>Text on surface</p>
      </div>
      <div class="demo-card error">
        <h3>Error</h3>
        <p>Text on error</p>
      </div>
    </div>

    <div class="demo-typography">
      <h2 class="section-title">Typography Tokens</h2>
      <p class="font-sans">This is Sans-Serif font (Customizable via config)</p>
      <p class="font-serif">This is Serif font (Customizable via config)</p>
      <p class="font-mono">This is Monospace font (Customizable via config)</p>
    </div>

    <div class="demo-controls">
      <button @click="themeStore.toggleTheme" class="btn-toggle">
        Toggle Theme (Current: {{ themeStore.currentTheme }})
      </button>
      <button @click="themeStore.loadRemoteTheme" class="btn-remote">
        Simulate Remote Load
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useThemeStore } from '~/store/themeStore';

const themeStore = useThemeStore();
</script>

<style scoped>
.theme-demo-container {
  padding: 2rem;
  background-color: var(--color-background);
  color: var(--color-on-background);
  font-family: var(--font-sans);
  min-height: 100vh;
  transition: all 0.3s ease;
}

.demo-header {
  margin-bottom: 2rem;
  text-align: center;
}

.demo-title {
  font-family: var(--font-serif);
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.demo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.demo-card {
  padding: 2rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Using Semantic Variables */
.primary {
  background-color: var(--color-primary);
  color: var(--color-on-primary);
}

.secondary {
  background-color: var(--color-secondary);
  color: var(--color-on-secondary);
}

.surface {
  background-color: var(--color-surface);
  color: var(--color-on-surface);
  border: 1px solid rgba(0,0,0,0.1);
}

.error {
  background-color: var(--color-error);
  color: var(--color-on-error);
}

.demo-typography {
  margin-bottom: 3rem;
}

.font-sans { font-family: var(--font-sans); }
.font-serif { font-family: var(--font-serif); font-size: 1.2rem; }
.font-mono { font-family: var(--font-mono); background: rgba(0,0,0,0.05); padding: 0.2rem 0.4rem; }

.demo-controls {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.btn-toggle, .btn-remote {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  transition: opacity 0.2s;
}

.btn-toggle {
  background-color: var(--color-primary);
  color: var(--color-on-primary);
}

.btn-remote {
  background-color: var(--color-secondary);
  color: var(--color-on-secondary);
}

button:hover {
  opacity: 0.9;
}
</style>
