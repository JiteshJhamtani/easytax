document.addEventListener("DOMContentLoaded", function () {
    /* ===============================
       SELECT2 INIT
    =============================== */

    if (window.jQuery && $.fn.select2) {
        $(".form-select-enhanced").select2({
            width: "100%",
            placeholder: function () {
                return $(this).data("placeholder");
            },
        });
    }

    /* ===============================
       AUTO SCROLL TO FIRST ERROR
    =============================== */

    const firstError = document.querySelector(".error");
    if (firstError) {
        firstError.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
    }

    /* ===============================
       INPUT FOCUS ANIMATION
    =============================== */

    document.querySelectorAll(".form-control").forEach(function (input) {
        input.addEventListener("focus", function () {
            this.closest(".form-group")?.classList.add("is-focused");
        });

        input.addEventListener("blur", function () {
            this.closest(".form-group")?.classList.remove("is-focused");
        });
    });

    /* ===============================
       FILE PREVIEW
    =============================== */

    document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener("change", function () {
            const wrapper = this.closest(".form-group");

            let preview = wrapper.querySelector(".file-preview");

            if (!preview) {
                preview = document.createElement("div");
                preview.classList.add("file-preview");
                wrapper.appendChild(preview);
            }

            preview.innerHTML = "";

            if (this.files.length > 0) {
                Array.from(this.files).forEach((file) => {
                    const fileTag = document.createElement("span");
                    fileTag.textContent = file.name;
                    preview.appendChild(fileTag);
                });
            }
        });
    });
});
