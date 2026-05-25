document.addEventListener("DOMContentLoaded", () => {
    // Mobile Navigation Toggle
    const toggleBtn = document.getElementById("mobileNavToggle");
    const mobileNav = document.getElementById("mobileNav");
    const header = document.getElementById("mainHeader");

    toggleBtn.addEventListener("click", function () {
        const isExpanded = toggleBtn.getAttribute("aria-expanded") === "true";

        toggleBtn.setAttribute("aria-expanded", !isExpanded);
        toggleBtn.classList.toggle("is-active");
        mobileNav.classList.toggle("is-active");

        // Prevent body scroll when menu is open
        document.body.style.overflow = isExpanded ? "" : "hidden";
    });

    // Add shadow on scroll for the sticky header
    window.addEventListener("scroll", function () {
        if (window.scrollY > 10) {
            header.classList.add("header--scrolled");
        } else {
            header.classList.remove("header--scrolled");
        }
    });

    // Intersection Observer for subtle scroll animations
    const observerOptions = {
        root: null,
        rootMargin: "0px 0px -50px 0px",
        threshold: 0.1,
    };

    const scrollObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Add the class that triggers the CSS transition
                entry.target.classList.add("is-visible");

                // Unobserve to ensure the animation only plays once for a professional feel
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Attach observer to all elements with the data-reveal attribute
    document.querySelectorAll("[data-reveal]").forEach((element) => {
        scrollObserver.observe(element);
    });
});
