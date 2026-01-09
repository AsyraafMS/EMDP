// TOP OF YOUR SCRIPT: Capture initial options
const initialItemOptions = document.querySelector('.item-select')?.innerHTML || '';

// Initialize supplier select
const supplierSelect = new vanillaSelectBox("#selectSupplier", {
    "keepInlineStyles": true,
    "maxHeight": 200,
    "minWidth": 325,
    "search": true,
    "placeHolder": "Please Choose"
});

// Date picker
const currentDate = new Date();
const dueDatePicker = flatpickr(document.getElementById('due'), {
    defaultDate: currentDate.setDate(currentDate.getDate() + 10),
    dateFormat: "Y-m-d"
});

// Calculate row amount
function calculateRowAmount(row) {
    const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const amount = row.querySelector('.amount');
    amount.textContent = (rate * qty).toFixed(2);
    return rate * qty;
}

// Update totals
function updateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-table tbody tr').forEach(row => {
        subtotal += calculateRowAmount(row);
    });
    
    document.querySelector('.subtotal-amount .amount').textContent = subtotal.toFixed(2);
    document.querySelector('.balance-due-amount span:last-child').textContent = subtotal.toFixed(2);
}

// Delete row
function deleteItemRow() {
    document.querySelectorAll('.delete-item').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('tr').remove();
            updateTotals();
        });
    });
}

// Add new item row
document.querySelector('.additem').addEventListener('click', function() {
    const table = document.querySelector('.item-table tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="delete-item-row">
            <ul class="table-controls">
                <li><a href="javascript:void(0);" class="delete-item"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></li>
            </ul>
        </td>
        <td class="description">
            <select class="form-select item-select" name="item_id[]" required>
                <option disabled selected value="">Please Choose</option>
                ${document.querySelector('.item-select').innerHTML}
            </select>
        </td>
        <td class="rate">
            <input type="text" step="0.01" class="form-control form-control-sm item-rate" name="rate[]" placeholder="Price" required>
        </td>
        <td class="text-right qty">
            <input type="text" class="form-control form-control-sm item-qty" name="quantity[]" value="1" placeholder="Quantity" required>
        </td>
        <td class="text-right amount">
            <span class="editable-amount">
                <span class="currency">RM</span> 
                <span class="amount">0.00</span>
            </span>
        </td>
    `;
    
    table.appendChild(newRow);
    deleteItemRow();
    setupRowEvents(newRow);
    updateTotals();
});

// Setup row event listeners
function setupRowEvents(row) {
    // Item selection change
    row.querySelector('.item-select').addEventListener('change', function() {
        const price = itemPrices[this.value] || 0;
        row.querySelector('.item-rate').value = price;
        calculateRowAmount(row);
        updateTotals();
    });
    
    // Rate/quantity change
    row.querySelector('.item-rate').addEventListener('input', () => {
        calculateRowAmount(row);
        updateTotals();
    });
    
    row.querySelector('.item-qty').addEventListener('input', () => {
        calculateRowAmount(row);
        updateTotals();
    });
}

// Initial setup
document.querySelectorAll('.item-table tbody tr').forEach(row => {
    setupRowEvents(row);
    deleteItemRow();
});
updateTotals();

// Set initial rate from selected item
document.querySelectorAll('.item-select').forEach(select => {
    if (select.value) {
        const price = itemPrices[select.value] || 0;
        select.closest('tr').querySelector('.item-rate').value = price;
        calculateRowAmount(select.closest('tr'));
    }
});

