// splits tables into pages so they aren't super long
// all happens in the browser

class TablePagination {
    constructor(tableSelector, options = {}) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) {
            console.error('Table not found:', tableSelector);
            return;
        }

        this.tbody = this.table.querySelector('tbody');
        if (!this.tbody) {
            console.error('Table body not found');
            return;
        }

        this.options = {
            itemsPerPage: options.itemsPerPage || 10,
            paginationContainer: options.paginationContainer || null,
            ...options
        };

        this.currentPage = 1;
        this.allRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.totalPages = Math.ceil(this.allRows.length / this.options.itemsPerPage);

        this.init();
    }

    init() {
        if (this.allRows.length <= this.options.itemsPerPage) {
            // table is short enough, don't need pages
            return;
        }

        this.createPaginationControls();
        this.showPage(1);
    }

    createPaginationControls() {
        const container = this.options.paginationContainer
            ? document.querySelector(this.options.paginationContainer)
            : this.table.parentElement;

        if (!container) return;

        // wrapper around the pagination stuff
        const paginationWrapper = document.createElement('div');
        paginationWrapper.className = 'pagination-wrapper';

        // Create info text
        const paginationInfo = document.createElement('div');
        paginationInfo.className = 'pagination-info';
        paginationInfo.id = `pagination-info-${this.getTableId()}`;
        this.updatePaginationInfo(paginationInfo);

        // buttons container
        const paginationControls = document.createElement('div');
        paginationControls.className = 'pagination-controls';
        paginationControls.id = `pagination-controls-${this.getTableId()}`;

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn pagination-prev';
        prevBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>`;
        prevBtn.addEventListener('click', () => this.previousPage());

        // Page numbers
        const pageNumbers = document.createElement('div');
        pageNumbers.className = 'pagination-numbers';
        pageNumbers.id = `pagination-numbers-${this.getTableId()}`;
        this.updatePageNumbers(pageNumbers);

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn pagination-next';
        nextBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>`;
        nextBtn.addEventListener('click', () => this.nextPage());

        paginationControls.appendChild(prevBtn);
        paginationControls.appendChild(pageNumbers);
        paginationControls.appendChild(nextBtn);

        paginationWrapper.appendChild(paginationInfo);
        paginationWrapper.appendChild(paginationControls);

        // Insert after table
        if (container === this.table.parentElement) {
            this.table.parentElement.insertBefore(paginationWrapper, this.table.nextSibling);
        } else {
            container.appendChild(paginationWrapper);
        }
    }

    updatePageNumbers(container) {
        container.innerHTML = '';

        const maxButtons = 7; // Show max 7 page buttons
        let startPage = Math.max(1, this.currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(this.totalPages, startPage + maxButtons - 1);

        // Adjust start if we're near the end
        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        // First page + ellipsis
        if (startPage > 1) {
            this.createPageButton(container, 1);
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'pagination-ellipsis';
                ellipsis.textContent = '...';
                container.appendChild(ellipsis);
            }
        }

        // Page buttons
        for (let i = startPage; i <= endPage; i++) {
            this.createPageButton(container, i);
        }

        // Last page + ellipsis
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'pagination-ellipsis';
                ellipsis.textContent = '...';
                container.appendChild(ellipsis);
            }
            this.createPageButton(container, this.totalPages);
        }
    }

    createPageButton(container, pageNum) {
        const btn = document.createElement('button');
        btn.className = 'pagination-number';
        btn.textContent = pageNum;

        if (pageNum === this.currentPage) {
            btn.classList.add('active');
        }

        btn.addEventListener('click', () => this.showPage(pageNum));
        container.appendChild(btn);
    }

    updatePaginationInfo(container) {
        const start = (this.currentPage - 1) * this.options.itemsPerPage + 1;
        const end = Math.min(this.currentPage * this.options.itemsPerPage, this.allRows.length);
        container.textContent = `Showing ${start}-${end} of ${this.allRows.length} entries`;
    }

    showPage(pageNum) {
        if (pageNum < 1 || pageNum > this.totalPages) return;

        this.currentPage = pageNum;

        // hide everything first
        this.allRows.forEach(row => {
            row.style.display = 'none';
        });

        // Show rows for current page
        const start = (pageNum - 1) * this.options.itemsPerPage;
        const end = start + this.options.itemsPerPage;
        const visibleRows = this.allRows.slice(start, end);

        visibleRows.forEach(row => {
            row.style.display = '';
        });

        // Update controls
        const infoElement = document.getElementById(`pagination-info-${this.getTableId()}`);
        if (infoElement) {
            this.updatePaginationInfo(infoElement);
        }

        const numbersElement = document.getElementById(`pagination-numbers-${this.getTableId()}`);
        if (numbersElement) {
            this.updatePageNumbers(numbersElement);
        }

        // Scroll to top of table
        this.table.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.showPage(this.currentPage + 1);
        }
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.showPage(this.currentPage - 1);
        }
    }

    getTableId() {
        return this.table.id || 'table-' + Math.random().toString(36).substr(2, 9);
    }

    // Method to refresh pagination after filtering
    refresh() {
        // Get currently visible rows (not filtered out)
        this.allRows = Array.from(this.tbody.querySelectorAll('tr')).filter(row => {
            return row.style.display !== 'none';
        });

        this.totalPages = Math.ceil(this.allRows.length / this.options.itemsPerPage);
        this.currentPage = 1;

        const infoElement = document.getElementById(`pagination-info-${this.getTableId()}`);
        if (infoElement) {
            this.updatePaginationInfo(infoElement);
        }

        const numbersElement = document.getElementById(`pagination-numbers-${this.getTableId()}`);
        if (numbersElement) {
            this.updatePageNumbers(numbersElement);
        }

        this.showPage(1);
    }
}

// look for tables that want pagination and turn it on
document.addEventListener('DOMContentLoaded', function () {
    const tables = document.querySelectorAll('table[data-pagination]');

    tables.forEach(table => {
        const itemsPerPage = parseInt(table.getAttribute('data-items-per-page')) || 10;
        new TablePagination(`#${table.id}` || 'table', { itemsPerPage });
    });
});
