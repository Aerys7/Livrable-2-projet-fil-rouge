</div>

<footer class="text-center mt-5 mb-4">

<p>Projet Web 2 - Repository JSON</p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

const dateInput = document.getElementById("date_rdv");
const selectHeure = document.getElementById("heure_rdv");

const creneaux = [
    "09:00-10:00",
    "10:00-11:00",
    "11:00-12:00",
    "13:00-14:00",
    "14:00-15:00",
    "15:00-16:00"
];

dateInput.addEventListener("change", function() {

    const date = this.value;

    if (!date) return;

    fetch("get-heures.php?date=" + date)
    .then(response => response.json())
    .then(data => {

        selectHeure.innerHTML = '<option value="">Choisir une plage horaire</option>';

        creneaux.forEach(c => {

            const option = document.createElement("option");

            option.value = c;

            let label = c.replace("-", " - ");

            if (data.includes(c)) {
                option.disabled = true;
                label += " (Complet)";
            }

            option.textContent = label;

            selectHeure.appendChild(option);
        });

    });

});

</script>
</body>
</html>