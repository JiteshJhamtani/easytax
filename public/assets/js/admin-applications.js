/**
 * EasyTax - Admin Application Manager Scripts 
 */

let table;

$(document).ready(function () {

    // 1. Check the URL to see what page we are on BEFORE initializing the table
    const urlParams = new URLSearchParams(window.location.search);
    const pageType = urlParams.get('type') || 'other';

    // 2. Define the base columns everyone gets
    let tableColumns = [
        { data: "id", name: "id" },
        { data: "agent", name: "agent.name" }, 
        { data: "service", name: "service.name" },
        { data: 'dynamic_data', name: 'dynamic_data', orderable: false, searchable: true },
        { data: "status", name: "status" },
        { data: "payment", name: "payment_status" }
    ];

    if (window.userRole !== 'SUB-ADMIN') {
        tableColumns.push({ data: "amount", name: "amount", className: "text-right tabular-nums" });
    }

    // 3. Inject ITR specific columns ONLY if we are on the ITR page
    if (pageType === 'itr-filing') {
        tableColumns.push({ data: 'ack_no', name: 'ack_no', orderable: false, searchable: false });
        tableColumns.push({ data: 'computation', name: 'computation', orderable: false, searchable: false }); // NEW COMPUTATION COLUMN
        tableColumns.push({ data: 'balance_sheet', name: 'balance_sheet', orderable: false, searchable: false });
    }

    // 4. Add the final columns that go at the end
    tableColumns.push({ data: "date", name: "created_at" });
    tableColumns.push({ data: "assign_to", name: "assign_to", orderable: false, searchable: false, className: "text-center" });
    tableColumns.push({ data: "actions", name: "actions", orderable: false, searchable: false, className: "text-right text-nowrap" });


   // 5. Initialize DataTable
    table = $("#applicationsTable").DataTable({
        
        processing: true,
        serverSide: true,
        stateSave: true, // Save pagination state
        stateDuration: 60 * 60, // 1 hour
        pageLength: 10,
        // ... rest of your config
        order: [[0, "desc"]], // Order by App ID descending initially

        // AJAX Config - Sends all filters to the backend
        ajax: {
            url: "/admin/applications/data",
            data: function (d) {
                // Pass the type we grabbed at the top of the file
                d.type = pageType;

                // Existing Dropdown Filters
                d.agent = $("#filterAgent").val();
                d.service = $("#filterService").val();
                d.status = $("#filterStatus").val();
                d.payment = $("#filterPayment").val();
                d.date_from = $("#filterDateFrom").val();
                d.date_to = $("#filterDateTo").val();
                d.is_trashed = window.isTrashedView || false;
            }
        },
          
        // Custom DOM string to position elements cleanly using Bootstrap grid
        dom:
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

        // USE OUR DYNAMIC ARRAY HERE
        columns: tableColumns, 

        // SaaS-style language overrides
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ID, Applicant, or Service...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: '<i class="fas fa-chevron-right" style="font-size:0.75rem;"></i>',
                previous: '<i class="fas fa-chevron-left" style="font-size:0.75rem;"></i>',
            },
            processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...',
        }
    });

    // 6. Trigger reload when any Advanced Filter dropdown changes
    $("#filterAgent, #filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo").on("change", function () {
        table.ajax.reload();
    });

    // 7. Reset Filters Button Logic
    $("#resetFilters").click(function() {
        // Reset all dropdowns and date inputs
        $("#filterAgent, #filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo").val('');

        // Clear state and global search, then reload the table
        table.state.clear();
        table.search('').draw();
    });

    // 8. Trash Toggle Logic
    window.isTrashedView = false;
    $('#toggleTrashBtn').click(function() {
        window.isTrashedView = !window.isTrashedView;
        if (window.isTrashedView) {
            $(this).html('<i class="fas fa-arrow-left"></i> Back to Active');
            $(this).css({ 'color': '#2563eb', 'border-color': '#93c5fd' });
        } else {
            $(this).html('<i class="fas fa-trash"></i> View Trash');
            $(this).css({ 'color': '#dc2626', 'border-color': '#fca5a5' });
        }
        table.draw();
    });

}); // <--- THIS IS THE CLOSING BRACKET FOR DOCUMENT.READY. EVERYTHING BELOW IS NOW GLOBAL!


/* ====================================================================
   BALANCE SHEET TYPEFORM GENERATOR LOGIC
==================================================================== */

let tfCurrentStep = 1;
const tfTotalSteps = 6;

// 1. Navigation Logic
function tfGoToStep(step, pushHistory = true) {
    $('.tf-step').removeClass('active');
    $('#step-' + step).addClass('active');
    $('#tf-progress').css('width', ((step / tfTotalSteps) * 100) + '%');
    $('#step-' + step).find('.tf-input').first().focus();
    
    if (pushHistory) {
        history.pushState({ step: step }, '', '#balance-sheet-step-' + step);
    }
}

$(document).on('click', '.tf-next', function() {
    if (tfCurrentStep < tfTotalSteps) { tfCurrentStep++; tfGoToStep(tfCurrentStep); }
});
$(document).on('click', '.tf-prev', function() {
    if (tfCurrentStep > 1) { tfCurrentStep--; tfGoToStep(tfCurrentStep); }
});

// Handle Back Button / History Navigation
$(window).on('popstate', function(e) {
    let hash = window.location.hash;
    if (hash.startsWith('#balance-sheet-step-')) {
        let step = parseInt(hash.replace('#balance-sheet-step-', ''));
        if (!isNaN(step)) {
            tfCurrentStep = step;
            tfGoToStep(step, false);
            if (!$('#balanceSheetModal').is(':visible')) {
                $('#balanceSheetModal').modal('show');
            }
        }
    } else {
        if ($('#balanceSheetModal').is(':visible')) {
            $('#balanceSheetModal').modal('hide');
        }
    }
});

