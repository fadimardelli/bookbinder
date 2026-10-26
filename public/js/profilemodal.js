document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("#openEditModal").addEventListener("click", () => {
        document.querySelector(".MyModalEdit").classList.toggle("open");
    });

    document.querySelector(".MyModalEdit #close_modal_btn").addEventListener("click", () => {
        document.querySelector(".MyModalEdit").classList.toggle("open");
    });
});
