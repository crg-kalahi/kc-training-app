<template>
  <div class="relative min-w-[16rem] max-w-[min(100vw-2rem,42rem)]">
    <div
      v-if="trainingId && trainingLabel && !pickerOpen"
      class="flex flex-wrap items-center gap-2 rounded-md border border-gray-300 bg-white px-2 py-1.5 text-sm shadow-sm"
    >
      <span class="min-w-0 flex-1 break-words font-medium text-gray-900">{{ trainingLabel }}</span>
      <button
        type="button"
        class="shrink-0 rounded px-2 py-0.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50"
        @click="openPicker"
      >
        Change
      </button>
      <button
        type="button"
        class="shrink-0 rounded px-2 py-0.5 text-xs font-medium text-gray-600 hover:bg-gray-100"
        @click="$emit('clear')"
      >
        Clear
      </button>
    </div>
    <div v-else class="relative">
      <input
        v-model="localQ"
        type="search"
        autocomplete="off"
        placeholder="Search trainings…"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        @focus="onFocus"
        @input="onInput"
        @blur="onBlur"
      />
      <div
        v-show="pickerOpen"
        class="absolute z-40 mt-1 w-full max-w-[min(100vw-2rem,42rem)] rounded-md border border-gray-200 bg-white shadow-lg"
        @mousedown.prevent
      >
        <div class="max-h-56 overflow-y-auto">
          <p v-if="loading" class="p-3 text-sm text-gray-500">Loading…</p>
          <template v-else>
            <p v-if="!rows.length" class="p-3 text-sm text-gray-500">No trainings found.</p>
            <button
              v-for="row in rows"
              :key="row.id"
              type="button"
              class="block w-full break-words border-b border-gray-50 px-3 py-2 text-left text-sm leading-snug text-gray-800 hover:bg-indigo-50"
              @mousedown.prevent="choose(row)"
            >
              {{ row.title }}
            </button>
          </template>
        </div>
        <div
          v-if="!loading && meta.total > 0"
          class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 bg-gray-50 px-2 py-2 text-xs text-gray-600"
        >
          <button
            type="button"
            class="rounded px-2 py-1 font-medium disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="page <= 1 || loading"
            @mousedown.prevent="goPrev"
          >
            Previous
          </button>
          <span class="tabular-nums">Page {{ page }} / {{ lastPage }}</span>
          <button
            type="button"
            class="rounded px-2 py-1 font-medium disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="page >= lastPage || loading"
            @mousedown.prevent="goNext"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import debounce from 'lodash/debounce';

export default {
  name: 'DashboardTrainingFilter',
  props: {
    trainingId: {
      type: String,
      default: '',
    },
    trainingLabel: {
      type: String,
      default: null,
    },
  },
  emits: ['select', 'clear'],
  data() {
    return {
      pickerOpen: false,
      localQ: '',
      rows: [],
      page: 1,
      lastPage: 1,
      loading: false,
      meta: { total: 0 },
      blurTimer: null,
    };
  },
  watch: {
    trainingId() {
      this.pickerOpen = false;
    },
  },
  created() {
    this.debouncedLoad = debounce(() => {
      this.loadPage(1);
    }, 300);
  },
  methods: {
    onFocus() {
      this.pickerOpen = true;
      this.loadPage(1);
    },
    onInput() {
      this.debouncedLoad();
    },
    onBlur() {
      this.blurTimer = setTimeout(() => {
        this.pickerOpen = false;
      }, 200);
    },
    cancelBlurClose() {
      if (this.blurTimer) {
        clearTimeout(this.blurTimer);
        this.blurTimer = null;
      }
    },
    openPicker() {
      this.cancelBlurClose();
      this.pickerOpen = true;
      this.localQ = '';
      this.loadPage(1);
    },
    async loadPage(page) {
      this.cancelBlurClose();
      this.loading = true;
      this.page = page;
      try {
        const { data } = await window.axios.get(route('dashboard.trainings.search'), {
          params: {
            q: this.localQ,
            page: this.page,
            per_page: 15,
          },
        });
        this.rows = data.data || [];
        const m = data.meta || {};
        this.lastPage = Math.max(m.last_page || 1, 1);
        this.meta = { total: m.total ?? 0 };
      } catch (e) {
        this.rows = [];
        this.lastPage = 1;
        this.meta = { total: 0 };
      } finally {
        this.loading = false;
      }
    },
    goPrev() {
      if (this.page <= 1) return;
      this.loadPage(this.page - 1);
    },
    goNext() {
      if (this.page >= this.lastPage) return;
      this.loadPage(this.page + 1);
    },
    choose(row) {
      this.cancelBlurClose();
      this.pickerOpen = false;
      this.$emit('select', row.id);
    },
  },
};
</script>
