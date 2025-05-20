// Loading States Handler
const LoadingStates = {

    // Create loading overlay
    createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = ` <div class="loading-spinner"><div class="spinner"></div><div class="loading-text">Loading...</div></div>`;
        return overlay;
    }

    ,

    // Show loading state
    showLoading(element) {
        const overlay = this.createLoadingOverlay();
        element.style.position = 'relative';
        element.appendChild(overlay);
    }

    ,

    // Hide loading state
    hideLoading(element) {
        const overlay = element.querySelector('.loading-overlay');

        if (overlay) {
            overlay.remove();
        }
    }
}

    ;

// Axios Interceptors
axios.interceptors.request.use(config => {
    // Get the target element from the request config
    const targetElement = document.querySelector(config.targetSelector);

    if (targetElement) {
        LoadingStates.showLoading(targetElement);
    }

    return config;
}

);

axios.interceptors.response.use(response => {
    // Get the target element from the response config
    const targetElement = document.querySelector(response.config.targetSelector);

    if (targetElement) {
        LoadingStates.hideLoading(targetElement);
    }

    return response;
}

    ,
    error => {
        // Hide loading on error
        const targetElement = document.querySelector(error.config.targetSelector);

        if (targetElement) {
            LoadingStates.hideLoading(targetElement);
        }

        return Promise.reject(error);
    }

);

// Livewire Loading States
document.addEventListener('livewire:load', () => {
    Livewire.hook('message.sent', (message, component) => {
        const element = document.querySelector(`[wire\\:id="${component.id}"]`);

        if (element) {
            LoadingStates.showLoading(element);
        }
    }

    );

    Livewire.hook('message.processed', (message, component) => {
        const element = document.querySelector(`[wire\\:id="${component.id}"]`);

        if (element) {
            LoadingStates.hideLoading(element);
        }
    }

    );
}

);