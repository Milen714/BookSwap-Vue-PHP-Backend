(function () {
  const root = document.getElementById("toast-root");

  window.toast = function ({
    title = "",
    description = "",
    type = "info",
    duration = 4000,
  }) {
    if (!root) return;

    const toast = document.createElement("div");
    toast.className = `toast toast-${type} toast-enter`;

    toast.innerHTML = `
      <div class="flex-1">
        ${title ? `<p class="font-semibold">${title}</p>` : ""}
        ${description ? `<p class="text-sm opacity-90">${description}</p>` : ""}
      </div>
      <button
        class="ml-2 text-lg leading-none opacity-60 hover:opacity-100"
        aria-label="Close"
      >
        &times;
      </button>
    `;

    root.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
      toast.classList.remove("toast-enter");
      toast.classList.add("toast-enter-active");
    });

    // Close handler
    const removeToast = () => {
      toast.classList.add("opacity-0", "-translate-y-2");
      setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector("button").addEventListener("click", removeToast);

    // Auto dismiss
    if (duration > 0) {
      setTimeout(removeToast, duration);
    }
  };
})();
