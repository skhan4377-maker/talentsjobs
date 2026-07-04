class AutoCompleteWidget {
  constructor({
    inputSelector,
    hiddenSelector,
    listSelector,
    apiUrl,
    minChars = 1,
    multiSelect = false,
    maxSelections = null,
    maxResults = null,
    onSelect = null
  }) {
    this.input = document.querySelector(inputSelector);
    this.hidden = document.querySelector(hiddenSelector);
    this.list = document.querySelector(listSelector);
    this.apiUrl = apiUrl;
    this.minChars = minChars;
    this.multiSelect = multiSelect;
    this.maxSelections = maxSelections;
    this.maxResults = maxResults;
    this.onSelect = onSelect;

    this.cache = new Map();
    this.highlightIndex = -1;
    this.selections = new Map();

    this.init();
  }

  init() {
    this.debouncedFetch = this.debounce(term => this.fetchAndRender(term), 300);
    this.input.addEventListener('input', e => this.handleInput(e));
    this.input.addEventListener('keydown', e => this.handleKeydown(e));
    this.list.addEventListener('click', e => this.handleClick(e));
    document.addEventListener('click', e => this.handleOutsideClick(e));
    window.addEventListener('resize', () => this.positionList());
  }

  handleInput(e) {
    const fullValue = e.target.value;
    // Split by commas, trim, and remove empty strings
    const parts = fullValue.split(',').map(p => p.trim()).filter(p => p);
    // Safely get the last term – default to empty string if parts is empty
    const term = parts.length > 0 ? parts[parts.length - 1] : '';

    // Sync selections with what's currently in the input
    const labelToId = new Map(
      Array.from(this.selections.entries()).map(([id, label]) => [label.toLowerCase(), id])
    );
    const newSelections = new Map();
    parts.forEach(label => {
      const lower = label.toLowerCase();
      if (labelToId.has(lower)) {
        newSelections.set(labelToId.get(lower), label);
      }
    });
    this.selections = newSelections;
    this.updateHidden();

    // Hide list if term is too short
    if (term.length < this.minChars) {
      this.hideList();
      return;
    }

    // Fetch or use cache
    if (this.cache.has(term)) {
      this.renderList(this.cache.get(term));
    } else {
      this.debouncedFetch(term);
    }
  }

  async fetchAndRender(term) {
    try {
      this.showLoading();
      const sep = this.apiUrl.includes('?') ? '&' : '?';
      const url = `${this.apiUrl}${sep}term=${encodeURIComponent(term)}`;
      const res = await fetch(url);
      const data = await res.json();
      this.cache.set(term, data);
      this.renderList(data);
    } catch {
      this.showError();
    }
  }

  renderList(items) {
    const listItems = this.maxResults ? items.slice(0, this.maxResults) : items;
    if (!listItems?.length) {
      this.list.innerHTML = '<li class="p-3 text-gray-500">No results</li>';
    } else {
      this.list.innerHTML = listItems
        .map((item, idx) => {
          const selected = this.selections.has(item.id) ? 'opacity-50' : '';
          return `<li data-id="${item.id}" data-label="${item.value || item.text}" class="text-left p-3 hover:bg-blue-50 cursor-pointer truncate ${idx === this.highlightIndex ? 'bg-blue-100' : ''} ${selected}">${item.value || item.text}</li>`;
        })
        .join('');
    }
    this.highlightIndex = -1;
    this.showList();
    this.positionList();
  }

  handleKeydown(e) {
    const items = Array.from(this.list.querySelectorAll('li'));
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      this.highlightIndex = (this.highlightIndex + 1) % items.length;
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      this.highlightIndex = (this.highlightIndex - 1 + items.length) % items.length;
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (this.highlightIndex > -1) items[this.highlightIndex].click();
    } else if (e.key === 'Escape') {
      this.hideList();
    }

    items.forEach((li, idx) => li.classList.toggle('bg-blue-100', idx === this.highlightIndex));
    items[this.highlightIndex]?.scrollIntoView({ block: 'nearest' });
  }

  handleClick(e) {
    const li = e.target.closest('li');
    if (!li) return;
    const id = li.dataset.id;
    const label = li.dataset.label;

    if (this.multiSelect) {
      if (this.selections.has(id)) return;
      if (this.maxSelections && this.selections.size >= this.maxSelections) return this.flashMessage(`Max ${this.maxSelections} items allowed`);

      this.selections.set(id, label);
      this.updateHidden();
      this.updateInputDisplay(true);
      this.input.focus();
      this.hideList();
    } else {
      this.input.value = label;
      this.hidden.value = id;
      this.hideList();
      this.onSelect?.({ id, label });
    }
  }

  updateInputDisplay(addTrailingComma = false) {
    if (this.multiSelect) {
      const labels = Array.from(this.selections.values());
      this.input.value = labels.join(', ') + (addTrailingComma ? ', ' : '');
    }
  }

  updateHidden() {
    this.hidden.value = this.multiSelect
      ? Array.from(this.selections.keys()).join(',')
      : '';
  }

  showLoading() {
    this.list.innerHTML = '<li class="p-3 text-gray-500">Loading...</li>';
    this.showList();
  }

  showError() {
    this.list.innerHTML = '<li class="p-3 text-red-500">Error loading data</li>';
    this.showList();
  }

  showList() {
    this.list.classList.remove('hidden');
  }

  hideList() {
    this.list.classList.add('hidden');
  }

  positionList() {
    const rect = this.input.getBoundingClientRect();
    this.list.style.width = `${rect.width}px`;
    // If you want to set top/left as well, add:
    // this.list.style.top = rect.bottom + 'px';
    // this.list.style.left = rect.left + 'px';
  }

  handleOutsideClick(e) {
    if (!this.input.contains(e.target) && !this.list.contains(e.target)) {
      this.hideList();
    }
  }

  debounce(fn, delay) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  }

  flashMessage(msg) {
    alert(msg); // Replace with a toast if you have one
  }
}

// Expose globally
window.AutoCompleteWidget = AutoCompleteWidget;