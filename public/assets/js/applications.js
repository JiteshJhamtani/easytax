/**
 * EasyTax - Agent Application Manager Scripts
 */

let table;
let currentFilter = "all";

$(document).ready(function () {
    // 1. Check the URL to see what page we are on BEFORE initializing the table
    const urlParams = new URLSearchParams(window.location.search);
    const pageType = urlParams.get('type') || 'other';

    // 2. Define the base columns everyone gets
    let tableColumns = [
        { data: "id", name: "id" },
        { data: "service", name: "service.name" },
        { data: "status", name: "status" },
        { data: "payment", name: "payment_status" },
        { data: "amount", name: "amount" }
        
    ];

    // 3. Inject ITR specific columns ONLY if we are on the ITR page
    if (pageType === 'itr-filing') {
        // ADDED THE TWO NEW COLUMNS HERE
        tableColumns.push({ data: 'ack_no', name: 'ack_no', orderable: false, searchable: false });
        tableColumns.push({ data: 'computation', name: 'computation', orderable: false, searchable: false });
        tableColumns.push({ data: 'balance_sheet', name: 'balance_sheet', orderable: false, searchable: false });
    }

    // 4. Add the final columns that go at the end
    tableColumns.push({ data: "date", name: "created_at" });
    tableColumns.push({
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false,
        className: "text-right",
    });
    // 5. Initialize DataTable
    table = $("#applicationsTable").DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        order: [[0, "desc"]], // Order by App ID descending initially

        // AJAX Config - Sends the current tab filter AND dropdown filters to the backend
        ajax: {
            url: "/agent/applications/data",
            data: function (d) {
                // Pass the type we grabbed at the top of the file
                d.type = pageType;

                // Quick tab filter
                d.filter = currentFilter;

                // Advanced dropdown/date filters
                d.service = $("#filterService").val();
                d.status = $("#filterStatus").val();
                d.payment = $("#filterPayment").val();
                d.date_from = $("#filterDateFrom").val();
                d.date_to = $("#filterDateTo").val();
            },
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
            searchPlaceholder: "Search ID or Service...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: '<i class="fas fa-chevron-right" style="font-size:0.75rem;"></i>',
                previous:
                    '<i class="fas fa-chevron-left" style="font-size:0.75rem;"></i>',
            },
            processing:
                '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...',
        },
    });

    // 6. Quick Tab Click Logic
    $(".app-filter").click(function () {
        // Toggle active visual state
        $(".app-filter").removeClass("active");
        $(this).addClass("active");

        // Update filter variable and reload DataTables
        currentFilter = $(this).data("filter");
        table.ajax.reload();
    });
    // 

    // 7. Trigger reload when any Advanced Filter dropdown changes
    $(
        "#filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo",
    ).on("change", function () {
        table.ajax.reload();
    });

    // 8. Reset Filters Button Logic
    $("#resetFilters").click(function () {
        // Reset dropdowns and dates
        $(
            "#filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo",
        ).val("");

        // Reset Quick Tabs to 'All Applications'
        $(".app-filter").removeClass("active");
        $(".app-filter[data-filter='all']").addClass("active");
        currentFilter = "all";

        // Clear global search and reload the table
        table.search("").draw();
    });
});