// SweetAlert2 handler for cart notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('cart-updated', (event) => {
        console.log('Event received:', event);
        
        // Import SweetAlert2 dynamically
        import('/build/assets/app-wArUTsht.js').then(() => {
            if (typeof window.Swal !== 'undefined') {
                window.Swal.fire({
                    text: event[0]?.title || event.title || 'Added to cart',
                    icon: event[0]?.icon || event.icon || 'success',
                    timer: 2000,
                    position: 'top-end',
                    showConfirmButton: false,
                    toast: true,
                    width: '300px',
                    iconColor: '#10b981',
                    background: '#f0f9ff',
                    color: '#1f2937',
                    customClass: {
                        popup: 'small-toast'
                    }
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
