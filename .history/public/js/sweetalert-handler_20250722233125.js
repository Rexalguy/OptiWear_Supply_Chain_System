// SweetAlert2 handler for cart notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('cart-updated', (event) => {
        console.log('Event received:', event);
        
        // Load SweetAlert2 from CDN if not already loaded
        if (typeof window.Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = () => showNotification(event);
            script.onerror = () => {
                console.error('Failed to load SweetAlert2');
                alert(event[0]?.title || event.title || 'Added to cart');
            };
            document.head.appendChild(script);
        } else {
            showNotification(event);
        }
    });
    
    function showNotification(event) {
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
            showClass: {
                popup: 'swal2-noanimation'
            },
            hideClass: {
                popup: 'swal2-noanimation'
            },
            customClass: {
                popup: 'small-toast'
            }
        });
    }
});
