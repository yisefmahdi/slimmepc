document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll("[data-service-index]");
    const panels = document.querySelectorAll("[data-service-panel]");

    function activateTab(index) {
        tabs.forEach((tab) => {
            tab.classList.toggle("active", tab.getAttribute("data-service-index") === String(index));
        });

        panels.forEach((panel) => {
            const active = panel.getAttribute("data-service-panel") === String(index);

            if (active) {
                panel.classList.remove("hidden");
                panel.classList.remove("content-animation");
                void panel.offsetWidth;
                panel.classList.add("content-animation");
            } else {
                panel.classList.add("hidden");
            }
        });
    }

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            activateTab(tab.getAttribute("data-service-index"));
        });
    });

    document.querySelectorAll(".accordion-trigger").forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const item = trigger.closest(".accordion-item");
            if (!item) return;

            const wasOpen = item.classList.contains("open");
            const container = item.parentElement;

            container.querySelectorAll(".accordion-item").forEach((other) => {
                other.classList.remove("open");
            });

            if (!wasOpen) {
                item.classList.add("open");
            }
        });
    });
});