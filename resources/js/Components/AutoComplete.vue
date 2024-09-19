<template>
  <div class="autocomplete">
    <div class="autocomplete-tags">
      <span v-for="(tag, index) in selectedTags" :key="index" class="autocomplete-tag">
        {{ tag }}
        <button @click="removeTag(index)" class="autocomplete-tag-remove">&times;</button>
      </span>
    </div>
    <input
      type="text"
      v-model="query"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
      class="autocomplete-input"
      placeholder="Type to search..."
    />
    <ul v-if="showDropdown && filteredSuggestions.length" class="autocomplete-dropdown">
      <li
        v-if="filteredSuggestions.length"
        @mousedown="selectAll"
        class="autocomplete-item select-all"
      >
        Select All
      </li>
      <li
        v-for="(suggestion, index) in filteredSuggestions"
        :key="index"
        @mousedown="selectSuggestion(suggestion)"
        class="autocomplete-item"
      >
        {{ suggestion }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    suggestions: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      query: '',
      showDropdown: false,
      selectedTags: []
    };
  },
  computed: {
    filteredSuggestions() {
      const queryLower = this.query.toLowerCase();
      return this.suggestions.filter(suggestion =>
        suggestion.toLowerCase().includes(queryLower) &&
        !this.selectedTags.includes(suggestion)
      );
    }
  },
  methods: {
    onInput() {
      this.showDropdown = true;
    },
    onFocus() {
      this.showDropdown = true;
    },
    onBlur() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
    selectSuggestion(suggestion) {
      if (!this.selectedTags.includes(suggestion)) {
        this.selectedTags.push(suggestion);
        this.query = '';
        this.showDropdown = false;
        this.$emit('select', this.selectedTags);
      }
    },
    selectAll() {
      const allSuggestions = this.filteredSuggestions.filter(suggestion => 
        !this.selectedTags.includes(suggestion)
      );
      this.selectedTags.push(...allSuggestions);
      this.query = '';
      this.showDropdown = false;
      this.$emit('select', this.selectedTags);
    },
    removeTag(index) {
      this.selectedTags.splice(index, 1);
      this.$emit('select', this.selectedTags);
    }
  }
};
</script>

<style scoped>
.autocomplete {
  position: relative;
}
.autocomplete-tags {
  display: flex;
  flex-wrap: wrap;
  margin-bottom: 8px;
}
.autocomplete-tag {
  display: flex;
  align-items: center;
  background: #e0e0e0;
  border-radius: 4px;
  padding: 4px 8px;
  margin-right: 4px;
  margin-bottom: 4px;
}
.autocomplete-tag-remove {
  background: transparent;
  border: none;
  cursor: pointer;
  margin-left: 8px;
}
.autocomplete-input {
  width: 100%;
  padding: 8px;
}
.autocomplete-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #fff;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
}
.autocomplete-item {
  padding: 8px;
  cursor: pointer;
}
.autocomplete-item:hover {
  background: #f0f0f0;
}
.select-all {
  font-weight: bold;
}
</style>