// Handle Manual Modal Close
$(document).ready(function() {
    $('#balanceSheetModal').on('hidden.bs.modal', function () {
        if (window.location.hash.startsWith('#balance-sheet-step-')) {
            history.pushState("", document.title, window.location.pathname + window.location.search);
        }
    });
});

// Press Enter to go next
$(document).on('keypress', '.tf-input', function(e) {
    if (e.which == 13) { e.preventDefault(); $(this).closest('.tf-step').find('.tf-next').click(); }
});

// 2. The Master Math Engine (Runs every time a number is typed)
function runBalanceSheetMath() {
    // Helper to grab number safely
    const val = (name) => parseFloat($(`input[name="${name}"]`).val()) || 0;

    // A. Grab Auto-Filled locked data
    const sales = parseFloat($('#tf-val-sales').val()) || 0;
    const targetNP = parseFloat($('#tf-val-target-np').val()) || 0;
    const otherInc = parseFloat($('#tf-val-other-inc').val()) || 0;

    // B. Trading Math -> Gross Profit
    const gp = (sales + val('closing_stock')) - (val('opening_stock') + val('purchases') + val('direct_expenses'));
    $('#disp-gp').text(gp.toLocaleString('en-IN'));

    // C. P&L Math -> Net Profit
    const totalExp = val('salaries') + val('shop_rent') + val('electricity') + val('other_expenses');
    const np = (gp + otherInc + val('interest_income')) - totalExp;
    $('#disp-np').text(np.toLocaleString('en-IN'));
    
    // NP Color Logic
    if (np === targetNP) {
        $('#np-status').removeClass('text-danger').addClass('text-success');
        $('#disp-np').append(' <i class="fas fa-check-circle"></i>');
    } else {
        $('#np-status').removeClass('text-success').addClass('text-danger');
    }

    // D. Capital Math
    const closingCap = val('opening_capital') + np - val('drawings');
    $('#disp-cap').text(closingCap.toLocaleString('en-IN'));

    // E. Final Tally Math
    const totalLiab = closingCap + val('bank_loan') + val('other_loans') + val('sundry_creditors') + val('other_current_liabilities');
    const totalAssets = val('closing_stock') + val('cash_in_hand') + val('bank_balance') + val('sundry_debtors') + val('furniture') + val('tds');
    
    $('#disp-tot-liab').text(totalLiab.toLocaleString('en-IN'));
    $('#disp-tot-assets').text(totalAssets.toLocaleString('en-IN'));

    // Tally Checker
    const diff = Math.abs(totalLiab - totalAssets);
    $('#disp-diff').text(diff.toLocaleString('en-IN'));

    if (totalLiab === totalAssets && totalLiab > 0) {
        $('#tally-bar').addClass('tally-match');
        $('#tally-icon').removeClass('fa-equals').addClass('fa-check-circle fa-2x');
        $('#tally-text').html('<strong>PERFECTLY BALANCED</strong>');
        $('#btn-generate-pdf').prop('disabled', false).removeClass('btn-dark').addClass('btn-success');
    } else {
        $('#tally-bar').removeClass('tally-match');
        $('#tally-icon').removeClass('fa-check-circle fa-2x').addClass('fa-equals');
        $('#tally-text').html('Difference: ₹<span id="disp-diff">' + diff.toLocaleString('en-IN') + '</span>');
        $('#btn-generate-pdf').prop('disabled', true).removeClass('btn-success').addClass('btn-dark');
    }
}

// Attach math engine to all inputs
$(document).on('input', '.calc-trigger', runBalanceSheetMath);

// 3. The Trigger Function (Call this from your DataTable button)
// THIS MUST BE OUTSIDE document.ready TO WORK WITH HTML ONCLICK
function openBalanceSheetModal(appId, salesAmt, netProfitAmt, otherIncomeAmt) {
    // Reset Modal
    $('#balanceSheetForm')[0].reset();
    tfCurrentStep = 1;
    tfGoToStep(1, true); // pushes state

    // Set Hidden Values
    $('#tf-app-id').val(appId);
    $('#tf-val-sales').val(salesAmt);
    $('#tf-val-target-np').val(netProfitAmt);
    $('#tf-val-other-inc').val(otherIncomeAmt);

    // Set Display Values
    $('#tf-disp-sales').text('₹' + Number(salesAmt).toLocaleString('en-IN'));
    $('#tf-disp-target-np').text('₹' + Number(netProfitAmt).toLocaleString('en-IN'));
    $('#disp-goal-np').text(Number(netProfitAmt).toLocaleString('en-IN'));

    // Run math once to clear it
    runBalanceSheetMath();

    // Show Modal
    $('#balanceSheetModal').modal('show');
}

// Export Logic for Dynamic DataTables
$(document).ready(function() {
    $('.ds-export-btn').on('click', function(e) {
        e.preventDefault();
        
        let exportType = $(this).data('export-type');
        
        // Get all current DataTables parameters (filters, pagination, sort)
        let params = typeof table !== 'undefined' && table && typeof table.ajax.params === 'function' ? (table.ajax.params() || {}) : {};
        
        const urlParams = new URLSearchParams(window.location.search);
        if (!params.type && urlParams.has('type')) {
            params.type = urlParams.get('type');
        }
        if (!params.session && urlParams.has('session')) {
            params.session = urlParams.get('session');
        }
        
        // Add export type flag
        params.export_type = exportType;
        
        // Build query string
        let queryString = $.param(params);
        
        // Use the dynamic export route
        let exportUrl = '/admin/applications/export?' + queryString;
        
        // Native browser navigation will download the file since the server sends a download header.
        window.location.href = exportUrl;
    });
});
