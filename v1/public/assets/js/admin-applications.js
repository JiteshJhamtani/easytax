/**
 * EasyTax - Admin Application Manager Scripts
 */

let table;

$(document).ready(function () {

    // 1. Initialize DataTable
    table = $("#applicationsTable").DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        order: [[0, "desc"]], // Order by App ID descending initially

        // AJAX Config - Sends all Admin dropdown filters to the backend
        ajax: {
            url: "/admin/applications/data",
            data: function (d) {
                d.agent = $("#filterAgent").val();
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

        // Map JSON payload to columns (Note: Added 'agent' column for Admin view)
        columns: [
            { data: "id", name: "id" },
            { data: "agent", name: "agent.name" },
            { data: "service", name: "service.name" },
            { data: "status", name: "status" },
            { data: "payment", name: "payment_status" },
            { data: "amount", name: "amount" },
            { data: "date", name: "created_at" },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-right", // Aligns action buttons to the right
            },
        ],

        // SaaS-style language overrides
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ID, Agent, or Service...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: '<i class="fas fa-chevron-right" style="font-size:0.75rem;"></i>',
                previous: '<i class="fas fa-chevron-left" style="font-size:0.75rem;"></i>',
            },
            processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...',
        }
    });

    // 2. Trigger reload when any Advanced Filter dropdown changes
    $("#filterAgent, #filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo").on("change", function () {
        table.ajax.reload();
    });

    // 3. Reset Filters Button Logic
    $("#resetFilters").click(function() {
        // Reset all dropdowns and date inputs
        $("#filterAgent, #filterService, #filterStatus, #filterPayment, #filterDateFrom, #filterDateTo").val('');

        // Clear global search and reload the table
        table.search('').draw();
    });
});
