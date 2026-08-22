document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();

    const body = document.body;

    /* =========================================
       MOBILE MENU + SEARCH OVERLAY
    ========================================= */
    const mobileDrawer = document.getElementById("mobileDrawer");
    const mobileOverlay = document.getElementById("mobileOverlay");
    const openMobileMenu = document.getElementById("openMobileMenu");
    const closeMobileMenu = document.getElementById("closeMobileMenu");
    const openSearch = document.getElementById("openSearch");
    const closeSearch = document.getElementById("closeSearch");
    const searchOverlay = document.getElementById("searchOverlay");
    const searchInput = document.getElementById("searchInput");

    function showMobileMenu() {
        mobileDrawer.classList.add("open");
        mobileOverlay.classList.add("open");
        body.classList.add("menu-open");
    }

    function hideMobileMenu() {
        mobileDrawer.classList.remove("open");
        mobileOverlay.classList.remove("open");
        body.classList.remove("menu-open");
    }

    if (openMobileMenu) openMobileMenu.addEventListener("click", showMobileMenu);
    if (closeMobileMenu) closeMobileMenu.addEventListener("click", hideMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener("click", hideMobileMenu);

    function showSearch() {
        searchOverlay.classList.add("open");
        body.classList.add("menu-open");
        setTimeout(() => searchInput && searchInput.focus(), 200);
    }

    function hideSearch() {
        searchOverlay.classList.remove("open");
        body.classList.remove("menu-open");
    }

    if (openSearch) openSearch.addEventListener("click", showSearch);
    if (closeSearch) closeSearch.addEventListener("click", hideSearch);
    if (searchOverlay) searchOverlay.addEventListener("click", (event) => {
        if (event.target === searchOverlay) hideSearch();
    });

    document.querySelectorAll("[data-accordion]").forEach((button) => {
        button.addEventListener("click", () => {
            const target = document.getElementById(button.dataset.accordion);
            const icon = button.querySelector(".accordion-icon");
            target.classList.toggle("hidden");
            icon.classList.toggle("rotate-180");
        });
    });

    /* =========================================
       SERVICE FAQ ACCORDION
    ========================================= */
    document.querySelectorAll(".faq-toggle").forEach((button) => {
        button.addEventListener("click", () => {
            const item = button.closest(".faq-item");

            document.querySelectorAll(".faq-item").forEach((other) => {
                if (other !== item) other.classList.remove("active");
            });

            item.classList.toggle("active");
        });
    });

    /* =========================================
       SERVICE SPECIALITY VIDEO — play button
       The design overlays sit above the <video>, so the center
       play button triggers playback (native controls also work
       because the overlays are pointer-events-none).
    ========================================= */
    const specialityVideo = document.getElementById("specialityVideo");
    const specialityPlayBtn = document.getElementById("specialityPlayBtn");

    if (specialityVideo && specialityPlayBtn) {
        specialityPlayBtn.addEventListener("click", () => {
            specialityVideo.play();
        });

        specialityVideo.addEventListener("play", () => {
            specialityPlayBtn.style.opacity = "0";
            specialityPlayBtn.style.pointerEvents = "none";
        });

        specialityVideo.addEventListener("pause", () => {
            if (specialityVideo.currentTime > 0 && !specialityVideo.ended) {
                specialityPlayBtn.style.opacity = "1";
                specialityPlayBtn.style.pointerEvents = "auto";
            }
        });
    }

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            hideMobileMenu();
            hideSearch();
        }
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1280) hideMobileMenu();
    });

    /* =========================================
       PROCESS ORBIT — pause on hover
    ========================================= */
    const processContainer = document.getElementById("repairProcess");
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    if (processContainer && !reducedMotion.matches) {
        const stages = processContainer.querySelectorAll(".process-stage");
        const orbit = processContainer.querySelector(".process-orbit");
        const lightOrbit = processContainer.querySelector(".process-light-orbit");

        stages.forEach((stage) => {
            stage.addEventListener("mouseenter", () => {
                if (orbit) orbit.style.animationPlayState = "paused";
                if (lightOrbit) lightOrbit.style.animationPlayState = "paused";
            });

            stage.addEventListener("mouseleave", () => {
                if (orbit) orbit.style.animationPlayState = "running";
                if (lightOrbit) lightOrbit.style.animationPlayState = "running";
            });
        });
    }

    /* =========================================
       SHOP PRODUCTS CAROUSEL
    ========================================= */
    const track = document.getElementById("shopProductsTrack");
    const prevButton = document.getElementById("shopPrev");
    const nextButton = document.getElementById("shopNext");
    const dotsContainer = document.getElementById("shopDots");

    if (track && prevButton && nextButton && dotsContainer) {
        const products = Array.from(track.querySelectorAll(".product-card"));

        let currentPage = 0;
        let productsPerPage = getProductsPerPage();
        let totalPages = getTotalPages();

        function getProductsPerPage() {
            if (window.innerWidth < 640) return 1;
            if (window.innerWidth < 1280) return 2;
            return 4;
        }

        function getTotalPages() {
            return Math.ceil(products.length / productsPerPage);
        }

        function createDots() {
            dotsContainer.innerHTML = "";
            for (let index = 0; index < totalPages; index++) {
                const dot = document.createElement("button");
                dot.type = "button";
                dot.className = "shop-dot";
                dot.setAttribute("aria-label", "Productpagina " + (index + 1));
                dot.addEventListener("click", function () { goToPage(index); });
                dotsContainer.appendChild(dot);
            }
        }

        function goToPage(page) {
            currentPage = Math.max(0, Math.min(page, totalPages - 1));
            const firstProduct = products[0];
            if (!firstProduct) return;

            const cardWidth = firstProduct.getBoundingClientRect().width;
            const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
            const offset = currentPage * productsPerPage * (cardWidth + gap);
            track.style.transform = "translateX(-" + offset + "px)";
            updateNavigation();
        }

        function updateNavigation() {
            dotsContainer.querySelectorAll(".shop-dot").forEach((dot, index) => {
                dot.classList.toggle("active", index === currentPage);
            });
            prevButton.disabled = currentPage === 0;
            nextButton.disabled = currentPage >= totalPages - 1;
        }

        prevButton.addEventListener("click", function () {
            if (currentPage > 0) goToPage(currentPage - 1);
        });

        nextButton.addEventListener("click", function () {
            if (currentPage < totalPages - 1) goToPage(currentPage + 1);
        });

        let resizeTimer;
        window.addEventListener("resize", function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                productsPerPage = getProductsPerPage();
                totalPages = getTotalPages();
                currentPage = 0;
                createDots();
                goToPage(0);
            }, 150);
        });

        createDots();
        goToPage(0);
    }

    /* =========================================
       REVEAL ON SCROLL
    ========================================= */
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll(".reveal").forEach((el) => revealObserver.observe(el));
});

