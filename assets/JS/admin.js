document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const rows = document.querySelectorAll("#adminTable tbody tr");

    function filterTable() {

        const search = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value.trim();

        rows.forEach(row => {

            const text = row.innerText.toLowerCase();

            const badge = row.querySelector(".status-badge");
            const rowStatus = badge
                ? badge.dataset.status.trim().toLowerCase()
                : "";

            const matchSearch = text.includes(search);
            const matchStatus = !status || rowStatus === status;

            row.style.display = (matchSearch && matchStatus) ? "" : "none";
        });
    }

    if (searchInput) searchInput.addEventListener("keyup", filterTable);
    if (statusFilter) statusFilter.addEventListener("change", filterTable);

});