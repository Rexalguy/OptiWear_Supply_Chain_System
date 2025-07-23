// Simple SweetAlert2 loader for Filament
(function() {
    // Load SweetAlert2 dynamically
    function loadSweetAlert() {
        return new Promise((resolve, reject) => {
            if (window.Swal) {
                resolve(window.Swal);
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = () => resolve(window.Swal);
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for Livewire to be ready
        if (typeof Livewire !== 'undefined') {
            setupEventListener();
        } else {
            // If Livewire isn't ready yet, wait for it
            document.addEventListener('livewire:init', setupEventListener);
        }
    });

    function setupEventListener() {
        Livewire.on('cart-updated', async (event) => {
            console.log('Event received:', event);
            
            try {
                const Swal = await loadSweetAlert();
                
                Swal.fire({
                    title: event[0]?.title || event.title || 'Cart Updated',
                    icon: event[0]?.icon || event.icon || 'success',
                    timer: 3000,
                    position: 'top-end',
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            } catch (error) {
                console.error('Failed to load SweetAlert2:', error);
                alert(event[0]?.title || event.title || 'Cart Updated');
            }
        });
    }
})();
