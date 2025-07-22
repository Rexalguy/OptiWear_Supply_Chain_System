// SweetAlert2 handler for cart notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('cart-updated', (event) => {
        console.log('Event received:', event);
        
        // Import SweetAlert2 dynamically
        import('/build/assets/app-wArUTsht.js').then(() => {
            if (typeof window.Swal !== 'undefined') {
                window.Swal.fire({
                    title: event[0]?.title || event.title || 'Cart Updated',
                    icon: event[0]?.icon || event.icon || 'success',
                    timer: 3000,
                    position: 'top-end',
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true
                });
            } else {
                console.error('Swal is not available');
                alert(event[0]?.title || event.title || 'Cart Updated');
            }
        }).catch((error) => {
            console.error('Failed to load SweetAlert2:', error);
            alert(event[0]?.title || event.title || 'Cart Updated');
        });
    });
});
