// making table headers clickable to sort rows
// because sorting is useful

class SortableTable {
    constructor(tableSelector) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) {
            console.error('Table not found:', tableSelector);
            return;
        }

        this.thead = this.table.querySelector('thead');
        this.tbody = this.table.querySelector('tbody');

        if (!this.thead || !this.tbody) {
            console.error('Table head or body not found');
            return;
        }

        this.currentSort = {
            column: null,
            direction: 'asc'
        };

        this.init();
    }

    init() {
        // grab all the table headers
        const headers = this.thead.querySelectorAll('th');

        headers.forEach((header, index) => {
            const headerText = header.textContent.trim().toLowerCase();

            // Skip certain columns from being sortable
            if (headerText === 'select' || headerText === 'actions' || header.hasAttribute('data-no-sort')) {
                return;
            }

            header.classList.add('sortable');
            header.style.cursor = 'pointer';
            header.setAttribute('data-column-index', index);

            // Add sort icon
            const sortIcon = document.createElement('span');
            sortIcon.className = 'sort-icon';
            sortIcon.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m7 15 5 5 5-5"/>
                    <path d="m7 9 5-5 5 5"/>
                </svg>
            `;
            header.appendChild(sortIcon);

            // Add click event
            header.addEventListener('click', () => this.sortColumn(index));
        });
    }

    sortColumn(columnIndex) {
        const headers = this.thead.querySelectorAll('th');
        const header = headers[columnIndex];
        const rows = Array.from(this.tbody.querySelectorAll('tr'));

        // Determine sort direction
        if (this.currentSort.column === columnIndex) {
            // Toggle direction
            this.currentSort.direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            // clicking a new column, start with A-Z
            this.currentSort.column = columnIndex;
            this.currentSort.direction = 'asc';
        }

        // Update header classes
        headers.forEach(h => {
            h.classList.remove('sort-asc', 'sort-desc');
        });
        header.classList.add(`sort-${this.currentSort.direction}`);

        // Sort rows
        const sortedRows = rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex];
            const cellB = rowB.cells[columnIndex];

            if (!cellA || !cellB) return 0;

            let valueA = cellA.textContent.trim();
            let valueB = cellB.textContent.trim();

            // checking if it's a number to sort correctly
            const numA = parseFloat(valueA.replace(/[^0-9.-]/g, ''));
            const numB = parseFloat(valueB.replace(/[^0-9.-]/g, ''));

            let comparison = 0;

            if (!isNaN(numA) && !isNaN(numB)) {
                // Numeric comparison
                comparison = numA - numB;
            } else {
                // String comparison
                comparison = valueA.localeCompare(valueB, undefined, { numeric: true, sensitivity: 'base' });
            }

            return this.currentSort.direction === 'asc' ? comparison : -comparison;
        });

        // Clear and re-append sorted rows
        this.tbody.innerHTML = '';
        sortedRows.forEach(row => this.tbody.appendChild(row));

        // Show toast notification
        if (typeof showInfoToast === 'function') {
            const headerText = header.textContent.replace(/[\u2191\u2193]/g, '').trim();
            const direction = this.currentSort.direction === 'asc' ? 'ascending' : 'descending';
            showInfoToast(`Sorted by ${headerText} (${direction})`, 2000);
        }
    }
}

// find all tables that should be sortable
document.addEventListener('DOMContentLoaded', function () {
    // Initialize for all tables with data-sortable attribute
    const sortableTables = document.querySelectorAll('table[data-sortable]');

    sortableTables.forEach(table => {
        const tableId = table.id || 'table-' + Math.random().toString(36).substr(2, 9);
        table.id = tableId;
        new SortableTable(`#${tableId}`);
    });

    // Initialize for all tables in .table-card by default
    const tables = document.querySelectorAll('.table-card table:not([data-sortable])');

    tables.forEach(table => {
        const tableId = table.id || 'table-' + Math.random().toString(36).substr(2, 9);
        table.id = tableId;
        new SortableTable(`#${tableId}`);
    });
});
